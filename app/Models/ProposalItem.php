<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalItem extends Model
{
    /** @use HasFactory<\Database\Factories\ProposalItemFactory> */
    use HasFactory;

    protected $fillable = [
        'proposal_id',
        'article_id',
        'fornecedor_id',
        'quantidade',
        'preco_unitario',
        'preco_custo',
        'subtotal',
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'preco_unitario' => 'decimal:2',
        'preco_custo' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relationships
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Entity::class, 'fornecedor_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->subtotal = $item->quantidade * $item->preco_unitario;
        });
    }
}
