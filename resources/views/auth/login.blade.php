<x-auth-layout title="Masuk – Jejak Hijau">

    {{-- Heading --}}
    <div class="mb-8 animate-fade-in-up">
        <x-app-badge label="GreenRun Platform" />
        <h2 class="mt-4 font-bold leading-tight" style="font-size: 28px; color: #111827;">
            Selamat Datang Kembali
        </h2>
        <p class="mt-1.5 text-sm" style="color: #6B7280;">
            Masuk untuk melanjutkan perjalanan hijaumu.
        </p>
    </div>

    {{-- Flash error message --}}
    @if (session('error'))
        <div class="mb-5 flex items-start gap-3 px-4 py-3 rounded-xl animate-fade-in-up"
             style="background: #FEF2F2; border: 1px solid #FCA5A5;">
            <svg class="shrink-0 mt-0.5" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="10" stroke="#EF4444" stroke-width="2"/>
                <path d="M12 8v4M12 16h.01" stroke="#EF4444" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <p class="text-sm font-medium" style="color: #DC2626;">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Login Form --}}
    <form id="login-form" method="POST" action="{{ route('login') }}" class="space-y-5 animate-fade-in-up animate-delay-100" novalidate>
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-semibold mb-1.5" style="color: #374151;">
                Email
            </label>
            <x-input
                id="email"
                name="email"
                type="email"
                placeholder="nama@email.com"
                :value="old('email')"
                :required="true"
                :autofocus="true">
                <x-slot name="icon">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="2" y="4" width="20" height="16" rx="3" stroke="currentColor" stroke-width="2"/>
                        <path d="m2 7 8.586 5.657a2 2 0 0 0 2.828 0L22 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </x-slot>
            </x-input>
        </div>

        {{-- Password --}}
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-sm font-semibold" style="color: #374151;">
                    Password
                </label>
                <a href="{{ route('password.request') }}"
                   class="text-xs font-medium hover:underline transition-colors"
                   style="color: #2ECF89;">
                    Forgot Password?
                </a>
            </div>
            <x-password-input
                id="password"
                name="password"
                placeholder="Masukkan password"
                :required="true"
            />
        </div>

        {{-- Submit button --}}
        <div class="pt-2">
            <x-button-primary :full-width="true" id="btn-login">
                Sign In
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </x-button-primary>
        </div>
    </form>

    {{-- Divider --}}
    <div class="my-6 flex items-center gap-3 animate-fade-in-up animate-delay-200">
        <div class="flex-1 h-px" style="background: #E5E7EB;"></div>
        <span class="text-xs font-medium" style="color: #9CA3AF;">atau</span>
        <div class="flex-1 h-px" style="background: #E5E7EB;"></div>
    </div>

    {{-- Create account link --}}
    <p class="text-center text-sm animate-fade-in-up animate-delay-300" style="color: #6B7280;">
        Belum punya akun?
        <a href="{{ route('register') }}"
           class="font-semibold hover:underline ml-1 transition-colors"
           id="link-create-account"
           style="color: #003F2F;">
            Create Account
        </a>
    </p>

</x-auth-layout>
