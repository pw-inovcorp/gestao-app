<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    /** @use HasFactory<\Database\Factories\CountryFactory> */
    use HasFactory;

    protected $fillable = [
        'nome',
        'codigo',
        'estado',
    ];

    // Relationships
    public function entities()
    {
        return $this->hasMany(Entity::class);
    }
}
