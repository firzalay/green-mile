<?php

namespace App\Models;

use Database\Factories\CheckpointFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['event_id', 'name', 'location', 'description', 'sequence', 'point', 'points', 'status', 'qr_token', 'is_custom_point'])]
class Checkpoint extends Model
{
    /** @use HasFactory<CheckpointFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sequence' => 'integer',
            'point' => 'integer',
            'points' => 'integer',
            'is_custom_point' => 'boolean',
        ];
    }

    /**
     * Accessor for status to return title-case (Active/Inactive).
     */
    public function getStatusAttribute($value): string
    {
        return ucfirst($value ?? 'active');
    }

    /**
     * Mutator for status to store lowercase in the database.
     */
    public function setStatusAttribute($value): void
    {
        $this->attributes['status'] = strtolower($value);
    }

    /**
     * The booted method of the Checkpoint model.
     */
    protected static function booted(): void
    {
        static::saving(function (Checkpoint $checkpoint) {
            if ($checkpoint->isDirty('points') && ! $checkpoint->isDirty('point')) {
                $checkpoint->point = $checkpoint->points;
            } elseif ($checkpoint->isDirty('point') && ! $checkpoint->isDirty('points')) {
                $checkpoint->points = $checkpoint->point;
            }
        });

        static::saved(function (Checkpoint $checkpoint) {
            $event = $checkpoint->event;
            if ($event) {
                $event->updateQuietly([
                    'total_checkpoints' => $event->checkpoints()->count(),
                ]);
                $event->distributePointsAutomatically();
            }
        });

        static::deleted(function (Checkpoint $checkpoint) {
            $event = $checkpoint->event;
            if ($event) {
                $event->updateQuietly([
                    'total_checkpoints' => $event->checkpoints()->count(),
                ]);
                $event->distributePointsAutomatically();
            }
        });
    }

    /**
     * Get the event that owns this checkpoint.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the scans for this checkpoint.
     */
    public function scans(): HasMany
    {
        return $this->hasMany(CheckpointScan::class);
    }

    /**
     * Get the bonus tiers for this checkpoint.
     */
    public function bonusTiers(): HasMany
    {
        return $this->hasMany(CheckpointBonusTier::class);
    }
}
