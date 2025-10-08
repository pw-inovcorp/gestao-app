<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\Country;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class EntityController extends Controller
{
    //
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'todos');

        $query = Entity::with('country')
            ->select(['id', 'numero', 'nif', 'nome', 'telefone', 'telemovel', 'website', 'email', 'is_cliente', 'is_fornecedor', 'estado', 'country_id', 'created_at']);

        if ($filter === 'clientes') {
            $query->where('is_cliente', true);
        } elseif ($filter === 'fornecedores') {
            $query->where('is_fornecedor', true);
        }

        $entities = $query->orderBy('created_at', 'desc')->paginate(10);

        return Inertia::render('Entity/Index', [
            'entities' => $entities,
            'filter' => $filter
        ]);
    }

    public function create()
    {
        $countries = Country::where('estado', 'ativo')
            ->orderBy('nome')
            ->get(['id', 'nome', 'codigo']);

        return Inertia::render('Entity/Create', [
            'countries' => $countries
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'is_cliente' => ['required', 'boolean'],
            'is_fornecedor' => ['required', 'boolean'],
            'nome' => ['required', 'string', 'max:255','min:2'],
            'nif' => ['required', 'string', 'size:9', 'unique:entities,nif'],
            'country_id' => ['required', 'exists:countries,id'],
            'morada' => ['nullable', 'string', 'max:255'],
            'codigo_postal' => ['nullable', 'string', 'regex:/^\d{4}-\d{3}$/'],
            'localidade' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'telemovel' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'observacoes' => ['nullable', 'string'],
            'consentimento_rgpd' => ['required', 'boolean'],
            'estado' => ['required', Rule::in(['ativo', 'inativo'])],
        ], [
            'is_cliente.required' => 'Deve selecionar pelo menos um tipo de entidade.',
            'is_fornecedor.required' => 'Deve selecionar pelo menos um tipo de entidade.',
            'nif.unique' => 'Este NIF já está registado.',
            'nif.size' => 'O NIF deve ter exatamente 9 dígitos.',
            'codigo_postal.regex' => 'O código postal deve ter o formato XXXX-XXX.',
            'country_id.exists' => 'País inválido.',
        ]);


        if (!$validated['is_cliente'] && !$validated['is_fornecedor']) {
            return back()->withErrors([
                'is_cliente' => 'Deve selecionar pelo menos um tipo (Cliente ou Fornecedor).'
            ])->withInput();
        }

        Entity::create($validated);

        return redirect()->route('entities.index')
            ->with('success', 'Entidade criada com sucesso!');
    }

    public function edit(Entity $entity)
    {
        $countries = Country::where('estado', 'ativo')
            ->orderBy('nome')
            ->get(['id', 'nome', 'codigo']);

        return Inertia::render('Entity/Edit', [
            'entity' => $entity,
            'countries' => $countries
        ]);
    }

    public function update(Request $request, Entity $entity)
    {
        $validated = $request->validate([
            'is_cliente' => ['required', 'boolean'],
            'is_fornecedor' => ['required', 'boolean'],
            'nome' => ['required', 'string', 'max:255', 'min:2'],
            'nif' => ['required', 'string', 'size:9', Rule::unique('entities', 'nif')->ignore($entity->id)],
            'country_id' => ['required', 'exists:countries,id'],
            'morada' => ['nullable', 'string', 'max:255'],
            'codigo_postal' => ['nullable', 'string', 'regex:/^\d{4}-\d{3}$/'],
            'localidade' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'telemovel' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'observacoes' => ['nullable', 'string'],
            'consentimento_rgpd' => ['required', 'boolean'],
            'estado' => ['required', Rule::in(['ativo', 'inativo'])],
        ], [
            'is_cliente.required' => 'Deve selecionar pelo menos um tipo de entidade.',
            'is_fornecedor.required' => 'Deve selecionar pelo menos um tipo de entidade.',
            'nif.unique' => 'Este NIF já está registado.',
            'nif.size' => 'O NIF deve ter exatamente 9 dígitos.',
            'codigo_postal.regex' => 'O código postal deve ter o formato XXXX-XXX.',
            'country_id.exists' => 'País inválido.',
        ]);

        if (!$validated['is_cliente'] && !$validated['is_fornecedor']) {
            return back()->withErrors([
                'is_cliente' => 'Deve selecionar pelo menos um tipo (Cliente ou Fornecedor).'
            ])->withInput();
        }

        $entity->update($validated);

        return redirect()->route('entities.index')
            ->with('success', 'Entidade atualizada com sucesso!');
    }

    public function destroy(Entity $entity)
    {
        $entity->delete();

        return redirect()->route('entities.index')
            ->with('success', 'Entidade eliminada com sucesso!');
    }


    public function checkVies(Request $request)
    {
        $request->validate([
            'nif' => ['required', 'string', 'size:9'],
            'country_code' => ['required', 'string', 'size:2']
        ]);

        try {
            $countryCode = strtoupper($request->country_code);
            $vatNumber = $request->nif;


            $client = new \SoapClient(
                "https://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl",
                [
                    'exceptions' => true,
                    'connection_timeout' => 10,
                ]
            );


            $result = $client->checkVat([
                'countryCode' => $countryCode,
                'vatNumber' => $vatNumber
            ]);


            if ($result->valid) {
                return response()->json([
                    'success' => true,
                    'valid' => true,
                    'data' => [
                        'name' => $result->name ?? null,
                        'address' => $result->address ?? null,
                        'country_code' => $countryCode,
                        'vat_number' => $vatNumber
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'valid' => false,
                    'message' => 'NIF não encontrado no sistema VIES'
                ]);
            }

        } catch (\SoapFault $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao conectar ao VIES: ' . $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao validar NIF: ' . $e->getMessage()
            ], 422);
        }
    }

}
