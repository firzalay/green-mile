<x-app-layout title="Gabung Event – Jejak Hijau" :user="$user">
    <div class="max-w-md mx-auto py-10 animate-fade-in-up">
        <div class="bg-white rounded-2xl p-8 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 space-y-6">
            {{-- Header --}}
            <div class="text-center space-y-2">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center mx-auto bg-emerald/10 text-emerald"
                     style="background-color: rgba(46,207,137,0.1); color: #2ECF89;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                        <line x1="12" y1="8" x2="12" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <line x1="8" y1="12" x2="16" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <h2 class="font-bold text-xl text-gray-900">Gabung Event Baru</h2>
                <p class="text-xs text-gray-500">
                    Masukkan 8-12 karakter kode akses yang diberikan oleh organizer untuk terdaftar ke dalam event.
                </p>
            </div>

            {{-- Notifications --}}
            @if (session('error'))
                <div class="p-4 rounded-xl border flex items-start gap-3"
                     style="background: rgba(239, 68, 68, 0.08); border-color: rgba(239, 68, 68, 0.25); color: #b91c1c;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mt-0.5">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                        <line x1="12" y1="8" x2="12" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <line x1="12" y1="16" x2="12.01" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <div>
                        <p class="font-bold text-sm">Gagal</p>
                        <p class="text-xs mt-0.5">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('events.join.submit') }}" class="space-y-4">
                @csrf
                <div class="space-y-2">
                    <label for="join_code" class="text-xs font-bold text-gray-600 uppercase tracking-wider block font-sans">Kode Event</label>
                    <input type="text" 
                           name="join_code" 
                           id="join_code" 
                           value="{{ old('join_code') }}"
                           placeholder="Contoh: SBY2026" 
                           required
                           class="w-full px-4 h-12 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald/20 focus:border-emerald uppercase text-center font-bold tracking-widest text-lg"
                           style="border-color: #E5E7EB;">
                    @error('join_code')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" 
                        id="btn-submit-code"
                        class="w-full btn-primary h-12 flex items-center justify-center font-bold gap-2 text-sm text-white rounded-xl shadow-sm transition-all"
                        style="background: #003F2F;">
                    Gabung Sekarang
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
