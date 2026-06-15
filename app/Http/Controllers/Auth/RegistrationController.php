<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    /**
     * Show the role selection page.
     */
    public function selectRole(Request $request): View|RedirectResponse
    {
        if (auth()->check()) {
            if (auth()->user()->isSuperAdmin()) {
                return redirect()->route('admin.organizers.index');
            }

            return auth()->user()->isOrganizer()
                ? redirect()->route('organizer.dashboard')
                : redirect()->route('dashboard');
        }

        return view('auth.register-select-role');
    }
}
