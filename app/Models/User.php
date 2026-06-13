<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'username', 'status', 'approved_by', 'approved_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Determine if the user has the organizer role.
     */
    public function isOrganizer(): bool
    {
        return $this->role === 'organizer';
    }

    /**
     * Determine if the user has the super admin role.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Get all event participations for this user.
     */
    public function eventParticipants(): HasMany
    {
        return $this->hasMany(EventParticipant::class);
    }

    /**
     * Get all events created by this user (organizer).
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'organizer_id');
    }

    /**
     * Get all activities of this user.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get all checkpoint scans of this user.
     */
    public function checkpointScans(): HasMany
    {
        return $this->hasMany(CheckpointScan::class);
    }

    /**
     * Get the organizer profile associated with the user.
     */
    public function organizerProfile(): HasOne
    {
        return $this->hasOne(OrganizerProfile::class);
    }

    /**
     * Get the super admin who approved/rejected this user.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get all organizer users approved/rejected by this super admin.
     */
    public function approvedOrganizers(): HasMany
    {
        return $this->hasMany(User::class, 'approved_by');
    }

    /**
     * Get all reward redemptions for this user.
     */
    public function rewardRedemptions(): HasMany
    {
        return $this->hasMany(RewardRedemption::class);
    }

    /**
     * Calculate user's available points dynamically.
     */
    public function getPointsAttribute(): int
    {
        $totalPoints = $this->eventParticipants()->sum('current_event_points');
        $redeemedPoints = $this->rewardRedemptions()->sum('points_used');

        return max(0, $totalPoints - $redeemedPoints);
    }
}
