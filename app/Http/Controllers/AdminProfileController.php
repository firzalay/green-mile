<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminProfileController extends Controller
{
    /**
     * Show the admin's profile.
     */
    public function show(Request $request): View
    {
        $user = $request->user();

        // Calculate statistics based on organizers approved/rejected by this admin
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
