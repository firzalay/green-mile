<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['checkpoint_id', 'rank_start', 'rank_end', 'bonus_percentage'])]
class CheckpointBonusTier extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rank_start' => 'integer',
            'rank_end' => 'integer',
            'bonus_percentage' => 'float',
        ];
    }

    /**
     * Get the checkpoint that owns this tier.
     */
    public function checkpoint(): BelongsTo
    {
        return $this->belongsTo(Checkpoint::class);
    }
}
