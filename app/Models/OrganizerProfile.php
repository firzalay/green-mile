<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'organization_name', 'contact_person', 'phone', 'description', 'website'])]
class OrganizerProfile extends Model
{
    /**
     * Get the user that owns the organizer profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
