@props(['user'])

@php
    $hour = now()->hour;
    $greeting = match(true) {
        $hour < 12 => 'Selamat Pagi',
        $hour < 15 => 'Selamat Siang',
        $hour < 18 => 'Selamat Sore',
        default    => 'Selamat Malam',
    };
    $firstName = explode(' ', $user->name)[0];
    $initials = collect(explode(' ', $user->name))
        ->map(fn ($w) => strtoupper(substr($w, 0, 1)))
        ->take(2)
        ->implode('');
@endphp

<header class="flex items-center justify-between px-4 py-4 sticky top-0 z-20"
        style="background: #F8F5F0; border-bottom: 1px solid #E5E7EB;">
    {{-- Logo + Greeting --}}
    <div>
        <div class="flex items-center gap-1.5 mb-0.5">
            <div class="w-5 h-5 rounded flex items-center justify-center" style="background: #003F2F;">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 3C9 3 6.5 4.5 5 7c-.5 1-1 2.5-1 4s.5 3.5 2 5l6 5 6-5c1.5-1.5 2-3.5 2-5s-.5-3-1-4C17.5 4.5 15 3 12 3z"
                          fill="#2ECF89"/>
                </svg>
            </div>
            <span class="text-xs font-semibold tracking-tight" style="color: #003F2F;">Jejak Hijau</span>
        </div>
        <p class="text-xs" style="color: #6B7280;">{{ $greeting }},</p>
        <p class="font-bold text-base leading-tight" style="color: #111827;">{{ $firstName }} 👋</p>
    </div>

    {{-- Avatar --}}
    <a href="{{ route('profile.show') }}" aria-label="Lihat Profil" id="nav-profile-avatar">
        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm text-white shadow-sm transition-transform hover:scale-105 overflow-hidden"
             style="background: linear-gradient(135deg, #003F2F 0%, #2ECF89 100%);">
            @if($user->avatar)
                <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
            @else
                {{ $initials }}
            @endif
        </div>
    </a>
</header>
