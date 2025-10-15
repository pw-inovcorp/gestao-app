<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Entity;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Proposal;
use App\Models\ProposalItem;
use App\Models\SupplierOrder;
use App\Models\SupplierOrderItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class OrderController extends Controller
{
    //
    public function index(Request $request)
    {
        $orders = Order::with(['client', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Order/Index', [
            'orders' => $orders
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['client', 'items.article.ivaRate', 'items.supplier', 'proposal']);

        return Inertia::render('Order/Show', [
            'order' => $order
        ]);
    }

    public function create()
    {

        $clients = Entity::where('is_cliente', true)
            ->where('estado', 'ativo')
            ->orderBy('nome')
            ->get(['id', 'nome', 'nif']);

        $articles = Article::where('estado', 'ativo')
            ->with('ivaRate')
            ->orderBy('nome')
            ->get();

        $suppliers = Entity::where('is_fornecedor', true)
            ->where('estado', 'ativo')
            ->orderBy('nome')
            ->get(['id', 'nome', 'nif']);

        return Inertia::render('Order/Create', [
            'clients' => $clients,
            'articles' => $articles,
            'suppliers' => $suppliers
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => ['required', 'exists:entities,id'],
            'estado' => ['required', Rule::in(['rascunho', 'fechado'])],
            'items' => ['required', 'array', 'min:1'],
            'items.*.article_id' => ['required', 'exists:articles,id'],
            'items.*.fornecedor_id' => ['nullable', 'exists:entities,id'],
            'items.*.quantidade' => ['required', 'integer', 'min:1'],
            'items.*.preco_unitario' => ['required', 'numeric', 'min:0'],
            'items.*.preco_custo' => ['nullable', 'numeric', 'min:0'],
        ], [
            'cliente_id.required' => 'Deve selecionar um cliente.',
            'cliente_id.exists' => 'Cliente inválido.',
            'items.required' => 'Deve adicionar pelo menos um artigo.',
            'items.min' => 'Deve adicionar pelo menos um artigo.',
        ]);

        $order = Order::create([
            'cliente_id' => $validated['cliente_id'],
            'estado' => $validated['estado'],
        ]);

        foreach ($validated['items'] as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'article_id' => $item['article_id'],
                'fornecedor_id' => $item['fornecedor_id'] ?? null,
                'quantidade' => $item['quantidade'],
                'preco_unitario' => $item['preco_unitario'],
                'preco_custo' => $item['preco_custo'] ?? null,
            ]);
        }

        $order->calculateTotalValue();

        return redirect()->route('orders.index')
            ->with('success', 'Encomenda criada com sucesso');
    }

    public function edit(Order $order)
    {

        if ($order->estado !== 'rascunho') {
            return redirect()->route('orders.index')
                ->with('error', 'Apenas encomendas em rascunho podem ser editadas.');
        }

        $order->load(['items.article.ivaRate', 'items.supplier']);

        $clients = Entity::where('is_cliente', true)
            ->where('estado', 'ativo')
            ->orderBy('nome')
            ->get(['id', 'nome', 'nif']);

        $articles = Article::where('estado', 'ativo')
            ->with('ivaRate')
            ->orderBy('nome')
            ->get();

        $suppliers = Entity::where('is_fornecedor', true)
            ->where('estado', 'ativo')
            ->orderBy('nome')
            ->get(['id', 'nome', 'nif']);

        return Inertia::render('Order/Edit', [
            'order' => $order,
            'clients' => $clients,
            'articles' => $articles,
            'suppliers' => $suppliers
        ]);
    }

    // Update
    public function update(Request $request, Order $order)
    {

        if ($order->estado !== 'rascunho') {
            return redirect()->route('orders.index')
                ->with('error', 'Apenas encomendas em rascunho podem ser editadas.');
        }

        $validated = $request->validate([
            'cliente_id' => ['required', 'exists:entities,id'],
            'estado' => ['required', Rule::in(['rascunho', 'fechado'])],
            'items' => ['required', 'array', 'min:1'],
            'items.*.article_id' => ['required', 'exists:articles,id'],
            'items.*.fornecedor_id' => ['nullable', 'exists:entities,id'],
            'items.*.quantidade' => ['required', 'integer', 'min:1'],
            'items.*.preco_unitario' => ['required', 'numeric', 'min:0'],
            'items.*.preco_custo' => ['nullable', 'numeric', 'min:0'],
        ]);

        $order->update([
            'cliente_id' => $validated['cliente_id'],
            'estado' => $validated['estado'],
        ]);


        $order->items()->delete();

        foreach ($validated['items'] as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'article_id' => $item['article_id'],
                'fornecedor_id' => $item['fornecedor_id'] ?? null,
                'quantidade' => $item['quantidade'],
                'preco_unitario' => $item['preco_unitario'],
                'preco_custo' => $item['preco_custo'] ?? null,
            ]);
        }

        $order->calculateTotalValue();

        return redirect()->route('orders.index')
            ->with('success', 'Encomenda atualizada com sucesso');
    }

    public function destroy(Order $order)
    {
        if ($order->estado !== 'rascunho') {
            return back()->with('error', 'Apenas encomendas em rascunho podem ser eliminadas.');
        }

        $numero = $order->numero;

        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', "Encomenda {$numero} eliminada com sucesso");
    }

    public function downloadPdf(Order $order)
    {
        $order->load([
            'client',
            'proposal',
            'items.article.ivaRate',
            'items.supplier'
        ]);

        $totalWithoutIva = $order->items->sum('subtotal');

        $totalIva = $order->items->sum(function ($item) {
            return $item->subtotal * (($item->article->ivaRate->taxa ?? 0) / 100);
        });

        $totalWithIva = $totalWithoutIva + $totalIva;

        $pdf = Pdf::loadView('order/pdf', [
            'order' => $order,
            'totalWithoutIva' => $totalWithoutIva,
            'totalIva' => $totalIva,
            'totalWithIva' => $totalWithIva,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("encomenda-{$order->numero}.pdf");
    }

    public function convertToSupplierOrders(Order $order)
    {
        if ($order->estado !== 'fechado') {
            return back()->with('error', 'Apenas encomendas fechadas podem ser convertidas.');
        }

        if (SupplierOrder::where('order_id', $order->id)->exists()) {
            return back()->with('error', 'Esta encomenda já foi convertida em encomendas de fornecedor.');
        }

        $itemsComFornecedor = $order->items()
            ->whereNotNull('fornecedor_id')
            ->with(['article', 'supplier'])
            ->get();

        if ($itemsComFornecedor->isEmpty()) {
            return back()->with('info', 'Nenhum artigo desta encomenda requer fornecedor.');
        }

        $itemsBySupplier = $itemsComFornecedor->groupBy('fornecedor_id');
        $encomendasCriadas = 0;

        DB::transaction(function () use ($order, $itemsBySupplier, &$encomendasCriadas) {
            foreach ($itemsBySupplier as $fornecedorId => $items) {
                $supplierOrder = SupplierOrder::create([
                    'fornecedor_id' => $fornecedorId,
                    'order_id' => $order->id,
                    'estado' => 'rascunho',
                ]);

                foreach ($items as $item) {
                    SupplierOrderItem::create([
                        'supplier_order_id' => $supplierOrder->id,
                        'article_id' => $item->article_id,
                        'quantidade' => $item->quantidade,
                        'preco_unitario' => $item->preco_custo ?? $item->preco_unitario,
                    ]);
                }

                $supplierOrder->calculateTotalValue();
                $encomendasCriadas++;
            }
        });

        $fornecedores = $itemsBySupplier->count();
        $message = $fornecedores === 1
            ? "1 encomenda de fornecedor criada com sucesso!"
            : "{$fornecedores} encomendas de fornecedor criadas com sucesso!";

        return redirect()->route('supplier-orders.index')->with('success', $message);
    }



}
