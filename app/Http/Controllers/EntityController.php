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

        // Aplicar filtro
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

}
