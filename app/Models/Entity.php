<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCustomActivityLog;

class Entity extends Model
{
    /** @use HasFactory<\Database\Factories\EntityFactory> */
    use HasFactory, HasCustomActivityLog;

    protected $logAttributes = ['nome', 'nif', 'email', 'is_cliente', 'is_fornecedor', 'estado'];

    protected $fillable = [
        'numero',
        'nif',
        'nome',
        'morada',
        'codigo_postal',
        'localidade',
        'country_id',
        'telefone',
        'telemovel',
        'website',
        'email',
        'consentimento_rgpd',
        'observacoes',
        'is_cliente',
        'is_fornecedor',
        'estado',
    ];

    protected $casts = [
        'consentimento_rgpd' => 'boolean',
        'is_cliente' => 'boolean',
        'is_fornecedor' => 'boolean',
    ];


    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }


    public static function gerarNumero(): string
    {
        $ultimoId = self::query()->max('id') ?? 0;
        return 'ENT-' . str_pad($ultimoId + 1, 4, '0', STR_PAD_LEFT);
    }

    public function isCliente(): bool
    {
        return $this->is_cliente;
    }

    public function isFornecedor(): bool
    {
        return $this->is_fornecedor;
    }

    public function isAtivo(): bool
    {
        return $this->estado === 'ativo';
    }



    protected static function boot()
    {
        parent::boot();

        static::creating(function ($entity) {
            if (empty($entity->numero)) {
                $entity->numero = self::gerarNumero();
            }
        });
    }
}
