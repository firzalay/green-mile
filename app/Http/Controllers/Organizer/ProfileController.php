<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizerProfile\UpdateOrganizerProfileRequest;
use App\Http\Requests\Profile\ChangePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the organizer's profile.
     */
    public function show(Request $request): View
    {
        $user = $request->user()->load('organizerProfile');
        $stats = $user->organizerStats();

        return view('organizer.profile.show', [
            'user' => $user,
            'totalEvents' => $stats['total_events'],
            'activeEvents' => $stats['active_events'],
            'totalParticipants' => $stats['total_participants'],
        ]);
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

        $userData = [
            'name' => $validated['name'],
            'username' => $validated['username'],
        ];

        if ($request->hasFile('avatar')) {
            $rawAvatar = $user->getRawOriginal('avatar');
            if ($rawAvatar && ! filter_var($rawAvatar, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($rawAvatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $userData['avatar'] = $path;
        }

        $user->update($userData);

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
