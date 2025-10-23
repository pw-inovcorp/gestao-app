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

    public function scopeAtivo($query)
    {
        return $query->where('estado', 'ativo');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByEntity($query, $entityId)
    {
        return $query->where('entity_id', $entityId);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('data', [$startDate, $endDate]);
    }

}
