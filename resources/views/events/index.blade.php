<x-app-layout title="Daftar Event – GreenMile" :user="$user">
    <div class="space-y-6">
        {{-- Header Section --}}
        <section class="animate-fade-in-up flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl" style="color: #111827;">Eksplorasi Event</h2>
                <p class="text-sm mt-1" style="color: #6B7280;">
                    Temukan event lari ramah lingkungan di sekitarmu, mulai kumpulkan poin, dan berkontribusi untuk bumi!
                </p>
            </div>
            <a href="{{ route('events.join') }}" 
               id="btn-join-event-page"
               class="btn-primary inline-flex items-center justify-center gap-2 h-11 px-5 text-sm font-semibold rounded-xl"
               style="background-color: #003F2F; color: #ffffff;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                    <line x1="12" y1="8" x2="12" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="8" y1="12" x2="16" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                Masukkan Kode Event
            </a>
        </section>

        {{-- Events Grid --}}
        @if($events->isEmpty())
            <section class="flex flex-col items-center justify-center py-16 text-center animate-fade-in-up">
                <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4"
                     style="background: rgba(107, 114, 128, 0.08); border: 2px dashed rgba(107, 114, 128, 0.3);">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="4" width="18" height="18" rx="2" stroke="#6B7280" stroke-width="2"/>
                        <path d="M16 2v4M8 2v4M3 10h18" stroke="#6B7280" stroke-width="2"/>
                    </svg>
                </div>
                <h3 class="font-bold text-lg mb-1" style="color: #111827;">Tidak Ada Event</h3>
                <p class="text-sm max-w-xs" style="color: #6B7280;">
                    Saat ini belum ada event aktif yang tersedia untuk diikuti.
                </p>
            </section>
        @else
            <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in-up animate-delay-100">
                @foreach ($events as $event)
                    @php
                        $isJoined = in_array($event->id, $joinedEventIds);
                        $status = $event->status; // Upcoming, Ongoing, Finished
                    @endphp

                    {{-- Event Card --}}
                    <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 flex flex-col group border border-gray-100">
                        {{-- Banner Image / Fallback --}}
                        <div class="relative h-44 w-full overflow-hidden bg-emerald-900">
                            @if ($event->banner_image)
                                <img src="{{ $event->banner_image }}" 
                                     alt="{{ $event->name }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-white/40 font-bold" 
                                     style="background: linear-gradient(135deg, #003F2F 0%, #2ECF89 100%);">
                                    GreenRun
                                </div>
                            @endif
                            {{-- Dark Overlay --}}
                            <div class="absolute inset-0 bg-black/20"></div>

                            {{-- Status Badge on Image --}}
                            <div class="absolute top-3 right-3">
                                @if ($status === 'Ongoing')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold shadow-sm"
                                          style="background: #2ECF89; color: #ffffff;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                        Ongoing
                                    </span>
                                @elseif ($status === 'Upcoming')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold shadow-sm"
                                          style="background: #FF7A45; color: #ffffff;">
                                        Upcoming
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold shadow-sm"
                                          style="background: #6B7280; color: #ffffff;">
                                        Finished
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Card Details --}}
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                            <div class="space-y-2">
                                <h3 class="font-bold text-base leading-tight group-hover:text-emerald transition-colors" style="color: #111827;">
                                    {{ $event->name }}
                                </h3>

                                {{-- Meta List --}}
                                <div class="space-y-1.5">
                                    {{-- Location --}}
                                    <div class="flex items-center gap-2 text-xs" style="color: #6B7280;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z" 
                                                  fill="currentColor"/>
                                        </svg>
                                        <span class="truncate">{{ $event->location }}</span>
                                    </div>
                                    {{-- Date --}}
                                    <div class="flex items-center gap-2 text-xs" style="color: #6B7280;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                                            <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2"/>
                                        </svg>
                                        <span>{{ $event->event_date ? $event->event_date->translatedFormat('d M Y') : '-' }}</span>
                                    </div>
                                    {{-- Participants Count --}}
                                    <div class="flex items-center gap-2 text-xs" style="color: #6B7280;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 7a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm14 14v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" 
                                                  stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        <span>{{ $event->participants_count }} Peserta bergabung</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer / Action Button --}}
                            <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                                @if ($isJoined)
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded bg-emerald/10 text-emerald">
                                        Joined
                                    </span>
                                @else
                                    <span class="text-xs" style="color: #9CA3AF;">Belum bergabung</span>
                                @endif

                                <a href="{{ route('events.show', $event->id) }}" 
                                   id="btn-detail-{{ $event->id }}"
                                   class="btn-secondary h-9 px-4 text-xs">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </section>
        @endif
    </div>
</x-app-layout>
