<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ParticipantRegistrationController extends Controller
{
    /**
     * Show the participant registration form.
     */
    public function create(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return auth()->user()->isOrganizer()
                ? redirect()->route('organizer.dashboard')
                : redirect()->route('dashboard');
        }

        return view('auth.register-participant');
    }

    /**
     * Handle participant registration.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'participant',
            'status' => 'active',
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
