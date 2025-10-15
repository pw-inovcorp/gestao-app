<?php

namespace App\Http\Controllers;

use App\Models\SupplierOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupplierOrderController extends Controller
{
    //
    public function index(Request $request)
    {
        $supplierOrders = SupplierOrder::with(['supplier', 'items', 'order'])
            ->orderBy('numero', 'desc')
            ->paginate(10);

        return Inertia::render('SupplierOrder/Index', [
            'supplierOrders' => $supplierOrders
        ]);
    }
}
