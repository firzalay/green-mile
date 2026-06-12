<x-app-layout title="Fitur Sedang Dikembangkan – Jejak Hijau" :user="$user">
    <div class="space-y-6">
        <div class="animate-fade-in-up">
            <a href="{{ route('organizer.dashboard') }}" 
               id="btn-back-to-dashboard"
               class="inline-flex items-center gap-1 text-sm font-semibold hover:underline"
               style="color: #2ECF89;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 12H5M12 19l-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Kembali ke Dashboard
            </a>
        </div>

        <section class="bg-white rounded-3xl p-8 lg:p-12 text-center border border-gray-100 shadow-sm flex flex-col items-center justify-center space-y-6 animate-fade-in-up animate-delay-100">
            <div class="w-24 h-24 rounded-full flex items-center justify-center" style="background: rgba(46,207,137,0.1); color: #2ECF89;">
                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z" stroke="currentColor" stroke-width="2"/>
                    <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>

            <div class="space-y-2 max-w-md">
                <h3 class="font-bold text-xl text-gray-900">{{ $title }}</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Modul ini sedang dalam proses pengembangan. Halaman ini berfungsi sebagai placeholder sebelum modul fungsional penuh diimplementasikan pada milestone berikutnya.
                </p>
            </div>

            <div class="pt-4">
                <a href="{{ route('organizer.dashboard') }}" class="btn-primary px-6 h-11 text-xs">
                    Kembali ke Dashboard
                </a>
            </div>
        </section>
    </div>
</x-app-layout>
