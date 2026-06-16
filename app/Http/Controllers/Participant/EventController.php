<?php

namespace App\Http\Controllers\Participant;

use App\Actions\Event\JoinEventAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Event\JoinEventRequest;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
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
            ->where('status', '!=', 'draft')
            ->withCount('participants')
            ->orderBy('start_date')
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
            ->where(function ($query) use ($user) {
                $query->where('status', '!=', 'draft')
                    ->orWhere('organizer_id', $user->id)
                    ->orWhereHas('participants', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
            })
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

    /**
     * Show the event join form.
     */
    public function showJoinForm(Request $request): View
    {
        return view('events.join', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Join an event using a join code.
     */
    public function joinWithCode(JoinEventRequest $request, JoinEventAction $action): RedirectResponse
    {
        try {
            $event = $action->execute($request->user(), $request->input('join_code'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('events.show', $event->id)
            ->with('success', "Berhasil bergabung ke {$event->name}");
    }
}
