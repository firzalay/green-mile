<x-auth-layout title="Pendaftaran Berhasil – GreenMile">

    {{-- Success Content --}}
    <div class="flex flex-col items-center text-center animate-fade-in-up">
        {{-- Success Graphic --}}
        <div class="w-16 h-16 rounded-full flex items-center justify-center mb-6 shadow-sm border"
             style="background: rgba(46,207,137,0.1); border-color: rgba(46,207,137,0.2); color: #2ECF89;">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                <polyline points="22 4 12 14.01 9 11.01" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        <x-app-badge label="Registrasi Berhasil" />
        
        <h2 class="mt-4 font-bold leading-tight" style="font-size: 26px; color: #111827;">
            Pendaftaran Berhasil
        </h2>
        
        <div class="mt-5 space-y-4 text-sm leading-relaxed text-left p-5 bg-white border border-gray-200/60 rounded-2xl" style="color: #4B5563;">
            <p>
                Akun organizer Anda sedang menunggu persetujuan dari tim GreenMile.
            </p>
            <p>
                Kami akan meninjau informasi yang telah Anda kirimkan sebelum memberikan akses ke dashboard organizer.
            </p>
        </div>

        {{-- Return to Login button --}}
        <div class="mt-8 w-full">
            <a href="{{ route('login') }}" 
               id="btn-back-to-login" 
               class="btn-primary w-full text-center justify-center shadow-md">
                Kembali ke Login
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>
    </div>

</x-auth-layout>
