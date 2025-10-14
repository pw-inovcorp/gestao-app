<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Entity;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Proposal;
use App\Models\ProposalItem;
use Illuminate\Http\Request;
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
}
