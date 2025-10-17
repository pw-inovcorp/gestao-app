<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\SupplierInvoice;
use App\Models\SupplierOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SupplierInvoiceController extends Controller
{
    //
    public function index(Request $request)
    {
        $invoices = SupplierInvoice::with(['supplier', 'supplierOrder'])
            ->orderBy('data_fatura', 'desc')
            ->paginate(10);

        return Inertia::render('SupplierInvoice/Index', [
            'invoices' => $invoices
        ]);
    }

    public function create()
    {
        $suppliers = Entity::where('is_fornecedor', true)
            ->where('estado', 'ativo')
            ->orderBy('nome')
            ->get(['id', 'nome', 'nif', 'email', 'telefone']);

        $supplierOrders = SupplierOrder::with('supplier')
            ->where('estado', 'fechado')
            ->orderBy('numero', 'desc')
            ->get();

        return Inertia::render('SupplierInvoice/Create', [
            'suppliers' => $suppliers,
            'supplierOrders' => $supplierOrders
        ]);
    }

    public function show(SupplierInvoice $supplierInvoice)
    {
        $supplierInvoice->load(['supplier', 'supplierOrder']);

        return Inertia::render('SupplierInvoice/Show', [
            'invoice' => $supplierInvoice
        ]);
    }

    public function destroy(SupplierInvoice $supplierInvoice)
    {
        if ($supplierInvoice->estado !== 'pendente_pagamento') {
            return back()->with('error', 'Apenas faturas pendentes podem ser eliminadas.');
        }

        if ($supplierInvoice->documento && Storage::disk('local')->exists($supplierInvoice->documento)) {
            Storage::disk('local')->delete($supplierInvoice->documento);
        }

        if ($supplierInvoice->comprovativo_pagamento && Storage::disk('local')->exists($supplierInvoice->comprovativo_pagamento)) {
            Storage::disk('local')->delete($supplierInvoice->comprovativo_pagamento);
        }

        $numero = $supplierInvoice->numero;
        $supplierInvoice->delete();

        return redirect()->route('supplier-invoices.index')
            ->with('success', "Fatura {$numero} eliminada com sucesso");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fornecedor_id' => ['required', 'exists:entities,id'],
            'supplier_order_id' => ['nullable', 'exists:supplier_orders,id'],
            'data_fatura' => ['required', 'date'],
            'data_vencimento' => ['required', 'date', 'after_or_equal:data_fatura'],
            'valor_total' => ['required', 'numeric', 'min:0'],
            'documento' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ], [
            'fornecedor_id.required' => 'Deve selecionar um fornecedor.',
            'data_vencimento.after_or_equal' => 'Data de vencimento deve ser igual ou posterior à data da fatura.',
            'documento.max' => 'Documento não pode ter mais de 5MB.',
        ]);


        if ($request->hasFile('documento')) {
            $path = $request->file('documento')->store('supplier_invoices/documents', 'local');
            $validated['documento'] = $path;
        }

        SupplierInvoice::create($validated);

        return redirect()->route('supplier-invoices.index')
            ->with('success', 'Fatura criada com sucesso');
    }

    public function updateStatus(Request $request, SupplierInvoice $supplierInvoice)
    {
        $validated = $request->validate([
            'estado' => ['required', 'in:pendente_pagamento,paga']
        ]);

        $supplierInvoice->update([
            'estado' => $validated['estado'],
            'data_pagamento' => $validated['estado'] === 'paga' ? now() : null,
            'comprovativo_pagamento' => $validated['estado'] === 'paga' ? $supplierInvoice->comprovativo_pagamento : null
        ]);

        return back()->with('success', "Estado alterado para {$validated['estado']} com sucesso!");
    }

    public function uploadPaymentProof(Request $request, SupplierInvoice $supplierInvoice)
    {
        $validated = $request->validate([
            'comprovativo_pagamento' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $path = $request->file('comprovativo_pagamento')->store('supplier_invoices/payment_proofs', 'local');

        $supplierInvoice->update([
            'comprovativo_pagamento' => $path
        ]);


        $supplier = $supplierInvoice->supplier;

        if ($supplier && $supplier->email) {
            try {
                Mail::send('email.payment-proof', [
                    'invoice' => $supplierInvoice,
                    'supplier' => $supplier
                ], function ($message) use ($supplier, $supplierInvoice, $path) {
                    $message->to($supplier->email)
                        ->subject("Comprovativo de Pagamento - Fatura {$supplierInvoice->numero}");

                    $message->attach(Storage::disk('local')->path($path));
                });

                return back()->with('success', 'Comprovativo guardado e email enviado com sucesso!');
            } catch (\Exception $e) {
                return back()->with('success', 'Comprovativo guardado, mas erro ao enviar email: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Comprovativo guardado! (Fornecedor sem email)');
    }

    public function downloadFile(SupplierInvoice $supplierInvoice, $type)
    {
        $field = $type === 'documento' ? 'documento' : 'comprovativo_pagamento';
        $path = $supplierInvoice->$field;

        if (!$path || !Storage::disk('local')->exists($path)) {
            abort(404, 'Ficheiro não encontrado');
        }

        return Storage::disk('local')->download($path);
    }
}
