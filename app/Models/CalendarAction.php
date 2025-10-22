<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarAction extends Model
{
    //
    protected $fillable = [
        'nome',
        'descricao',
        'estado',
    ];

    public function events()
    {
        return $this->hasMany(CalendarEvent::class);
    }
}
