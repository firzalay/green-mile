@props(['participation'])

@php
    $event = $participation->event;
    $completed = $participation->completed_checkpoints;
    $total = $event->total_checkpoints;
    $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;
@endphp

<div class="rounded-2xl overflow-hidden shadow-md"
     style="background: linear-gradient(135deg, #003F2F 0%, #005540 100%); box-shadow: 0px 8px 24px rgba(0,63,47,0.25);">

    {{-- Background decoration --}}
    <div class="relative p-5">
        <div class="absolute top-0 right-0 w-32 h-32 rounded-full opacity-10"
             style="background: #2ECF89; transform: translate(30%, -30%);"></div>
        <div class="absolute bottom-0 left-0 w-20 h-20 rounded-full opacity-5"
             style="background: #7BE0B3; transform: translate(-30%, 30%);"></div>

        {{-- Event badge --}}
        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full mb-3 text-xs font-semibold"
             style="background: rgba(46,207,137,0.2); border: 1px solid rgba(46,207,137,0.35); color: #7BE0B3;">
            <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background: #2ECF89;"></span>
            Event Aktif
        </div>

        {{-- Event name --}}
        <h3 class="text-white font-bold text-base leading-tight mb-1">{{ $event->name }}</h3>

        {{-- Meta --}}
        <div class="flex flex-wrap gap-3 mb-4">
            <div class="flex items-center gap-1.5 text-white/70 text-xs">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                    <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                {{ $event->event_date ? $event->event_date->translatedFormat('d M Y') : '-' }}
            </div>
            <div class="flex items-center gap-1.5 text-white/70 text-xs">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"
                          fill="currentColor" opacity="0.7"/>
                </svg>
                {{ $event->location }}
            </div>
        </div>

        {{-- Checkpoint progress --}}
        <div class="mb-2 flex items-center justify-between">
            <span class="text-white/80 text-xs font-medium">Checkpoint Progress</span>
            <span class="font-bold text-sm" style="color: #2ECF89;">{{ $completed }} / {{ $total }}</span>
        </div>

        <div class="w-full h-2 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.15);">
            <div class="h-full rounded-full transition-all duration-500"
                 style="width: {{ $percentage }}%; background: linear-gradient(90deg, #2ECF89, #7BE0B3);">
            </div>
        </div>

        <p class="text-white/50 text-xs mt-1.5">{{ $percentage }}% selesai</p>
    </div>
</div>
