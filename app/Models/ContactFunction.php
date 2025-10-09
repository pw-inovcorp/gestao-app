<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactFunction extends Model
{
    /** @use HasFactory<\Database\Factories\ContactFunctionFactory> */
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'estado',
    ];

    // Relationships
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

}
