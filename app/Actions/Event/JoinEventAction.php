<?php

namespace App\Actions\Event;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\User;

class JoinEventAction
{
    /**
     * Join a user to an event using its join code.
     *
     * @throws \Exception
     */
    public function execute(User $user, string $joinCode): Event
    {
        $event = Event::where('join_code', strtoupper($joinCode))->first();

        if (! $event) {
            throw new \Exception('Kode event tidak ditemukan.');
        }

        if (! $event->is_active || $event->status === 'Finished') {
            throw new \Exception('Event sudah berakhir dan tidak menerima peserta baru.');
        }

        $alreadyJoined = $user->eventParticipants()
            ->where('event_id', $event->id)
            ->exists();

        if ($alreadyJoined) {
            throw new \Exception('Anda sudah terdaftar pada event ini.');
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

        return $event;
    }
}
