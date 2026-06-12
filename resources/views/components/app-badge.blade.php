@props(['label' => 'GreenRun App'])

<div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold"
     style="background: rgba(46,207,137,0.12); border: 1px solid rgba(46,207,137,0.3); color: #003F2F;">
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="#2ECF89" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    {{ $label }}
</div>