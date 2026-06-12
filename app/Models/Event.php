<?php

namespace App\Models;

use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'location', 'event_date', 'total_checkpoints', 'is_active', 'banner_image', 'description', 'total_rewards', 'max_points', 'user_id'])]
class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['status'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get all participants for this event.
     */
    public function participants(): HasMany
    {
        return $this->hasMany(EventParticipant::class);
    }

    /**
     * Get top participants ordered by points for leaderboard.
     *
     * @return HasMany<EventParticipant>
     */
    public function leaderboard(): HasMany
    {
        return $this->hasMany(EventParticipant::class)
            ->with('user')
            ->orderByDesc('current_event_points')
            ->orderBy('completed_checkpoints');
    }

    /**
     * Get the dynamic event status based on event_date.
     */
    public function getStatusAttribute(): string
    {
        if (! $this->event_date) {
            return 'Upcoming';
        }

        $today = now()->startOfDay();
        $eventDate = $this->event_date->startOfDay();

        if ($eventDate->isFuture()) {
            return 'Upcoming';
        }

        if ($eventDate->isToday()) {
            return 'Ongoing';
        }

        return 'Finished';
    }

    /**
     * Get the organizer of this event.
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all checkpoints for this event.
     */
    public function checkpoints(): HasMany
    {
        return $this->hasMany(Checkpoint::class);
    }

    /**
     * Get all activities related to this event.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
}
