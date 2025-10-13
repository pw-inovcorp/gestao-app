<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Entity;
use App\Models\Proposal;
use App\Models\ProposalItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProposalController extends Controller
{
    //
    public function index(Request $request)
    {
        $proposals = Proposal::with(['client', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Proposal/Index', [
            'proposals' => $proposals
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

        return Inertia::render('Proposal/Create', [
            'clients' => $clients,
            'articles' => $articles,
            'suppliers' => $suppliers
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => ['required', 'exists:entities,id'],
            'validade' => ['required', 'date', 'after:today'],
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
            'validade.required' => 'A data de validade é obrigatória.',
            'validade.after' => 'A data de validade deve ser posterior a hoje.',
            'items.required' => 'Deve adicionar pelo menos um artigo.',
            'items.min' => 'Deve adicionar pelo menos um artigo.',
        ]);


        $proposal = Proposal::create([
            'cliente_id' => $validated['cliente_id'],
            'validade' => $validated['validade'],
            'estado' => $validated['estado'],
        ]);

        foreach ($validated['items'] as $item) {
            ProposalItem::create([
                'proposal_id' => $proposal->id,
                'article_id' => $item['article_id'],
                'fornecedor_id' => $item['fornecedor_id'] ?? null,
                'quantidade' => $item['quantidade'],
                'preco_unitario' => $item['preco_unitario'],
                'preco_custo' => $item['preco_custo'] ?? null,
            ]);
        }

        $proposal->calculateTotalValue();

        return redirect()->route('proposals.index')
            ->with('success', 'Proposta criada com sucesso');
    }

    public function edit(Proposal $proposal)
    {
        // Só permite editar propostas em rascunho
        if ($proposal->estado !== 'rascunho') {
            return redirect()->route('proposals.index')
                ->with('error', 'Apenas propostas em rascunho podem ser editadas.');
        }

        $proposal->load(['items.article.ivaRate', 'items.supplier']);

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

        return Inertia::render('Proposal/Edit', [
            'proposal' => $proposal,
            'clients' => $clients,
            'articles' => $articles,
            'suppliers' => $suppliers
        ]);
    }

    public function update(Request $request, Proposal $proposal)
    {

        if ($proposal->estado !== 'rascunho') {
            return back()->with('error', 'Apenas propostas em rascunho podem ser editadas.');
        }

        $validated = $request->validate([
            'cliente_id' => ['required', 'exists:entities,id'],
            'validade' => ['required', 'date', 'after:today'],
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
            'validade.required' => 'A data de validade é obrigatória.',
            'validade.after' => 'A data de validade deve ser posterior a hoje.',
            'items.required' => 'Deve adicionar pelo menos um artigo.',
            'items.min' => 'Deve adicionar pelo menos um artigo.',
        ]);


        $proposal->update([
            'cliente_id' => $validated['cliente_id'],
            'validade' => $validated['validade'],
            'estado' => $validated['estado'],
        ]);


        $proposal->items()->delete();


        foreach ($validated['items'] as $item) {
            ProposalItem::create([
                'proposal_id' => $proposal->id,
                'article_id' => $item['article_id'],
                'fornecedor_id' => $item['fornecedor_id'] ?? null,
                'quantidade' => $item['quantidade'],
                'preco_unitario' => $item['preco_unitario'],
                'preco_custo' => $item['preco_custo'] ?? null,
            ]);
        }


        $proposal->calculateTotalValue();

        return redirect()->route('proposals.index')
            ->with('success', 'Proposta atualizada com sucesso!');
    }

    public function destroy(Proposal $proposal)
    {
        if ($proposal->estado !== 'rascunho') {
            return back()->with('error', 'Apenas propostas em rascunho podem ser eliminadas.');
        }

        $numero = $proposal->numero;

        $proposal->delete();

        return redirect()->route('proposals.index')
            ->with('success', "Proposta {$numero} eliminada com sucesso!");
    }


}
