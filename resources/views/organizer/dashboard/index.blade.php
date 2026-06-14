<x-app-layout title="Dashboard Organizer – Jejak Hijau" :user="$user">
    <div class="space-y-6">
        {{-- PWA Install Promo --}}
        <div id="pwa-install-container" class="hidden animate-fade-in-up">
            <div class="bg-white border border-emerald-500/10 rounded-2xl p-4 flex items-center justify-between gap-4 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald/10 flex items-center justify-center text-emerald flex-shrink-0 text-lg">
                        📱
                    </div>
                    <div class="text-left">
                        <h4 class="font-bold text-sm text-gray-800">Install Aplikasi GreenRun</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Pantau event larimu lebih cepat langsung dari home screen.</p>
                    </div>
                </div>
                <button id="pwa-install-btn" class="px-4 py-2 rounded-xl text-xs font-bold text-white transition-all shadow-sm" style="background-color: #003F2F;">
                    Install
                </button>
            </div>
        </div>

        {{-- Empty State Check --}}
        @if ($events->isEmpty())
            <section class="flex flex-col items-center justify-center py-20 text-center animate-fade-in-up">
                {{-- Illustration --}}
                <div class="w-32 h-32 rounded-full flex items-center justify-center mb-6"
                     style="background: rgba(46,207,137,0.08); border: 2px dashed rgba(46,207,137,0.3);">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="4" width="18" height="18" rx="2" stroke="#003F2F" stroke-width="1.5"/>
                        <path d="M16 2v4M8 2v4M3 10h18" stroke="#003F2F" stroke-width="1.5"/>
                        <circle cx="12" cy="16" r="2" fill="#2ECF89"/>
                    </svg>
                </div>

                <h3 class="font-bold text-xl mb-2" style="color: #111827;">Belum Ada Event</h3>
                <p class="text-sm leading-relaxed mb-6 max-w-md" style="color: #6B7280;">
                    Belum ada event yang dibuat. Mulai buat event pertama Anda untuk mengajak peserta berpartisipasi dalam GreenRun.
                </p>

                <a href="{{ route('organizer.events.create') }}" id="btn-buat-event-empty" class="btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    Buat Event Pertama
                </a>
            </section>
        @else
            {{-- Header/Greeting --}}
            <section class="animate-fade-in-up">
                <h2 class="font-bold text-2xl" style="color: #111827;">Monitoring Dashboard</h2>
                <p class="text-sm mt-0.5" style="color: #6B7280;">
                    Pusat pemantauan event, scan checkpoint, aktivitas peserta, dan analisis performa lari.
                </p>
            </section>

            {{-- 1. Statistics Cards --}}
            <section class="grid grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-in-up animate-delay-100">
                {{-- Total Event --}}
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center mb-3" style="background: rgba(0,63,47,0.08); color: #003F2F;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                                <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Event</p>
                        <p class="font-bold text-2xl mt-1 text-gray-900">{{ number_format($totalEvents) }}</p>
                    </div>
                    <span class="text-xs text-gray-400 mt-2">Terdaftar di sistem</span>
                </div>

                {{-- Event Aktif --}}
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center mb-3" style="background: rgba(46,207,137,0.12); color: #2ECF89;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald animate-pulse"></span>
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" fill="currentColor"/>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Event Aktif</p>
                        <p class="font-bold text-2xl mt-1 text-gray-900">{{ number_format($activeEvents) }}</p>
                    </div>
                    <span class="text-xs text-emerald-500 mt-2 font-medium">Sedang berjalan</span>
                </div>

                {{-- Total Peserta --}}
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center mb-3" style="background: rgba(123,224,179,0.15); color: #003F2F;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 7a4 4 0 1 0 0-8 4 4 0 0 0 0 8z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Peserta</p>
                        <p class="font-bold text-2xl mt-1 text-gray-900">{{ number_format($totalParticipants) }}</p>
                    </div>
                    <span class="text-xs text-gray-400 mt-2">Pelari berpartisipasi</span>
                </div>

                {{-- Total Scan Hari Ini --}}
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center mb-3" style="background: rgba(255,122,69,0.12); color: #FF7A45;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="3" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                                <rect x="14" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                                <rect x="3" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                                <path d="M14 14h2v2h-2zm4 0h3v3h-3z" fill="currentColor"/>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Scan Hari Ini</p>
                        <p class="font-bold text-2xl mt-1 text-gray-900">{{ number_format($totalScansToday) }}</p>
                    </div>
                    <span class="text-xs text-gray-400 mt-2">Checkpoint terverifikasi</span>
                </div>
            </section>

            {{-- Point Pool Summary Card --}}
            <section class="bg-white rounded-2xl p-6 border border-gray-150 shadow-sm animate-fade-in-up animate-delay-150">
                <div class="flex items-center justify-between border-b pb-3 border-gray-100 mb-4">
                    <h3 class="font-bold text-base text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Ringkasan Point Pool
                    </h3>
                    <span class="text-xs px-2.5 py-1 rounded-full font-bold {{ $totalRemainingPointPool > 0 ? 'bg-emerald/10 text-emerald' : 'bg-red-100 text-red-700' }}">
                        {{ $totalRemainingPointPool > 0 ? 'Available' : 'Exhausted' }}
                    </span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Total Point Pool --}}
                    <div class="space-y-1">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Total Point Pool</p>
                        <p class="text-2xl font-black text-gray-900">{{ number_format($totalPointPool) }} <span class="text-xs font-normal text-gray-500">Poin</span></p>
                    </div>
                    {{-- Remaining Point Pool --}}
                    <div class="space-y-1">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Sisa Point Pool</p>
                        <p class="text-2xl font-black text-emerald">{{ number_format($totalRemainingPointPool) }} <span class="text-xs font-normal text-gray-500">Poin</span></p>
                    </div>
                    {{-- Distributed Points --}}
                    <div class="space-y-1">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Poin Terdistribusi</p>
                        <p class="text-2xl font-black text-forest" style="color: #003F2F;">{{ number_format($totalDistributedPoints) }} <span class="text-xs font-normal text-gray-500">Poin</span></p>
                    </div>
                </div>
            </section>

            {{-- Main Layout Content Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- LEFT Column (Overview & Performance - Spans 2) --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- 2. Active Event Performance (Ongoing analysis) --}}
                    @if ($activePerformance)
                        <section class="bg-gradient-to-br from-forest to-emerald rounded-3xl p-6 text-white shadow-md relative overflow-hidden animate-fade-in-up animate-delay-150">
                            {{-- Decorative Background circles --}}
                            <div class="absolute top-0 right-0 w-48 h-48 rounded-full opacity-10 bg-white" style="transform: translate(20%, -20%);"></div>
                            <div class="absolute bottom-0 left-0 w-32 h-32 rounded-full opacity-5 bg-white" style="transform: translate(-20%, 20%);"></div>

                            <div class="relative space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold uppercase tracking-wider px-2.5 py-1 rounded-full bg-white/10 border border-white/20 text-mint">
                                        Performa Terkini
                                    </span>
                                    <span class="text-xs opacity-75">Event: {{ $activePerformance['event']->name }}</span>
                                </div>

                                <div class="grid grid-cols-3 gap-4 pt-2">
                                    <div>
                                        <p class="text-xs text-white/60">Peserta Terdaftar</p>
                                        <p class="font-bold text-xl">{{ number_format($activePerformance['registered_count']) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-white/60">Scan Checkpoint</p>
                                        <p class="font-bold text-xl">{{ number_format($activePerformance['scans_count']) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-white/60">Poin Terkumpul</p>
                                        <p class="font-bold text-xl">{{ number_format($activePerformance['points_count']) }} pts</p>
                                    </div>
                                </div>

                                {{-- Progress --}}
                                <div class="pt-3 border-t border-white/10 space-y-2">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-white/80">Penyelesaian Rata-rata Checkpoint</span>
                                        <span class="font-bold text-mint">{{ $activePerformance['progress_percent'] }}%</span>
                                    </div>
                                    <div class="w-full h-2 rounded-full bg-white/10 overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-emerald to-mint transition-all duration-500"
                                             style="width: {{ $activePerformance['progress_percent'] }}%;"></div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    @endif

                    {{-- 3. Event Overview list --}}
                    <section class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-4 animate-fade-in-up animate-delay-200">
                        <div class="flex items-center justify-between border-b pb-3 border-gray-100">
                            <h3 class="font-bold text-base text-gray-900">Daftar Event</h3>
                            <a href="{{ route('organizer.events.create') }}" class="text-xs font-semibold text-emerald hover:underline">
                                + Buat Baru
                            </a>
                        </div>

                        <div class="divide-y divide-gray-50">
                            @foreach ($events as $event)
                                @php
                                    $status = $event->status;
                                @endphp
                                <a href="{{ route('organizer.events.show', $event->id) }}" class="py-3.5 flex items-center justify-between gap-4 group hover:bg-gray-50/50 -mx-6 px-6 transition-colors">
                                    <div class="min-w-0 flex-1 space-y-1">
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-bold text-sm text-gray-800 truncate">{{ $event->name }}</h4>
                                            
                                            {{-- Status Badge --}}
                                            @if ($status === 'Ongoing')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald/10 text-emerald">
                                                    Ongoing
                                                </span>
                                            @elseif ($status === 'Upcoming')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-500/10 text-amber-600">
                                                    Upcoming
                                                </span>
                                            @elseif ($status === 'Draft')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-500/10 text-blue-600">
                                                    Draft
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-500/10 text-gray-600">
                                                    Finished
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 text-xs text-gray-400">
                                            <span>Tanggal: {{ $event->event_date ? $event->event_date->translatedFormat('d M Y') : '-' }}</span>
                                            <span>•</span>
                                            <span>Checkpoint: {{ $event->checkpoints_count }}</span>
                                            <span>•</span>
                                            <span>Peserta: {{ $event->participants_count }}</span>
                                        </div>
                                    </div>

                                    <div class="flex-shrink-0">
                                        <span class="text-xs font-semibold text-gray-400 group-hover:text-emerald transition-colors">
                                            Detail →
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </section>
                </div>

                {{-- RIGHT Column (Quick Actions & Recent Activity) --}}
                <div class="space-y-6">
                    
                    {{-- 4. Quick Actions --}}
                    <section class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-4 animate-fade-in-up animate-delay-250">
                        <h3 class="font-bold text-base text-gray-900 border-b pb-3 border-gray-100">Akses Cepat</h3>

                        <div class="grid grid-cols-2 gap-3">
                            {{-- Buat Event --}}
                            <a href="{{ route('organizer.events.create') }}" 
                               id="btn-buat-event-action"
                               class="p-3.5 rounded-xl border border-gray-100 hover:border-emerald/30 hover:bg-emerald/5 transition-all text-center flex flex-col items-center group">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-2.5 bg-forest/5 text-forest group-hover:bg-emerald/10 group-hover:text-emerald transition-all">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                                        <line x1="12" y1="8" x2="12" y2="16" stroke="currentColor" stroke-width="2"/>
                                        <line x1="8" y1="12" x2="16" y2="12" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                </div>
                                <span class="text-xs font-bold text-gray-800">Buat Event</span>
                            </a>

                            {{-- Kelola Checkpoint --}}
                            <a href="{{ route('organizer.placeholder', 'checkpoints') }}" 
                               id="btn-kelola-checkpoint-action"
                               class="p-3.5 rounded-xl border border-gray-100 hover:border-emerald/30 hover:bg-emerald/5 transition-all text-center flex flex-col items-center group">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-2.5 bg-forest/5 text-forest group-hover:bg-emerald/10 group-hover:text-emerald transition-all">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" stroke="currentColor" stroke-width="2"/>
                                        <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                </div>
                                <span class="text-xs font-bold text-gray-800">Kelola Checkpoint</span>
                            </a>

                            {{-- Generate QR --}}
                            <a href="{{ route('organizer.placeholder', 'qr-generation') }}" 
                               id="btn-generate-qr-action"
                               class="p-3.5 rounded-xl border border-gray-100 hover:border-emerald/30 hover:bg-emerald/5 transition-all text-center flex flex-col items-center group">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-2.5 bg-forest/5 text-forest group-hover:bg-emerald/10 group-hover:text-emerald transition-all">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="3" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                                        <rect x="14" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                                        <rect x="3" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                                        <path d="M14 14h3M14 18h3M18 18h3v3" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                </div>
                                <span class="text-xs font-bold text-gray-800">Generate QR</span>
                            </a>

                            {{-- Lihat Peserta --}}
                            <a href="{{ route('organizer.placeholder', 'participants') }}" 
                               id="btn-lihat-peserta-action"
                               class="p-3.5 rounded-xl border border-gray-100 hover:border-emerald/30 hover:bg-emerald/5 transition-all text-center flex flex-col items-center group">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-2.5 bg-forest/5 text-forest group-hover:bg-emerald/10 group-hover:text-emerald transition-all">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 7a4 4 0 1 0 0-8 4 4 0 0 0 0 8z" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                </div>
                                <span class="text-xs font-bold text-gray-800">Lihat Peserta</span>
                            </a>
                        </div>
                    </section>

                    {{-- 5. Recent Participant Activity --}}
                    <section class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-4 animate-fade-in-up animate-delay-300">
                        <h3 class="font-bold text-base text-gray-900 border-b pb-3 border-gray-100">Aktivitas Peserta</h3>

                        <div class="space-y-4 max-h-[360px] overflow-y-auto pr-1">
                            @if ($recentActivities->isEmpty())
                                <p class="text-xs text-gray-400 text-center py-6">Belum ada aktivitas peserta terekam.</p>
                            @else
                                @foreach ($recentActivities as $activity)
                                    @php
                                        $initials = collect(explode(' ', $activity->user->name ?? 'User'))
                                            ->map(fn ($w) => strtoupper(substr($w, 0, 1)))
                                            ->take(2)->implode('');
                                    @endphp
                                    <div class="flex items-start gap-3">
                                        {{-- User initials avatar --}}
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs text-white flex-shrink-0"
                                             style="background: linear-gradient(135deg, #003F2F, #2ECF89);">
                                            {{ $initials }}
                                        </div>
                                        
                                        <div class="min-w-0 flex-1 space-y-0.5">
                                            <p class="text-xs text-gray-700 leading-tight">
                                                <span class="font-bold text-gray-900">{{ $activity->user->name ?? 'User' }}</span> 
                                                {{ $activity->description }}
                                            </p>
                                            <p class="text-[10px] text-gray-400">{{ $activity->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </section>
                </div>

            </div>
        @endif
    </div>
</x-app-layout>
