<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Inertia\Inertia;

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
}
