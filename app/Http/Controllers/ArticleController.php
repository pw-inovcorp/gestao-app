<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use App\Models\IvaRate;


class ArticleController extends Controller
{
    //
    public function index(Request $request)
    {
        $articles = Article::with('ivaRate')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Article/Index', [
            'articles' => $articles
        ]);
    }
    public function create()
    {
        $ivaRates = IvaRate::where('estado', 'ativo')
            ->orderBy('taxa')
            ->get(['id', 'nome', 'taxa']);

        return Inertia::render('Article/Create', [
            'ivaRates' => $ivaRates
        ]);
    }

    public function show(Article $article)
    {
        $article->load('ivaRate');

        return Inertia::render('Article/Show', [
            'article' => $article
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255', 'min:2'],
            'descricao' => ['nullable', 'string'],
            'preco' => ['required', 'numeric', 'min:0'],
            'iva_rate_id' => ['required', 'exists:iva_rates,id'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'observacoes' => ['nullable', 'string'],
            'estado' => ['required', Rule::in(['ativo', 'inativo'])],
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'nome.min' => 'O nome deve ter no mínimo 2 caracteres.',
            'preco.required' => 'O preço é obrigatório.',
            'preco.numeric' => 'O preço deve ser um número válido.',
            'preco.min' => 'O preço não pode ser negativo.',
            'iva_rate_id.required' => 'Deve selecionar uma taxa de IVA.',
            'iva_rate_id.exists' => 'Taxa de IVA inválida.',
            'foto.image' => 'O ficheiro deve ser uma imagem.',
            'foto.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg, gif ou webp.',
            'foto.max' => 'A imagem não pode ter mais de 2MB.',
        ]);


        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('articles', 'public');
            $validated['foto'] = $path;
        }

        Article::create($validated);

        return redirect()->route('articles.index')
            ->with('success', 'Artigo criado com sucesso');
    }

    public function edit(Article $article)
    {
        $ivaRates = IvaRate::where('estado', 'ativo')
            ->orderBy('taxa')
            ->get(['id', 'nome', 'taxa']);

        return Inertia::render('Article/Edit', [
            'article' => $article,
            'ivaRates' => $ivaRates
        ]);
    }

    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255', 'min:2'],
            'descricao' => ['nullable', 'string'],
            'preco' => ['required', 'numeric', 'min:0'],
            'iva_rate_id' => ['required', 'exists:iva_rates,id'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'observacoes' => ['nullable', 'string'],
            'estado' => ['required', Rule::in(['ativo', 'inativo'])],
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'nome.min' => 'O nome deve ter no mínimo 2 caracteres.',
            'preco.required' => 'O preço é obrigatório.',
            'preco.numeric' => 'O preço deve ser um número válido.',
            'preco.min' => 'O preço não pode ser negativo.',
            'iva_rate_id.required' => 'Deve selecionar uma taxa de IVA.',
            'iva_rate_id.exists' => 'Taxa de IVA inválida.',
            'foto.image' => 'O ficheiro deve ser uma imagem.',
            'foto.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg, gif ou webp.',
            'foto.max' => 'A imagem não pode ter mais de 2MB.',
        ]);

        if ($request->hasFile('foto')) {

            if ($article->foto && \Storage::disk('public')->exists($article->foto)) {
                \Storage::disk('public')->delete($article->foto);
            }

            $path = $request->file('foto')->store('articles', 'public');
            $validated['foto'] = $path;
        }


        $article->update($validated);

        return redirect()->route('articles.index')
            ->with('success', 'Artigo atualizado com sucesso');
    }

    public function destroy(Article $article)
    {
        if ($article->foto && \Storage::disk('public')->exists($article->foto)) {
            \Storage::disk('public')->delete($article->foto);
        }

        $article->delete();

        return redirect()->route('articles.index')
            ->with('success', 'Artigo eliminado com sucesso');
    }



}
