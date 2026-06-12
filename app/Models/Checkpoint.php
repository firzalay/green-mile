<?php

namespace App\Models;

use Database\Factories\CheckpointFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['event_id', 'name', 'location', 'points'])]
class Checkpoint extends Model
{
    /** @use HasFactory<CheckpointFactory> */
    use HasFactory;

    /**
     * Get the event that owns this checkpoint.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
