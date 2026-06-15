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

        $pointsAwarded = 0;

        DB::transaction(function () use ($user, $event, $checkpoint, $participant, &$pointsAwarded) {
            $lockedEvent = Event::where('id', $event->id)->lockForUpdate()->first();
            $lockedCheckpoint = Checkpoint::with('bonusTiers')->where('id', $checkpoint->id)->lockForUpdate()->first();

            $scanCount = CheckpointScan::where('checkpoint_id', $lockedCheckpoint->id)->count();
            $rank = $scanCount + 1;

            $bonusTiers = $lockedCheckpoint->bonusTiers;
            $useTiers = $bonusTiers->isNotEmpty();

            $basePoint = (int) $lockedCheckpoint->point;
            $tierPoint = 0;

            if ($useTiers) {
                $matchingTier = null;
                foreach ($bonusTiers as $tier) {
                    $minRank = (int) ($tier->rank_start ?? 1);
                    $maxRank = $tier->rank_end !== null ? (int) $tier->rank_end : null;

                    if ($rank >= $minRank && ($maxRank === null || $rank <= $maxRank)) {
                        $matchingTier = $tier;
                        break;
                    }
                }

                if ($matchingTier) {
                    $percentage = (float) ($matchingTier->bonus_percentage ?? 0);
                    $tierPoint = (int) floor($basePoint * ($percentage / 100));
                }
            }

            $totalPoint = $useTiers ? $tierPoint : $basePoint;
            $pointsAwarded = $totalPoint;

            CheckpointScan::create([
                'user_id' => $user->id,
                'event_id' => $lockedEvent->id,
                'checkpoint_id' => $lockedCheckpoint->id,
                'base_point' => $basePoint,
                'bonus_point' => $useTiers ? $tierPoint : 0,
                'total_point' => $totalPoint,
                'scanned_at' => now(),
            ]);

            $participant->increment('completed_checkpoints');
            $participant->increment('current_event_points', $totalPoint);
            $participant->increment('total_points', $totalPoint);

            Activity::create([
                'user_id' => $user->id,
                'event_id' => $lockedEvent->id,
                'activity_type' => 'scan_checkpoint',
                'description' => 'berhasil scan '.$lockedCheckpoint->name,
                'points' => $totalPoint,
            ]);
        });

        return [
            'checkpoint_name' => $checkpoint->name,
            'points_awarded' => $pointsAwarded,
            'total_points' => $participant->fresh()->current_event_points,
        ];
    }
}
