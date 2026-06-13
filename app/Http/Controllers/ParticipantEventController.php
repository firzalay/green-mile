<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventParticipant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParticipantEventController extends Controller
{
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
    public function joinWithCode(Request $request): RedirectResponse
    {
        $request->validate([
            'join_code' => ['required', 'string', 'max:12'],
        ]);

        $user = $request->user();
        $joinCode = strtoupper($request->input('join_code'));

        $event = Event::where('join_code', $joinCode)->first();

        if (! $event) {
            return back()->with('error', 'Kode event tidak ditemukan.')->withInput();
        }

        if (! $event->is_active || $event->status === 'Finished') {
            return back()->with('error', 'Event sudah berakhir dan tidak menerima peserta baru.')->withInput();
        }

        $alreadyJoined = $user->eventParticipants()
            ->where('event_id', $event->id)
            ->exists();

        if ($alreadyJoined) {
            return back()->with('error', 'Anda sudah terdaftar pada event ini.')->withInput();
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
