<?php

namespace App\Http\Controllers;

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
            return auth()->user()->isOrganizer()
                ? redirect()->route('organizer.dashboard')
                : redirect()->route('dashboard');
        }

        return view('auth.register-organizer');
    }

    /**
     * Handle organizer registration.
     */
    public function store(Request $request): View|RedirectResponse
    {
        $request->validate([
            'organization_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

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
