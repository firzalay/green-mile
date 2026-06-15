<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the admin's profile.
     */
    public function show(Request $request): View
    {
        $user = $request->user();

        $approvedCount = $user->approvedOrganizers()->where('status', 'approved')->count();
        $rejectedCount = $user->approvedOrganizers()->where('status', 'rejected')->count();
        $totalReviews = $user->approvedOrganizers()->count();

        return view('admin.profile.show', compact(
            'user',
            'approvedCount',
            'rejectedCount',
            'totalReviews'
        ));
    }

    /**
     * Show the edit profile page.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        return view('admin.profile.edit', compact('user'));
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

        return redirect()->route('admin.profile.show')
            ->with('success', 'Profil admin berhasil diperbarui.');
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

        return redirect()->route('admin.profile.show')
            ->with('success', 'Password Anda berhasil diperbarui.');
    }
}
