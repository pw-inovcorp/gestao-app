<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProposalController extends Controller
{
    //
    public function index(Request $request)
    {
        $proposals = Proposal::with(['cliente', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Proposal/Index', [
            'proposals' => $proposals
        ]);
    }
}
