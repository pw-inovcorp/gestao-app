<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    /** @use HasFactory<\Database\Factories\ProposalFactory> */
    use HasFactory;

    protected $fillable = [
        'numero',
        'data_proposta',
        'cliente_id',
        'validade',
        'valor_total',
        'estado',
    ];

    protected $casts = [
        'data_proposta' => 'date',
        'validade' => 'date',
        'valor_total' => 'decimal:2',
    ];


    public function client()
    {
        return $this->belongsTo(Entity::class, 'cliente_id');
    }

    public function items()
    {
        return $this->hasMany(ProposalItem::class);
    }

    public function calculateTotalValue()
    {
        $this->valor_total = $this->items()->sum('subtotal');
        $this->save();
    }


    public static function gerarNumero(): string
    {
        $ultimoId = self::query()->max('id') ?? 0;
        return 'PROP-' . str_pad($ultimoId + 1, 4, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($proposal) {
            if (empty($proposal->numero)) {
                $proposal->numero = self::gerarNumero();
            }

            if ($proposal->estado === 'fechado' && empty($proposal->data_proposta)) {
                $proposal->data_proposta = Carbon::now();
            }

            if (empty($proposal->validade) && $proposal->data_proposta) {
                $proposal->validade = Carbon::parse($proposal->data_proposta)->addDays(30);
            }
        });

        static::updating(function ($proposal) {
            if ($proposal->estado === 'fechado' && empty($proposal->data_proposta)) {
                $proposal->data_proposta = Carbon::now();
            }
        });
    }
}
