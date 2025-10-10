<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use App\Models\Entity;
use App\Models\ContactFunction;

class ContactController extends Controller
{
    //
    public function index(Request $request)
    {
        $contacts = Contact::with(['entity', 'contactFunction'])
            ->select([
                'id',
                'numero',
                'nome',
                'apelido',
                'contact_function_id',
                'entity_id',
                'telefone',
                'telemovel',
                'email',
                'estado',
                'created_at'
            ])
            ->orderBy('nome', 'asc')
            ->paginate(10);

        return Inertia::render('Contact/Index', [
            'contacts' => $contacts
        ]);
    }

    public function create()
    {
        $entities = Entity::where('estado', 'ativo')
            ->orderBy('nome')
            ->get(['id', 'nome', 'nif']);

        $contactFunctions = ContactFunction::where('estado', 'ativo')
            ->orderBy('nome')
            ->get(['id', 'nome']);

        return Inertia::render('Contact/Create', [
            'entities' => $entities,
            'contactFunctions' => $contactFunctions
        ]);
    }

    public function show(Contact $contact)
    {
        $contact->load(['entity', 'contactFunction']);

        return Inertia::render('Contact/Show', [
            'contact' => $contact
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'entity_id' => ['required', 'exists:entities,id'],
            'nome' => ['required', 'string', 'max:255', 'min:2'],
            'apelido' => ['required', 'string', 'max:255', 'min:2'],
            'contact_function_id' => ['nullable', 'exists:contact_functions,id'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'telemovel' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'observacoes' => ['nullable', 'string'],
            'consentimento_rgpd' => ['required', 'boolean'],
            'estado' => ['required', Rule::in(['ativo', 'inativo'])],
        ], [
            'entity_id.required' => 'Deve selecionar uma entidade.',
            'entity_id.exists' => 'Entidade inválida.',
            'nome.required' => 'O nome é obrigatório.',
            'nome.min' => 'O nome deve ter no mínimo 2 caracteres.',
            'apelido.required' => 'O apelido é obrigatório.',
            'apelido.min' => 'O apelido deve ter no mínimo 2 caracteres.',
            'contact_function_id.exists' => 'Função inválida.',
            'email.email' => 'Email inválido.',
        ]);

        Contact::create($validated);

        return redirect()->route('contacts.index')
            ->with('success', 'Contacto criado com sucesso!');
    }

    public function edit(Contact $contact)
    {
        $entities = Entity::where('estado', 'ativo')
            ->orderBy('nome')
            ->get(['id', 'nome', 'nif']);

        $contactFunctions = ContactFunction::where('estado', 'ativo')
            ->orderBy('nome')
            ->get(['id', 'nome']);

        return Inertia::render('Contact/Edit', [
            'contact' => $contact,
            'entities' => $entities,
            'contactFunctions' => $contactFunctions
        ]);
    }

    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'entity_id' => ['required', 'exists:entities,id'],
            'nome' => ['required', 'string', 'max:255', 'min:2'],
            'apelido' => ['required', 'string', 'max:255', 'min:2'],
            'contact_function_id' => ['nullable', 'exists:contact_functions,id'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'telemovel' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'observacoes' => ['nullable', 'string'],
            'consentimento_rgpd' => ['required', 'boolean'],
            'estado' => ['required', Rule::in(['ativo', 'inativo'])],
        ], [
            'entity_id.required' => 'Deve selecionar uma entidade.',
            'entity_id.exists' => 'Entidade inválida.',
            'nome.required' => 'O nome é obrigatório.',
            'nome.min' => 'O nome deve ter no mínimo 2 caracteres.',
            'apelido.required' => 'O apelido é obrigatório.',
            'apelido.min' => 'O apelido deve ter no mínimo 2 caracteres.',
            'contact_function_id.exists' => 'Função inválida.',
            'email.email' => 'Email inválido.',
        ]);

        $contact->update($validated);

        return redirect()->route('contacts.index')
            ->with('success', 'Contacto atualizada com sucesso!');

    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('contacts.index')
            ->with('success', 'Contacto eliminada com sucesso!');
    }

}
