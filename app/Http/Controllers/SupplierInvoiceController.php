<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\SupplierInvoice;
use App\Models\SupplierOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
            ->get(['id', 'nome', 'nif']);

        $supplierOrders = SupplierOrder::with('supplier')
            ->where('estado', 'fechado')
            ->orderBy('numero', 'desc')
            ->get();

        return Inertia::render('SupplierInvoice/Create', [
            'suppliers' => $suppliers,
            'supplierOrders' => $supplierOrders
        ]);
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
            $path = $request->file('documento')->store('supplier_invoices/documents', 'private');
            $validated['documento'] = $path;
        }

        SupplierInvoice::create($validated);

        return redirect()->route('supplier-invoices.index')
            ->with('success', 'Fatura criada com sucesso');
    }
}
