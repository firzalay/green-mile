<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Display a listing of all active events.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $events = Event::where('is_active', true)
            ->withCount('participants')
            ->orderBy('event_date')
            ->get();

        $joinedEventIds = $user->eventParticipants()
            ->pluck('event_id')
            ->toArray();

        return view('events.index', [
            'user' => $user,
            'events' => $events,
            'joinedEventIds' => $joinedEventIds,
        ]);
    }

    /**
     * Display the specified event.
     */
    public function show(Request $request, int $id): View
    {
        $user = $request->user();

        $event = Event::where('is_active', true)
            ->withCount('participants')
            ->findOrFail($id);

        $isJoined = $user->eventParticipants()
            ->where('event_id', $event->id)
            ->exists();

        return view('events.show', [
            'user' => $user,
            'event' => $event,
            'isJoined' => $isJoined,
        ]);
    }
}
