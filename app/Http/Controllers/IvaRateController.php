<?php

namespace App\Http\Controllers;

use App\Models\IvaRate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class IvaRateController extends Controller
{
    public function index()
    {
        $ivaRates = IvaRate::orderBy('taxa')->get();

        return Inertia::render('CompanySetting/IvaRates/Index', [
            'ivaRates' => $ivaRates
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'taxa' => ['required', 'numeric', 'min:0', 'max:100'],
            'descricao' => ['nullable', 'string'],
            'estado' => ['required', Rule::in(['ativo', 'inativo'])],
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'taxa.required' => 'A taxa é obrigatória.',
            'taxa.numeric' => 'A taxa deve ser um número.',
            'taxa.min' => 'A taxa não pode ser negativa.',
            'taxa.max' => 'A taxa não pode ser superior a 100%.',
        ]);

        IvaRate::create($validated);

        return back()->with('success', 'Taxa de IVA criada com sucesso');
    }

    public function update(Request $request, IvaRate $ivaRate)
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'taxa' => ['required', 'numeric', 'min:0', 'max:100'],
            'descricao' => ['nullable', 'string'],
            'estado' => ['required', Rule::in(['ativo', 'inativo'])],
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'taxa.required' => 'A taxa é obrigatória.',
            'taxa.numeric' => 'A taxa deve ser um número.',
            'taxa.min' => 'A taxa não pode ser negativa.',
            'taxa.max' => 'A taxa não pode ser superior a 100%.',
        ]);

        $ivaRate->update($validated);

        return back()->with('success', 'Taxa de IVA atualizada com sucesso');
    }

    public function destroy(IvaRate $ivaRate)
    {
        if ($ivaRate->articles()->count() > 0) {
            return back()->with('error', 'Não é possível eliminar. Existem artigos associados a esta taxa.');
        }

        $ivaRate->delete();

        return back()->with('success', 'Taxa de IVA eliminada com sucesso');
    }
}
