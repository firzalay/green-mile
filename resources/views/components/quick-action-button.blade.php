@props(['href' => '#'])

<a href="{{ $href }}"
   id="btn-scan-qr"
   class="flex items-center justify-center gap-3 w-full rounded-2xl py-4 font-bold text-white text-base transition-all duration-200 hover:opacity-90 active:scale-95"
   style="background: linear-gradient(135deg, #2ECF89 0%, #003F2F 100%); box-shadow: 0px 8px 24px rgba(46,207,137,0.35);">

    {{-- QR icon --}}
    <div class="w-8 h-8 rounded-xl flex items-center justify-center"
         style="background: rgba(255,255,255,0.2);">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="3" y="3" width="7" height="7" rx="1" stroke="white" stroke-width="2"/>
            <rect x="14" y="3" width="7" height="7" rx="1" stroke="white" stroke-width="2"/>
            <rect x="3" y="14" width="7" height="7" rx="1" stroke="white" stroke-width="2"/>
            <path d="M14 14h2v2h-2zM18 14h3M14 18h3M18 18h3v3" stroke="white" stroke-width="2" stroke-linecap="round"/>
        </svg>
    </div>

    Scan QR Sekarang

    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M5 12h14M13 6l6 6-6 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</a>
