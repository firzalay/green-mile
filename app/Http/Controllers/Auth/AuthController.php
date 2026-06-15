<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            if (auth()->user()->isSuperAdmin()) {
                return redirect()->route('admin.organizers.index');
            }

            return auth()->user()->isOrganizer()
                ? redirect()->route('organizer.dashboard')
                : redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle a login request.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if ($user->role === 'organizer') {
                if ($user->status === 'pending') {
                    Auth::logout();

                    return back()
                        ->withInput($request->only('email'))
                        ->with('error', 'Akun Anda masih menunggu persetujuan dari Super Admin.');
                }

                if ($user->status === 'rejected') {
                    Auth::logout();

                    return back()
                        ->withInput($request->only('email'))
                        ->with('error', 'Pendaftaran organizer Anda belum dapat disetujui. Silakan hubungi tim Jejak Hijau untuk informasi lebih lanjut.');
                }
            }

            $request->session()->regenerate();

            if ($user->isSuperAdmin()) {
                return redirect()->intended(route('admin.organizers.index'));
            }

            return $user->isOrganizer()
                ? redirect()->intended(route('organizer.dashboard'))
                : redirect()->intended(route('dashboard'));
        }

        return back()
            ->withInput($request->only('email'))
            ->with('error', 'Email atau password yang kamu masukkan tidak valid.');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
