<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCustomActivityLog;

class SupplierInvoice extends Model
{
    //
    use HasCustomActivityLog;

    protected static $logAttributes = ['fornecedor_id', 'valor_total', 'estado', 'data_pagamento'];


    protected $fillable = [
        'numero',
        'data_fatura',
        'data_vencimento',
        'fornecedor_id',
        'supplier_order_id',
        'valor_total',
        'documento',
        'comprovativo_pagamento',
        'estado',
        'data_pagamento',
    ];

    protected $casts = [
        'data_fatura' => 'date',
        'data_vencimento' => 'date',
        'data_pagamento' => 'date',
        'valor_total' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Entity::class, 'fornecedor_id');
    }

    public function supplierOrder()
    {
        return $this->belongsTo(SupplierOrder::class);
    }

    public static function gerarNumero(): string
    {
        $ultimoId = self::query()->max('id') ?? 0;
        return 'FT-' . str_pad($ultimoId + 1, 4, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->numero)) {
                $invoice->numero = self::gerarNumero();
            }
        });
    }

}
