<?php

namespace App\Models;

use Database\Factories\RewardFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description', 'image', 'required_points', 'stock', 'is_active', 'event_id', 'total_redeemed'])]
class Reward extends Model
{
    /** @use HasFactory<RewardFactory> */
    use HasFactory;

    /**
     * Get the event that owns this reward.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get all redemptions for this reward.
     */
    public function redemptions(): HasMany
    {
        return $this->hasMany(RewardRedemption::class);
    }
}
