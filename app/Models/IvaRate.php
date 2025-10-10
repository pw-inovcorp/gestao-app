<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IvaRate extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'nome',
        'taxa',
        'descricao',
        'estado',
    ];

    protected $casts = [
        'taxa' => 'decimal:2',
    ];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
