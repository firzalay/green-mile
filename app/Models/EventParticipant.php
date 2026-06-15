<?php

namespace App\Models;

use Database\Factories\EventParticipantFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['event_id', 'user_id', 'completed_checkpoints', 'current_event_points', 'total_points', 'rank', 'joined_at', 'status'])]
class EventParticipant extends Model
{
    /** @use HasFactory<EventParticipantFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'completed_checkpoints' => 'integer',
            'current_event_points' => 'integer',
            'total_points' => 'integer',
            'rank' => 'integer',
            'joined_at' => 'datetime',
        ];
    }

    /**
     * Get the event this participation belongs to.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user this participation belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate the checkpoint progress percentage.
     */
    public function checkpointProgressPercentage(): float
    {
        if ($this->event->total_checkpoints === 0) {
            return 0;
        }

        return round(($this->completed_checkpoints / $this->event->total_checkpoints) * 100);
    }

    /**
     * Get the participant rank, falling back to dynamic calculation if null.
     */
    public function getRankAttribute($value): ?int
    {
        if ($value !== null) {
            return $value;
        }

        return self::where('event_id', $this->event_id)
            ->where('current_event_points', '>', $this->current_event_points)
            ->count() + 1;
    }

    /**
     * Get all participants for an event ranked by leaderboard rules.
     * Tie-breaker: total_points DESC, total_scans DESC, last_scan_at ASC, joined_at ASC.
     *
     * @return Collection<int, self>
     */
    public static function rankedLeaderboard(int $eventId): Collection
    {
        return self::where('event_id', $eventId)
            ->with('user')
            ->select('event_participants.*')
            ->selectSub(function ($q) {
                $q->selectRaw('count(*)')
                    ->from('checkpoint_scans')
                    ->whereColumn('checkpoint_scans.user_id', 'event_participants.user_id')
                    ->whereColumn('checkpoint_scans.event_id', 'event_participants.event_id');
            }, 'total_scans')
            ->selectSub(function ($q) {
                $q->selectRaw('max(scanned_at)')
                    ->from('checkpoint_scans')
                    ->whereColumn('checkpoint_scans.user_id', 'event_participants.user_id')
                    ->whereColumn('checkpoint_scans.event_id', 'event_participants.event_id');
            }, 'last_scan_at')
            ->orderByDesc('total_points')
            ->orderByDesc('total_scans')
            ->orderByRaw('CASE WHEN last_scan_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('last_scan_at')
            ->orderBy('joined_at')
            ->get()
            ->map(function ($participant, $index) {
                $participant->computed_rank = $index + 1;

                return $participant;
            });
    }
}
