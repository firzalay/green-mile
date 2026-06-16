<x-auth-layout title="Daftar Event Organizer – GreenMile">

    {{-- Heading --}}
    <div class="mb-8 animate-fade-in-up">
        <a href="{{ route('register.select-role') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-emerald hover:underline mb-4">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 12H5M12 19l-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Kembali ke Peran
        </a>
        <x-app-badge label="Registrasi Organizer" />
        <h2 class="mt-4 font-bold leading-tight" style="font-size: 28px; color: #111827;">
            Daftar Organizer
        </h2>
        <p class="mt-1.5 text-sm" style="color: #6B7280;">
            Kelola event larimu, buat checkpoint, pantau leaderboard peserta.
        </p>
    </div>

    {{-- Errors --}}
    @if ($errors->any())
        <div class="mb-5 flex flex-col gap-1.5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 animate-fade-in-up">
            @foreach ($errors->all() as $error)
                <div class="flex items-start gap-2 text-xs font-medium text-red-600">
                    <svg class="shrink-0 mt-0.5" width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                        <path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Registration Form --}}
    <form id="register-form" method="POST" action="{{ route('register.organizer') }}" class="space-y-5 animate-fade-in-up animate-delay-100" novalidate>
        @csrf

        {{-- Organization Name --}}
        <div>
            <label for="organization_name" class="block text-sm font-semibold mb-1.5" style="color: #374151;">
                Nama Organisasi
            </label>
            <x-input
                id="organization_name"
                name="organization_name"
                type="text"
                placeholder="Nama Organisasi / Komunitas"
                :value="old('organization_name')"
                :required="true"
                :autofocus="true">
                <x-slot name="icon">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 21h18M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16M9 7h1M9 11h1M9 15h1M14 7h1M14 11h1M14 15h1" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </x-slot>
            </x-input>
        </div>

        {{-- Contact Person Name --}}
        <div>
            <label for="contact_person" class="block text-sm font-semibold mb-1.5" style="color: #374151;">
                Nama Penanggung Jawab
            </label>
            <x-input
                id="contact_person"
                name="contact_person"
                type="text"
                placeholder="Nama lengkap penanggung jawab"
                :value="old('contact_person')"
                :required="true">
                <x-slot name="icon">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/>
                        <path d="M4 20c0-4 3.582-7 8-7s8 3 8 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </x-slot>
            </x-input>
        </div>

        {{-- Username --}}
        <div>
            <label for="username" class="block text-sm font-semibold mb-1.5" style="color: #374151;">
                Username
            </label>
            <x-input
                id="username"
                name="username"
                type="text"
                placeholder="username_organizer"
                :value="old('username')"
                :required="true">
                <x-slot name="icon">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                        <path d="M12 16v-4M8 12h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </x-slot>
            </x-input>
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-semibold mb-1.5" style="color: #374151;">
                Email
            </label>
            <x-input
                id="email"
                name="email"
                type="email"
                placeholder="organisasi@email.com"
                :value="old('email')"
                :required="true">
                <x-slot name="icon">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="2" y="4" width="20" height="16" rx="3" stroke="currentColor" stroke-width="2"/>
                        <path d="m2 7 8.586 5.657a2 2 0 0 0 2.828 0L22 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </x-slot>
            </x-input>
        </div>

        {{-- Phone Number --}}
        <div>
            <label for="phone" class="block text-sm font-semibold mb-1.5" style="color: #374151;">
                Nomor Telepon
            </label>
            <x-input
                id="phone"
                name="phone"
                type="text"
                placeholder="Contoh: 081234567890"
                :value="old('phone')"
                :required="true">
                <x-slot name="icon">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </x-slot>
            </x-input>
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-semibold mb-1.5" style="color: #374151;">
                Password
            </label>
            <x-password-input
                id="password"
                name="password"
                placeholder="Buat password (min. 8 karakter)"
                :required="true"
            />
        </div>

        {{-- Password Confirmation --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-semibold mb-1.5" style="color: #374151;">
                Konfirmasi Password
            </label>
            <x-password-input
                id="password_confirmation"
                name="password_confirmation"
                placeholder="Ulangi password"
                :required="true"
            />
        </div>

        {{-- Submit button --}}
        <div class="pt-2">
            <x-button-primary :full-width="true" id="btn-register">
                Daftar sebagai Organizer
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

    {{-- Login link --}}
    <p class="text-center text-sm animate-fade-in-up animate-delay-300" style="color: #6B7280;">
        Sudah punya akun?
        <a href="{{ route('login') }}"
           class="font-semibold hover:underline ml-1 transition-colors"
           style="color: #003F2F;">
            Sign In
        </a>
    </p>

</x-auth-layout>
