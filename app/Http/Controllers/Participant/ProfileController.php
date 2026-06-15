<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
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
        $stats = $user->participantStats();

        // Calculate global rank among participants
        $rankings = User::where('role', 'participant')
            ->withSum('eventParticipants as total_earned', 'current_event_points')
            ->get()
            ->sortByDesc('total_earned')
            ->values();

        $rankIndex = $rankings->search(fn ($u) => $u->id === $user->id);
        $currentRank = $rankIndex !== false ? $rankIndex + 1 : '-';

        // Calculate best event leaderboard rank
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

        $activities = $user->activities()
            ->with('event')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('participant.profile.show', [
            'user' => $user,
            'totalPoints' => $stats['total_points'],
            'eventsJoined' => $stats['events_joined'],
            'eventsCompleted' => $stats['events_completed'],
            'checkpointsScanned' => $stats['checkpoints_scanned'],
            'rewardsRedeemed' => $stats['rewards_redeemed'],
            'currentRank' => $currentRank,
            'bestRank' => $bestRank,
            'activities' => $activities,
        ]);
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
            $rawAvatar = $user->getRawOriginal('avatar');
            if ($rawAvatar && ! filter_var($rawAvatar, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($rawAvatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
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
