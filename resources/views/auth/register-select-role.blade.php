<x-auth-layout title="Pilih Peran – Jejak Hijau">

    {{-- Heading --}}
    <div class="mb-8 animate-fade-in-up">
        <x-app-badge label="Langkah Pertama" />
        <h2 class="mt-4 font-bold leading-tight" style="font-size: 28px; color: #111827;">
            Pilih Jenis Akun Anda
        </h2>
        <p class="mt-1.5 text-sm" style="color: #6B7280;">
            Sebelum mendaftar, silakan tentukan bagaimana Anda ingin berkontribusi di platform GreenRun.
        </p>
    </div>

    {{-- Selection Cards --}}
    <div class="space-y-4 animate-fade-in-up animate-delay-100">
        {{-- Card 1: Participant --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-200/60 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-emerald/40 flex flex-col justify-between">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-emerald/10 text-emerald flex-shrink-0">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2" />
                        <path d="M4 20c0-4 3.582-7 8-7s8 3 8 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-base text-forest">Participant</h3>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                        Ikuti event, scan QR, kumpulkan poin, dan tukarkan reward.
                    </p>
                </div>
            </div>
            <div class="mt-4 pt-1">
                <a href="{{ route('register.participant') }}" 
                   id="btn-select-participant" 
                   class="btn-primary w-full text-center justify-center" 
                   style="height: 44px; font-size: 0.875rem;">
                    Daftar sebagai Participant
                </a>
            </div>
        </div>

        {{-- Card 2: Event Organizer --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-200/60 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-emerald/40 flex flex-col justify-between">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-forest/10 text-forest flex-shrink-0">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 21h18M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16M9 7h1M9 11h1M9 15h1M14 7h1M14 11h1M14 15h1" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-base text-forest">Event Organizer</h3>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                        Buat event, kelola checkpoint, generate QR, dan pantau peserta.
                    </p>
                </div>
            </div>
            <div class="mt-4 pt-1">
                <a href="{{ route('register.organizer') }}" 
                   id="btn-select-organizer" 
                   class="btn-primary w-full text-center justify-center" 
                   style="background: #003F2F; height: 44px; font-size: 0.875rem;">
                    Daftar sebagai Event Organizer
                </a>
            </div>
        </div>
    </div>

    {{-- Link to Login --}}
    <p class="text-center text-sm mt-8 animate-fade-in-up animate-delay-200" style="color: #6B7280;">
        Sudah punya akun?
        <a href="{{ route('login') }}"
           class="font-semibold hover:underline ml-1 transition-colors"
           style="color: #003F2F;">
            Masuk Sekarang
        </a>
    </p>

</x-auth-layout>
