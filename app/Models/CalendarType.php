<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarType extends Model
{
    //
    protected $fillable = [
        'nome',
        'cor',
        'descricao',
        'estado',
    ];

    public function events()
    {
        return $this->hasMany(CalendarEvent::class);
    }
}
