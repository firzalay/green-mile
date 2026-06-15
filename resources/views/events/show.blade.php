<x-app-layout title="{{ $event->name }} – Jejak Hijau" :user="$user">
    <div class="space-y-6">
        {{-- Navigation & Back button --}}
        <div class="animate-fade-in-up">
            <a href="{{ route('events.index') }}" 
               id="btn-back-to-events"
               class="inline-flex items-center gap-1 text-sm font-semibold hover:underline"
               style="color: #2ECF89;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 12H5M12 19l-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Kembali ke Daftar Event
            </a>
        </div>

        {{-- Alerts for success/error flash notifications --}}
        @if (session('success'))
            <div class="p-4 rounded-xl border animate-fade-in-up flex items-start gap-3"
                 style="background: rgba(34, 197, 94, 0.08); border-color: rgba(34, 197, 94, 0.25); color: #15803d;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mt-0.5">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <polyline points="22 4 12 14.01 9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div>
                    <p class="font-bold text-sm">Berhasil</p>
                    <p class="text-xs mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-xl border animate-fade-in-up flex items-start gap-3"
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

        {{-- Details Container --}}
        <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 grid grid-cols-1 lg:grid-cols-3 animate-fade-in-up animate-delay-100">
            
            {{-- Main detail content (spans 2 columns) --}}
            <div class="lg:col-span-2 p-6 lg:p-8 space-y-6">
                {{-- Banner image --}}
                <div class="relative h-64 md:h-80 w-full rounded-2xl overflow-hidden bg-emerald-950 shadow-inner">
                    @if ($event->banner_image)
                        <img src="{{ $event->banner_image }}" 
                             alt="{{ $event->name }}" 
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-white/30 font-bold" 
                             style="background: linear-gradient(135deg, #003F2F 0%, #2ECF89 100%);">
                            GreenRun Event
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-black/20"></div>
                </div>

                {{-- Name & Status --}}
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($event->status === 'Ongoing')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                  style="background: rgba(46,207,137,0.12); border: 1px solid rgba(46,207,137,0.3); color: #003F2F;">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald animate-pulse"></span>
                                Ongoing
                            </span>
                        @elseif ($event->status === 'Upcoming')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                  style="background: rgba(255,122,69,0.12); border: 1px solid rgba(255,122,69,0.3); color: #FF7A45;">
                                Upcoming
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                  style="background: rgba(107,114,128,0.12); border: 1px solid rgba(107,114,128,0.3); color: #374151;">
                                Finished
                            </span>
                        @endif

                        @if ($isJoined)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                  style="background: rgba(46,207,137,0.2); border: 1px solid rgba(46,207,137,0.4); color: #003F2F;">
                                Terdaftar (Joined)
                            </span>
                        @endif
                    </div>
                    <h3 class="font-bold text-2xl lg:text-3xl leading-tight" style="color: #111827;">{{ $event->name }}</h3>
                </div>

                {{-- Description --}}
                <div class="space-y-2.5">
                    <h4 class="font-bold text-sm uppercase tracking-wider" style="color: #6B7280;">Deskripsi Event</h4>
                    <p class="text-sm leading-relaxed" style="color: #374151;">
                        {{ $event->description ?: 'Belum ada deskripsi untuk event ini.' }}
                    </p>
                </div>
            </div>

            {{-- Sidebar details & CTA action --}}
            <div class="p-6 lg:p-8 bg-gray-50 flex flex-col justify-between border-t lg:border-t-0 lg:border-l border-gray-100">
                <div class="space-y-6">
                    <h4 class="font-bold text-sm uppercase tracking-wider" style="color: #6B7280;">Detail Informasi</h4>

                    {{-- Stats List --}}
                    <div class="space-y-4">
                        {{-- Date --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background: rgba(46,207,137,0.12); color: #003F2F;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                                    <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs" style="color: #9CA3AF;">Tanggal</p>
                                <p class="text-sm font-semibold" style="color: #374151;">{{ $event->event_date ? $event->event_date->translatedFormat('d F Y') : '-' }}</p>
                            </div>
                        </div>

                        {{-- Location --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background: rgba(46,207,137,0.12); color: #003F2F;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z" 
                                          fill="currentColor"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs" style="color: #9CA3AF;">Lokasi</p>
                                <p class="text-sm font-semibold" style="color: #374151;">{{ $event->location }}</p>
                            </div>
                        </div>

                        {{-- Checkpoints --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background: rgba(46,207,137,0.12); color: #003F2F;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="3" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                                    <rect x="14" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                                    <rect x="3" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                                    <path d="M14 14h2v2h-2zM18 14h3M14 18h3M18 18h3v3" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs" style="color: #9CA3AF;">Total Checkpoint</p>
                                <p class="text-sm font-semibold" style="color: #374151;">{{ $event->total_checkpoints }} Checkpoint</p>
                            </div>
                        </div>

                        {{-- Rewards --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background: rgba(46,207,137,0.12); color: #003F2F;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="2" y="7" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
                                    <path d="M16 7c0-2.21-1.79-4-4-4S8 4.79 8 7" stroke="currentColor" stroke-width="2"/>
                                    <path d="M12 7v14M2 12h20" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs" style="color: #9CA3AF;">Total Hadiah Tersedia</p>
                                <p class="text-sm font-semibold" style="color: #374151;">{{ $event->total_rewards ?: '-' }}</p>
                            </div>
                        </div>

                        {{-- Total Event Points --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background: rgba(46,207,137,0.12); color: #003F2F;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14l-5-4.87 6.91-1.01L12 2z"
                                          fill="currentColor"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs" style="color: #9CA3AF;">Total Poin Event</p>
                                <p class="text-sm font-semibold" style="color: #374151;">{{ number_format($event->total_event_point) }} pts</p>
                            </div>
                        </div>

                        {{-- Joined Participants count --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background: rgba(46,207,137,0.12); color: #003F2F;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 7a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm14 14v-2a4 4 0 0 0-3-3.87" 
                                          stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs" style="color: #9CA3AF;">Peserta Terdaftar</p>
                                <p class="text-sm font-semibold" style="color: #374151;">{{ $event->participants_count }} Peserta</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action CTA --}}
                <div class="pt-8 lg:pt-0">
                    @if ($isJoined)
                        <button disabled 
                                class="w-full btn-secondary h-12 flex items-center justify-center opacity-75 cursor-not-allowed">
                            Joined (Terdaftar)
                        </button>
                    @elseif ($event->status === 'Finished')
                        <button disabled 
                                class="w-full btn-secondary h-12 flex items-center justify-center opacity-50 cursor-not-allowed">
                            Event Telah Selesai
                        </button>
                    @else
                        <a href="{{ route('events.join') }}" 
                           id="btn-join-event"
                           class="w-full btn-primary h-12 flex items-center justify-center gap-2">
                            Ikuti Event (Join)
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
