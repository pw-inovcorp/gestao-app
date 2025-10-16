<?php

namespace App\Http\Controllers;

use App\Models\SupplierOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupplierOrderController extends Controller
{
    //
    public function index(Request $request)
    {
        $supplierOrders = SupplierOrder::with(['supplier', 'items', 'order'])
            ->orderBy('numero', 'desc')
            ->paginate(10);

        return Inertia::render('SupplierOrder/Index', [
            'supplierOrders' => $supplierOrders
        ]);
    }

    public function show(SupplierOrder $supplierOrder)
    {
        $supplierOrder->load([
            'supplier',
            'order.client',
            'items.article.ivaRate'
        ]);

        return Inertia::render('SupplierOrder/Show', [
            'supplierOrder' => $supplierOrder
        ]);
    }

    public function updateStatus(Request $request, SupplierOrder $supplierOrder)
    {
        $validated = $request->validate([
            'estado' => ['required', 'in:rascunho,fechado']
        ]);

        $supplierOrder->update([
            'estado' => $validated['estado']
        ]);

        return back()->with('success', "Estado alterado para {$validated['estado']} com sucesso!");
    }

    public function destroy(SupplierOrder $supplierOrder)
    {
        if ($supplierOrder->estado !== 'rascunho') {
            return back()->with('error', 'Apenas encomendas em rascunho podem ser eliminadas.');
        }

        $numero = $supplierOrder->numero;

        $supplierOrder->delete();

        return redirect()->route('supplier-orders.index')
            ->with('success', "Encomenda {$numero} eliminada com sucesso");
    }

    public function downloadPdf(SupplierOrder $supplierOrder)
    {
        $supplierOrder->load([
            'supplier',
            'items.article.ivaRate'
        ]);

        $totalWithoutIva = $supplierOrder->items->sum('subtotal');

        $totalIva = $supplierOrder->items->sum(function ($item) {
            return $item->subtotal * (($item->article->ivaRate->taxa ?? 0) / 100);
        });

        $totalWithIva = $totalWithoutIva + $totalIva;

        $pdf = Pdf::loadView('supplierOrder.pdf', [
            'supplierOrder' => $supplierOrder,
            'totalWithoutIva' => $totalWithoutIva,
            'totalIva' => $totalIva,
            'totalWithIva' => $totalWithIva,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("encomenda-fornecedor-{$supplierOrder->numero}.pdf");
    }
}
