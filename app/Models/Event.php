<?php

namespace App\Models;

use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['name', 'location', 'start_date', 'end_date', 'total_checkpoints', 'is_active', 'banner', 'description', 'total_rewards', 'max_points', 'organizer_id', 'max_participants', 'status', 'user_id', 'join_code', 'point_pool', 'remaining_point_pool'])]
class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory;

    /**
     * Get all rewards for this event.
     */
    public function rewards(): HasMany
    {
        return $this->hasMany(Reward::class);
    }

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
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
            'point_pool' => 'integer',
            'remaining_point_pool' => 'integer',
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
     * Get the dynamic event status based on database status column.
     * Maps to standard strings for backward compatibility with existing views.
     */
    public function getStatusAttribute(): string
    {
        $status = $this->attributes['status'] ?? 'draft';

        return match ($status) {
            'draft' => 'Draft',
            'published' => 'Upcoming',
            'ongoing' => 'Ongoing',
            'finished' => 'Finished',
            default => ucfirst($status),
        };
    }

    /**
     * Check if the event is draft.
     */
    public function isDraft(): bool
    {
        return ($this->attributes['status'] ?? 'draft') === 'draft';
    }

    /**
     * Check if the event is published.
     */
    public function isPublished(): bool
    {
        return ($this->attributes['status'] ?? 'draft') === 'published';
    }

    /**
     * Accessor for backward compatibility with event_date.
     */
    public function getEventDateAttribute(): ?Carbon
    {
        return $this->start_date;
    }

    /**
     * Get the banner URL.
     */
    public function getBannerAttribute(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        $path = ltrim($value, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }

        return Storage::disk('public')->url($path);
    }

    /**
     * Accessor for backward compatibility with banner_image.
     */
    public function getBannerImageAttribute(): ?string
    {
        return $this->banner;
    }

    /**
     * Accessor for backward compatibility with user_id.
     */
    public function getUserIdAttribute(): ?int
    {
        return $this->organizer_id;
    }

    /**
     * Mutator for backward compatibility with user_id.
     */
    public function setUserIdAttribute($value): void
    {
        $this->attributes['organizer_id'] = $value;
        unset($this->attributes['user_id']);
    }

    /**
     * Get the organizer of this event.
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
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

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Event $event) {
            if (empty($event->join_code)) {
                $event->join_code = static::generateUniqueJoinCode();
            }
        });
    }

    /**
     * Generate a unique event join code.
     */
    public static function generateUniqueJoinCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (static::where('join_code', $code)->exists());

        return $code;
    }
}
