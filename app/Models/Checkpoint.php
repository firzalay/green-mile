<?php

namespace App\Models;

use Database\Factories\CheckpointFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['event_id', 'name', 'location', 'description', 'sequence', 'points', 'status'])]
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
            'points' => 'integer',
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
     * Get the event that owns this checkpoint.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
