<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCustomActivityLog;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory, HasCustomActivityLog;

    protected static $logAttributes = ['cliente_id', 'valor_total', 'estado', 'proposal_id'];

    protected $fillable = [
        'numero',
        'data_encomenda',
        'cliente_id',
        'valor_total',
        'estado',
        'proposal_id',
    ];

    protected $casts = [
        'data_encomenda' => 'date',
        'valor_total' => 'decimal:2',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(Entity::class, 'cliente_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function supplierOrders()
    {
        return $this->hasMany(SupplierOrder::class);
    }

    // Helper methods
    public function calculateTotalValue()
    {
        $this->valor_total = $this->items()->sum('subtotal');
        $this->save();
    }

    public static function gerarNumero(): string
    {
        $ultimoId = self::query()->max('id') ?? 0;
        return 'ENC-' . str_pad($ultimoId + 1, 4, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->numero)) {
                $order->numero = self::gerarNumero();
            }

            if ($order->estado === 'fechado' && empty($order->data_encomenda)) {
                $order->data_encomenda = Carbon::now();
            }
        });

        static::updating(function ($order) {
            if ($order->estado === 'fechado' && empty($order->data_encomenda)) {
                $order->data_encomenda = Carbon::now();
            }
        });
    }
}
