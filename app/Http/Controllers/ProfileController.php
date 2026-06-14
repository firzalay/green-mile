<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the participant's profile.
     */
    public function show(Request $request): View
    {
        $user = $request->user();

        // 1. Calculate statistics
        $totalPoints = $user->eventParticipants()->sum('current_event_points');
        $eventsJoined = $user->eventParticipants()->count();
        $eventsCompleted = $user->eventParticipants()->where('status', 'completed')->count();
        $checkpointsScanned = $user->checkpointScans()->count();

        // 2. Calculate global rank among participants
        $rankings = User::where('role', 'participant')
            ->withSum('eventParticipants as total_earned', 'current_event_points')
            ->get()
            ->sortByDesc('total_earned')
            ->values();

        $rankIndex = $rankings->search(fn ($u) => $u->id === $user->id);
        $currentRank = $rankIndex !== false ? $rankIndex + 1 : '-';

        // 3. Calculate best event leaderboard rank
        $bestRankVal = null;
        $participations = $user->eventParticipants()->with('event')->get();
        foreach ($participations as $participation) {
            $event = $participation->event;
            if ($event) {
                $leaderboard = $event->leaderboard()->get();
                $rankIndexInEvent = $leaderboard->search(fn ($ep) => $ep->user_id === $user->id);
                if ($rankIndexInEvent !== false) {
                    $rankInEvent = $rankIndexInEvent + 1;
                    if ($bestRankVal === null || $rankInEvent < $bestRankVal) {
                        $bestRankVal = $rankInEvent;
                    }
                }
            }
        }
        $bestRank = $bestRankVal !== null ? '#'.$bestRankVal : '-';

        // 4. Calculate total rewards redeemed
        $rewardsRedeemed = $user->rewardRedemptions()->count();

        // 5. Get recent activities
        $activities = $user->activities()
            ->with('event')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('participant.profile.show', compact(
            'user',
            'totalPoints',
            'eventsJoined',
            'eventsCompleted',
            'checkpointsScanned',
            'currentRank',
            'bestRank',
            'rewardsRedeemed',
            'activities'
        ));
    }

    /**
     * Show the edit profile page.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        return view('participant.profile.edit', compact('user'));
    }

    /**
     * Update the profile info and picture.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                $oldPath = str_replace('/storage/', '', $user->avatar);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = '/storage/'.$path;
        }

        $user->update($validated);

        return redirect()->route('profile.show')
            ->with('success', 'Profil Anda berhasil diperbarui.');
    }

    /**
     * Update the user password.
     */
    public function updatePassword(ChangePasswordRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Password Anda berhasil diperbarui.');
    }
}
