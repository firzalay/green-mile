<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventParticipant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ParticipantEventController extends Controller
{
    /**
     * Join the specified event.
     */
    public function join(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();

        $event = Event::where('is_active', true)->findOrFail($id);

        $alreadyJoined = $user->eventParticipants()
            ->where('event_id', $event->id)
            ->exists();

        if ($alreadyJoined) {
            return redirect()->route('events.show', $event->id)
                ->with('error', 'Kamu sudah bergabung dalam event ini.');
        }

        EventParticipant::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'completed_checkpoints' => 0,
            'current_event_points' => 0,
            'total_points' => 0,
            'joined_at' => now(),
            'status' => 'joined',
        ]);

        return redirect()->route('events.show', $event->id)
            ->with('success', "Berhasil bergabung ke {$event->name}");
    }
}
