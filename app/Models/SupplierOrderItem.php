<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierOrderItem extends Model
{
    //
    protected $fillable = [
        'supplier_order_id',
        'article_id',
        'quantidade',
        'preco_unitario',
        'subtotal',
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'preco_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function supplierOrder()
    {
        return $this->belongsTo(SupplierOrder::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->subtotal = $item->quantidade * $item->preco_unitario;
        });
    }
}
