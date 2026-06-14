<x-app-layout title="Profil Saya – Jejak Hijau" :user="$user">
    <div class="space-y-6 max-w-2xl mx-auto pb-10">
        {{-- Header Section --}}
        <section class="animate-fade-in-up flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl" style="color: #111827;">Profil Saya</h2>
                <p class="text-sm mt-1" style="color: #6B7280;">
                    Kelola informasi profil, pantau aktivitas, dan tingkatkan pencapaian Anda.
                </p>
            </div>
            <div>
                <a href="{{ route('profile.edit') }}" 
                   id="btn-edit-profile-page"
                   class="inline-flex items-center justify-center gap-2 h-10 px-4 text-xs font-bold rounded-xl text-white shadow-sm transition-all"
                   style="background: #003F2F; hover:background: #002f23;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"></path>
                    </svg>
                    Edit Profil
                </a>
            </div>
        </section>

        @if(session('success'))
            <div class="p-4 rounded-xl text-sm font-semibold text-emerald bg-emerald/10 border border-emerald/20 flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Profile Card --}}
        <section class="bg-white rounded-2xl p-6 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex flex-col items-center text-center sm:flex-row sm:text-left gap-5 animate-fade-in-up">
            <div class="w-24 h-24 rounded-full overflow-hidden flex-shrink-0 border-2 border-emerald-500/20 shadow-sm bg-gray-50 flex items-center justify-center">
                @if($user->avatar)
                    <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                @else
                    @php
                        $initials = collect(explode(' ', $user->name))
                            ->map(fn ($w) => strtoupper(substr($w, 0, 1)))
                            ->take(2)
                            ->implode('');
                    @endphp
                    <div class="w-full h-full flex items-center justify-center font-black text-2xl text-white"
                         style="background: linear-gradient(135deg, #003F2F 0%, #2ECF89 100%);">
                        {{ $initials }}
                    </div>
                @endif
            </div>
            <div class="space-y-1 min-w-0 flex-1">
                <h3 class="text-xl font-black text-gray-900 leading-tight truncate">{{ $user->name }}</h3>
                <p class="text-sm font-semibold text-emerald" style="color: #2ECF89;">{{ '@' . $user->username }}</p>
                <p class="text-xs text-gray-400 mt-1 flex items-center justify-center sm:justify-start gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25H4.5A2.25 2.25 0 0 1 2.25 17.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5H4.5a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"></path>
                    </svg>
                    {{ $user->email }}
                </p>
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mt-2">
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-emerald/10 text-emerald uppercase tracking-wider">
                        {{ $user->role }}
                    </span>
                    <span class="text-[11px] text-gray-400">
                        Bergabung sejak {{ $user->created_at->translatedFormat('F Y') }}
                    </span>
                </div>
            </div>
        </section>

        {{-- Statistics Grid --}}
        <section class="grid grid-cols-2 sm:grid-cols-4 gap-4 animate-fade-in-up">
            {{-- Points --}}
            <div class="bg-white p-4 rounded-2xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 space-y-1">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Total Poin</span>
                <p class="text-xl font-black text-gray-800" id="stat-points">{{ number_format($totalPoints) }}</p>
            </div>
            {{-- Points Available --}}
            <div class="bg-white p-4 rounded-2xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 space-y-1">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Poin Tersedia</span>
                <p class="text-xl font-black text-emerald" id="stat-points-available" style="color: #2ECF89;">{{ number_format($user->points) }}</p>
            </div>
            {{-- Events --}}
            <div class="bg-white p-4 rounded-2xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 space-y-1">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Event Diikuti</span>
                <p class="text-xl font-black text-gray-800" id="stat-events">{{ $eventsJoined }}</p>
            </div>
            {{-- Scans --}}
            <div class="bg-white p-4 rounded-2xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 space-y-1">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Scan Checkpoint</span>
                <p class="text-xl font-black text-gray-800" id="stat-scans">{{ $checkpointsScanned }}</p>
            </div>
        </section>

        {{-- Change Password --}}
        <section class="bg-white rounded-2xl p-5 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 space-y-4 animate-fade-in-up">
            <h3 class="font-bold text-sm text-gray-800 uppercase tracking-wider border-b border-gray-100 pb-2.5">
                Ubah Password
            </h3>
            <form method="POST" action="{{ route('profile.update-password') }}" class="space-y-4" novalidate>
                @csrf
                @method('PUT')

                {{-- Current Password --}}
                <div class="space-y-1.5">
                    <label for="current_password" class="block text-xs font-bold text-gray-700">Password Sekarang <span class="text-red-500">*</span></label>
                    <input type="password" id="current_password" name="current_password" required
                           class="w-full px-4 py-2 rounded-xl border {{ $errors->has('current_password') ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-emerald focus:ring-emerald/20' }} focus:outline-none focus:ring-4 transition-all text-sm text-gray-850">
                    @error('current_password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- New Password --}}
                <div class="space-y-1.5">
                    <label for="password" class="block text-xs font-bold text-gray-700">Password Baru <span class="text-red-500">*</span></label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-2 rounded-xl border {{ $errors->has('password') ? 'border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-emerald focus:ring-emerald/20' }} focus:outline-none focus:ring-4 transition-all text-sm text-gray-850">
                    @error('password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="space-y-1.5">
                    <label for="password_confirmation" class="block text-xs font-bold text-gray-700">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-emerald focus:ring-emerald/20 focus:outline-none focus:ring-4 transition-all text-sm text-gray-850">
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" 
                            id="btn-update-password"
                            class="inline-flex items-center justify-center h-10 px-5 text-xs font-bold rounded-xl text-white shadow-sm transition-all"
                            style="background: #003F2F; hover:background: #002f23;">
                        Simpan Password Baru
                    </button>
                </div>
            </form>
        </section>

        {{-- Logout Section --}}
        <section class="bg-white rounded-2xl p-5 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 space-y-4 animate-fade-in-up">
            <h3 class="font-bold text-sm text-gray-800 uppercase tracking-wider border-b border-gray-100 pb-2.5">
                Sesi Akun
            </h3>
            <div class="flex items-center justify-between text-sm gap-4">
                <div>
                    <p class="font-semibold text-gray-800">Keluar dari Akun</p>
                    <p class="text-xs text-gray-400">Pastikan data Anda sudah tersimpan sebelum keluar.</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                    @csrf
                    <button type="submit"
                            id="btn-logout-profile"
                            class="inline-flex items-center justify-center gap-2 h-10 px-5 text-xs font-bold rounded-xl text-white shadow-sm transition-all"
                            style="background: #DC2626; hover:background: #B91C1C;">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"></path>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </section>
    </div>
</x-app-layout>
