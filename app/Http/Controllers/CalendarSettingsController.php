<?php

namespace App\Http\Controllers;

use App\Models\CalendarAction;
use App\Models\CalendarType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CalendarSettingsController extends Controller
{
    // Calendario Tipos

    public function indexTypes()
    {
        $types = CalendarType::orderBy('nome')->get();

        return Inertia::render('Calendar/Settings/Types/Index', [
            'types' => $types
        ]);
    }

    public function storeType(Request $request)
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255', 'unique:calendar_types,nome'],
            'cor' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'descricao' => ['nullable', 'string'],
            'estado' => ['required', Rule::in(['ativo', 'inativo'])],
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'nome.unique' => 'Já existe um tipo com este nome.',
            'cor.required' => 'A cor é obrigatória.',
            'cor.regex' => 'A cor deve estar no formato hexadecimal (ex: #3b82f6).',
        ]);

        CalendarType::create($validated);

        return back()->with('success', 'Tipo criado com sucesso');
    }

    public function updateType(Request $request, CalendarType $type)
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255', Rule::unique('calendar_types', 'nome')->ignore($type->id)],
            'cor' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'descricao' => ['nullable', 'string'],
            'estado' => ['required', Rule::in(['ativo', 'inativo'])],
        ]);

        $type->update($validated);

        return back()->with('success', 'Tipo atualizado com sucesso');
    }

    public function destroyType(CalendarType $type)
    {
        $type->delete();

        return back()->with('success', 'Tipo eliminado com sucesso');
    }


    //Calendario Ações

    public function indexActions()
    {
        $actions = CalendarAction::orderBy('nome')->get();

        return Inertia::render('Calendar/Settings/Actions/Index', [
            'actions' => $actions
        ]);
    }

    public function storeAction(Request $request)
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255', 'unique:calendar_actions,nome'],
            'descricao' => ['nullable', 'string'],
            'estado' => ['required', Rule::in(['ativo', 'inativo'])],
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'nome.unique' => 'Já existe uma ação com este nome.',
        ]);

        CalendarAction::create($validated);

        return back()->with('success', 'Ação criada com sucesso');
    }

    public function updateAction(Request $request, CalendarAction $action)
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255', Rule::unique('calendar_actions', 'nome')->ignore($action->id)],
            'descricao' => ['nullable', 'string'],
            'estado' => ['required', Rule::in(['ativo', 'inativo'])],
        ]);

        $action->update($validated);

        return back()->with('success', 'Ação atualizada com sucesso');
    }

    public function destroyAction(CalendarAction $action)
    {
        $action->delete();

        return back()->with('success', 'Ação eliminada com sucesso');
    }
}
