@props(['currentEventPoints', 'totalPoints'])

<div class="grid grid-cols-2 gap-3">
    {{-- Current event points --}}
    <div class="rounded-2xl p-4" style="background: #FFFFFF; box-shadow: 0px 2px 8px rgba(0,0,0,0.08);">
        <div class="w-8 h-8 rounded-xl flex items-center justify-center mb-3"
             style="background: rgba(46,207,137,0.12);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14l-5-4.87 6.91-1.01L12 2z"
                      fill="#2ECF89"/>
            </svg>
        </div>
        <p class="text-xs font-medium mb-0.5" style="color: #6B7280;">Poin Event Ini</p>
        <p class="font-bold text-xl leading-none" style="color: #111827;">{{ number_format($currentEventPoints) }}</p>
        <p class="text-xs mt-0.5" style="color: #2ECF89;">pts</p>
    </div>

    {{-- Total accumulation points --}}
    <div class="rounded-2xl p-4" style="background: #003F2F; box-shadow: 0px 2px 8px rgba(0,63,47,0.2);">
        <div class="w-8 h-8 rounded-xl flex items-center justify-center mb-3"
             style="background: rgba(46,207,137,0.2);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" fill="#2ECF89"/>
            </svg>
        </div>
        <p class="text-xs font-medium mb-0.5" style="color: rgba(255,255,255,0.6);">Total Poin</p>
        <p class="font-bold text-xl leading-none text-white">{{ number_format($totalPoints) }}</p>
        <p class="text-xs mt-0.5" style="color: #7BE0B3;">pts akumulasi</p>
    </div>
</div>
