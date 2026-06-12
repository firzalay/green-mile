<?php

namespace App\Models;

use Database\Factories\EventParticipantFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['event_id', 'user_id', 'completed_checkpoints', 'current_event_points', 'total_points', 'rank'])]
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
}
