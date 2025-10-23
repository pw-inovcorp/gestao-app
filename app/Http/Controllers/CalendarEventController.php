<?php

namespace App\Http\Controllers;

use App\Models\CalendarAction;
use App\Models\CalendarEvent;
use App\Models\CalendarType;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CalendarEventController extends Controller
{
    public function index()
    {
        $entities = Entity::where('estado', 'ativo')
            ->orderBy('nome')
            ->get(['id', 'nome']);

        $types = CalendarType::ativo()
            ->orderBy('nome')
            ->get(['id', 'nome', 'cor']);

        $actions = CalendarAction::ativo()
            ->orderBy('nome')
            ->get(['id', 'nome']);

        $users = User::where('status', 'active')
            ->where('id', '!=', auth()->id())
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Calendar/Index', [
            'entities' => $entities,
            'types' => $types,
            'actions' => $actions,
            'users' => $users,
        ]);
    }

    public function getEvents(Request $request)
    {
        $currentUserId = auth()->id();

        $query = CalendarEvent::with(['user', 'entity', 'calendarType', 'calendarAction'])
            ->ativo();

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        } else {
            $query->where(function($q) use ($currentUserId) {
                $q->where('user_id', $currentUserId)
                    ->orWhereJsonContains('partilha', $currentUserId);
            });
        }

        if ($request->has('entity_id') && $request->entity_id) {
            $query->where('entity_id', $request->entity_id);
        }

        if ($request->has('start') && $request->has('end')) {
            $query->betweenDates($request->start, $request->end);
        }

        $events = $query->get();

        $formatted = $events->map(function ($event) use ($currentUserId) {
            $isOwner = $event->user_id === $currentUserId;

            $start = $event->data->copy()->setTimeFromTimeString($event->hora);
            $end = $start->copy()->addMinutes($event->duracao);

            $backgroundColor = $event->calendarType->cor ?? 'blue';
            if (!$isOwner) {
                $backgroundColor = 'lightblue';
            }

            return [
                'id' => $event->id,
                'title' => $event->titulo . ($isOwner ? '' : ' (' . $event->user->name . ')'),
                'start' => $start->toIso8601String(),
                'end' => $end->toIso8601String(),
                'backgroundColor' => $backgroundColor,
                'borderColor' => $event->calendarType->cor ?? 'blue',
                'extendedProps' => [
                    'descricao' => $event->descricao,
                    'conhecimento' => $event->conhecimento,
                    'entity' => $event->entity?->nome,
                    'type' => $event->calendarType?->nome,
                    'action' => $event->calendarAction?->nome,
                    'user' => $event->user?->name,
                    'duracao' => $event->duracao,
                    'isOwner' => $isOwner,
                ],
            ];
        });

        return response()->json($formatted);
    }

    public function show(CalendarEvent $calendarEvent)
    {
        $userId = auth()->id();
        $canView = $calendarEvent->user_id === $userId ||
            in_array($userId, $calendarEvent->partilha ?? []);

        if (!$canView) {
            abort(403, 'Não tem permissão para ver este evento.');
        }

        $calendarEvent->load(['user', 'entity', 'calendarType', 'calendarAction']);

        return response()->json([
            'id' => $calendarEvent->id,
            'titulo' => $calendarEvent->titulo,
            'data' => $calendarEvent->data->format('Y-m-d'),
            'hora' => substr($calendarEvent->hora, 0, 5),
            'duracao' => $calendarEvent->duracao,
            'entity_id' => $calendarEvent->entity_id,
            'calendar_type_id' => $calendarEvent->calendar_type_id,
            'calendar_action_id' => $calendarEvent->calendar_action_id,
            'descricao' => $calendarEvent->descricao,
            'conhecimento' => $calendarEvent->conhecimento,
            'partilha' => $calendarEvent->partilha ?? [],
            'estado' => $calendarEvent->estado,
            'user_id' => $calendarEvent->user_id,
            'isOwner' => $calendarEvent->user_id === $userId,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'data_evento' => ['required', 'date'],
            'hora' => ['required'],
            'duracao' => ['required', 'integer', 'min:1'],
            'partilha' => ['nullable', 'array'],
            'partilha.*' => ['exists:users,id'],
            'conhecimento' => ['nullable', 'string'],
            'descricao' => ['nullable', 'string'],
            'entity_id' => ['nullable', 'exists:entities,id'],
            'calendar_type_id' => ['nullable', 'exists:calendar_types,id'],
            'calendar_action_id' => ['nullable', 'exists:calendar_actions,id'],
            'estado' => ['required', Rule::in(['ativo', 'inativo'])],
        ]);

        $validated['data'] = $validated['data_evento'];
        unset($validated['data_evento']);
        $validated['user_id'] = auth()->id();

        if (strlen($validated['hora']) > 5) {
            $validated['hora'] = substr($validated['hora'], 0, 5);
        }

        CalendarEvent::create($validated);

        return back()->with('success', 'Evento criado com sucesso!');
    }

    public function update(Request $request, CalendarEvent $calendarEvent)
    {
        if ($calendarEvent->user_id !== auth()->id()) {
            abort(403, 'Não tem permissão para editar este evento.');
        }

        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'data_evento' => ['required', 'date'],
            'hora' => ['required'],
            'duracao' => ['required', 'integer', 'min:1'],
            'partilha' => ['nullable', 'array'],
            'partilha.*' => ['exists:users,id'],
            'conhecimento' => ['nullable', 'string'],
            'descricao' => ['nullable', 'string'],
            'entity_id' => ['nullable', 'exists:entities,id'],
            'calendar_type_id' => ['nullable', 'exists:calendar_types,id'],
            'calendar_action_id' => ['nullable', 'exists:calendar_actions,id'],
            'estado' => ['required', Rule::in(['ativo', 'inativo'])],
        ]);

        $validated['data'] = $validated['data_evento'];
        unset($validated['data_evento']);

        if (strlen($validated['hora']) > 5) {
            $validated['hora'] = substr($validated['hora'], 0, 5);
        }

        $calendarEvent->update($validated);

        return back()->with('success', 'Evento atualizado com sucesso!');
    }

    public function destroy(CalendarEvent $calendarEvent)
    {
        if ($calendarEvent->user_id !== auth()->id()) {
            abort(403, 'Não tem permissão para eliminar este evento.');
        }

        $calendarEvent->delete();

        return back()->with('success', 'Evento eliminado com sucesso!');
    }
}
