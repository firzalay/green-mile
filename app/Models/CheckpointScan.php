<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'event_id', 'checkpoint_id', 'base_point', 'bonus_point', 'total_point', 'points_awarded', 'scanned_at'])]
class CheckpointScan extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'base_point' => 'integer',
            'bonus_point' => 'integer',
            'total_point' => 'integer',
            'points_awarded' => 'integer',
            'scanned_at' => 'datetime',
        ];
    }

    /**
     * The booted method of the CheckpointScan model.
     */
    protected static function booted(): void
    {
        static::saving(function (CheckpointScan $scan) {
            if ($scan->isDirty('points_awarded') && ! $scan->isDirty('total_point')) {
                $scan->total_point = $scan->points_awarded;
            } elseif ($scan->isDirty('total_point') && ! $scan->isDirty('points_awarded')) {
                $scan->points_awarded = $scan->total_point;
            }
        });
    }

    /**
     * Get the user that made the scan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the event associated with the scan.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the checkpoint associated with the scan.
     */
    public function checkpoint(): BelongsTo
    {
        return $this->belongsTo(Checkpoint::class);
    }
}
