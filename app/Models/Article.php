<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCustomActivityLog;

class Article extends Model
{
    /** @use HasFactory<\Database\Factories\ArticleFactory> */
    use HasFactory, HasCustomActivityLog;

    protected $logAttributes = ['nome', 'descricao', 'preco', 'iva_rate_id', 'estado'];

    protected $fillable = [
        'referencia',
        'nome',
        'descricao',
        'preco',
        'iva_rate_id',
        'foto',
        'observacoes',
        'estado',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
    ];

    public function ivaRate()
    {
        return $this->belongsTo(IvaRate::class);
    }


    public static function gerarReferencia(): string
    {
        $ultimoId = self::query()->max('id') ?? 0;
        return 'ART-' . str_pad($ultimoId + 1, 4, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->referencia)) {
                $article->referencia = self::gerarReferencia();
            }
        });
    }
}
