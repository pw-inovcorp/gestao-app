<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCustomActivityLog;

class Contact extends Model
{
    /** @use HasFactory<\Database\Factories\ContactFactory> */
    use HasFactory, HasCustomActivityLog;

    protected $logAttributes = ['nome', 'apelido', 'email', 'telemovel', 'estado'];

    protected $fillable = [
        'numero',
        'entity_id',
        'nome',
        'apelido',
        'contact_function_id',
        'telefone',
        'telemovel',
        'email',
        'consentimento_rgpd',
        'observacoes',
        'estado',
    ];

    protected $casts = [
        'consentimento_rgpd' => 'boolean',
    ];


    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function contactFunction()
    {
        return $this->belongsTo(ContactFunction::class);
    }

    public static function gerarNumero(): string
    {
        $ultimoId = self::query()->max('id') ?? 0;
        return 'CON-' . str_pad($ultimoId + 1, 4, '0', STR_PAD_LEFT);
    }

    public function isAtivo(): bool
    {
        return $this->estado === 'ativo';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contact) {
            if (empty($contact->numero)) {
                $contact->numero = self::gerarNumero();
            }
        });
    }

}
