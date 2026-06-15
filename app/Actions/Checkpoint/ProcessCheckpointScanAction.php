<?php

namespace App\Actions\Checkpoint;

use App\Models\Activity;
use App\Models\Checkpoint;
use App\Models\CheckpointScan;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProcessCheckpointScanAction
{
    /**
     * @return array{checkpoint_name: string, points_awarded: int, total_points: int}
     *
     * @throws \Exception
     */
    public function execute(User $user, string $qrToken): array
    {
        $checkpoint = Checkpoint::with('event')->where('qr_token', $qrToken)->first();

        if (! $checkpoint) {
            throw new \Exception('QR Code tidak valid.');
        }

        if (strtolower($checkpoint->status) !== 'active') {
            throw new \Exception('Checkpoint tidak aktif.');
        }

        $event = $checkpoint->event;

        if (strtolower($event->getRawOriginal('status') ?? '') !== 'ongoing') {
            throw new \Exception('Event tidak sedang berlangsung.');
        }

        $participant = EventParticipant::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $participant) {
            throw new \Exception('Anda belum terdaftar pada event ini.');
        }

        $alreadyScanned = CheckpointScan::where('user_id', $user->id)
            ->where('checkpoint_id', $checkpoint->id)
            ->exists();

        if ($alreadyScanned) {
            throw new \Exception('Checkpoint ini sudah pernah Anda scan.');
        }

        if ($participant->current_event_points + $checkpoint->points > $event->max_points) {
            throw new \Exception(
                'Batas maksimal poin per peserta untuk event ini adalah '.number_format($event->max_points).' poin.'
            );
        }

        DB::transaction(function () use ($user, $event, $checkpoint, $participant) {
            $lockedEvent = Event::where('id', $event->id)->lockForUpdate()->first();

            if ($lockedEvent->remaining_point_pool < $checkpoint->points) {
                throw new \Exception('Poin event telah habis.');
            }

            CheckpointScan::create([
                'user_id' => $user->id,
                'event_id' => $lockedEvent->id,
                'checkpoint_id' => $checkpoint->id,
                'points_awarded' => $checkpoint->points,
                'scanned_at' => now(),
            ]);

            $participant->increment('completed_checkpoints');
            $participant->increment('current_event_points', $checkpoint->points);
            $participant->increment('total_points', $checkpoint->points);

            Activity::create([
                'user_id' => $user->id,
                'event_id' => $lockedEvent->id,
                'activity_type' => 'scan_checkpoint',
                'description' => 'berhasil scan '.$checkpoint->name,
                'points' => $checkpoint->points,
            ]);

            $lockedEvent->decrement('remaining_point_pool', $checkpoint->points);
        });

        return [
            'checkpoint_name' => $checkpoint->name,
            'points_awarded' => $checkpoint->points,
            'total_points' => $participant->fresh()->current_event_points,
        ];
    }
}
