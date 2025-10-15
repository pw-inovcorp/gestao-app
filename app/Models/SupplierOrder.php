<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SupplierOrder extends Model
{
    //

    protected $fillable = [
        'numero',
        'data_encomenda',
        'fornecedor_id',
        'order_id',
        'valor_total',
        'estado',
        'observacoes',
    ];

    protected $casts = [
        'data_encomenda' => 'date',
        'valor_total' => 'decimal:2',
    ];


    public function supplier()
    {
        return $this->belongsTo(Entity::class, 'fornecedor_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(SupplierOrderItem::class);
    }

    public function calculateTotalValue()
    {
        $this->valor_total = $this->items()->sum('subtotal');
        $this->save();
    }

    public static function gerarNumero(): string
    {
        $ultimoId = self::query()->max('id') ?? 0;
        return 'EF-' . str_pad($ultimoId + 1, 4, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($supplierOrder) {
            if (empty($supplierOrder->numero)) {
                $supplierOrder->numero = self::gerarNumero();
            }

            if ($supplierOrder->estado === 'fechado' && empty($supplierOrder->data_encomenda)) {
                $supplierOrder->data_encomenda = Carbon::now();
            }
        });

        static::updating(function ($supplierOrder) {
            if ($supplierOrder->estado === 'fechado' && empty($supplierOrder->data_encomenda)) {
                $supplierOrder->data_encomenda = Carbon::now();
            }
        });
    }
}
