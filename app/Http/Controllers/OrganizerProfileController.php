<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizerProfile\UpdateOrganizerProfileRequest;
use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Models\EventParticipant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OrganizerProfileController extends Controller
{
    /**
     * Show the organizer's profile.
     */
    public function show(Request $request): View
    {
        $user = $request->user()->load('organizerProfile');

        // Calculate statistics
        $totalEvents = $user->events()->count();
        $activeEvents = $user->events()->where('is_active', true)->count();

        $eventIds = $user->events()->pluck('id')->toArray();
        $totalParticipants = EventParticipant::whereIn('event_id', $eventIds)
            ->distinct('user_id')
            ->count('user_id');

        return view('organizer.profile.show', compact(
            'user',
            'totalEvents',
            'activeEvents',
            'totalParticipants'
        ));
    }

    /**
     * Show the edit profile page.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load('organizerProfile');

        return view('organizer.profile.edit', compact('user'));
    }

    /**
     * Update the profile info and picture.
     */
    public function update(UpdateOrganizerProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Handle user account updates
        $userData = [
            'name' => $validated['name'],
            'username' => $validated['username'],
        ];

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                $oldPath = str_replace('/storage/', '', $user->avatar);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $userData['avatar'] = '/storage/'.$path;
        }

        $user->update($userData);

        // Handle organizer profile updates
        $profileData = [
            'organization_name' => $validated['organization_name'],
            'contact_person' => $validated['contact_person'],
            'phone' => $validated['phone'],
            'description' => $validated['description'] ?? null,
            'website' => $validated['website'] ?? null,
        ];

        $user->organizerProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return redirect()->route('organizer.profile.show')
            ->with('success', 'Profil organisasi berhasil diperbarui.');
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

        return redirect()->route('organizer.profile.show')
            ->with('success', 'Password Anda berhasil diperbarui.');
    }
}
