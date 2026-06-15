<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\OrganizerRegisterRequest;
use App\Models\OrganizerProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class OrganizerRegistrationController extends Controller
{
    /**
     * Show the organizer registration form.
     */
    public function create(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            if (auth()->user()->isSuperAdmin()) {
                return redirect()->route('admin.organizers.index');
            }

            return auth()->user()->isOrganizer()
                ? redirect()->route('organizer.dashboard')
                : redirect()->route('dashboard');
        }

        return view('auth.register-organizer');
    }

    /**
     * Handle organizer registration.
     */
    public function store(OrganizerRegisterRequest $request): View|RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->contact_person,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'organizer',
                'status' => 'pending',
            ]);

            OrganizerProfile::create([
                'user_id' => $user->id,
                'organization_name' => $request->organization_name,
                'contact_person' => $request->contact_person,
                'phone' => $request->phone,
            ]);
        });

        return view('auth.register-organizer-success');
    }
}
