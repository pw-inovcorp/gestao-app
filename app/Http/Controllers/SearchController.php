<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Contact;
use App\Models\Entity;
use App\Models\Order;
use App\Models\Proposal;
use App\Models\SupplierOrder;
use App\Models\SupplierInvoice;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');


        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = [
            'entities' => [],
            'contacts' => [],
            'articles' => [],
            'proposals' => [],
            'orders' => [],
            'supplierOrders' => [],
            'supplierInvoices' => [],
        ];


        if (auth()->user()->can('entities.view')) {
            $results['entities'] = Entity::where(function($q) use ($query) {
                $q->where('nome', 'like', "%{$query}%")
                    ->orWhere('nif', 'like', "%{$query}%");
            })
                ->where('estado', 'ativo')
                ->limit(5)
                ->get(['id', 'nome', 'nif']);
        }


        if (auth()->user()->can('contacts.view')) {
            $results['contacts'] = Contact::where(function($q) use ($query) {
                $q->where('nome', 'like', "%{$query}%")
                    ->orWhere('apelido', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
                ->where('estado', 'ativo')
                ->limit(5)
                ->get(['id', 'nome', 'apelido', 'email', 'telemovel']);
        }


        if (auth()->user()->can('articles.view')) {
            $results['articles'] = Article::where(function($q) use ($query) {
                $q->where('nome', 'like', "%{$query}%")
                    ->orWhere('referencia', 'like', "%{$query}%");
            })
                ->where('estado', 'ativo')
                ->limit(5)
                ->get(['id', 'nome', 'referencia']);
        }


        if (auth()->user()->can('proposals.view')) {
            $results['proposals'] = Proposal::with('client:id,nome')
                ->where('numero', 'like', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(function($proposal) {
                    return [
                        'id' => $proposal->id,
                        'numero' => $proposal->numero,
                        'client_name' => $proposal->client->nome ?? '',
                    ];
                });
        }


        if (auth()->user()->can('orders.view')) {
            $results['orders'] = Order::with('client:id,nome')
                ->where('numero', 'like', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(function($order) {
                    return [
                        'id' => $order->id,
                        'numero' => $order->numero,
                        'client_name' => $order->client->nome ?? '',
                    ];
                });
        }


        if (auth()->user()->can('supplier-orders.view')) {
            $results['supplierOrders'] = SupplierOrder::with('supplier:id,nome')
                ->where('numero', 'like', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(function($so) {
                    return [
                        'id' => $so->id,
                        'numero' => $so->numero,
                        'supplier_name' => $so->supplier->nome ?? '',
                    ];
                });
        }


        if (auth()->user()->can('supplier-invoices.view')) {
            $results['supplierInvoices'] = SupplierInvoice::with('supplier:id,nome')
                ->where('numero', 'like', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(function($si) {
                    return [
                        'id' => $si->id,
                        'numero' => $si->numero,
                        'supplier_name' => $si->supplier->nome ?? '',
                    ];
                });
        }

        return response()->json($results);
    }
}
