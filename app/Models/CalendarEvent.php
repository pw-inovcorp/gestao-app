<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    //
    protected $logAttributes = ['titulo', 'data', 'hora', 'entity_id', 'estado'];

    protected $fillable = [
        'titulo',
        'data',
        'hora',
        'duracao',
        'partilha',
        'conhecimento',
        'descricao',
        'entity_id',
        'calendar_type_id',
        'calendar_action_id',
        'user_id',
        'estado'
    ];

    protected $casts = [
        'data' => 'date',
        'partilha' => 'array'
    ];


    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function calendarType()
    {
        return $this->belongsTo(CalendarType::class);
    }

    public function calendarAction()
    {
        return $this->belongsTo(CalendarAction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
