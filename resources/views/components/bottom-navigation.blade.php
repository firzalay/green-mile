@php
    $currentRoute = request()->routeIs('dashboard') ? 'home'
        : (request()->routeIs('qr*') ? 'qr'
        : (request()->routeIs('leaderboard*') ? 'leaderboard'
        : (request()->routeIs('reward*') ? 'reward'
        : (request()->routeIs('history*') ? 'history'
        : ''))));

    $navItems = [
        ['id' => 'nav-home',        'route' => route('dashboard'), 'key' => 'home',        'label' => 'Home',       'icon' => 'home'],
        ['id' => 'nav-leaderboard', 'route' => '#', 'key' => 'leaderboard', 'label' => 'Ranking',    'icon' => 'trophy'],
        ['id' => 'nav-qr',          'route' => '#', 'key' => 'qr',          'label' => 'Scan',       'icon' => 'qr',    'center' => true],
        ['id' => 'nav-reward',      'route' => '#', 'key' => 'reward',      'label' => 'Reward',     'icon' => 'gift'],
        ['id' => 'nav-history',     'route' => '#', 'key' => 'history',     'label' => 'Riwayat',    'icon' => 'clock'],
    ];
@endphp

<nav class="fixed bottom-0 w-full max-w-[480px] z-30"
     style="background: #FFFFFF; border-top: 1px solid #E5E7EB; box-shadow: 0px -4px 16px rgba(0,0,0,0.06);">
    <div class="flex items-center justify-around px-2 py-2">
        @foreach ($navItems as $item)
            @php
                $isActive = $currentRoute === $item['key'];
                $isCenter = $item['center'] ?? false;
            @endphp

            @if ($isCenter)
                {{-- QR center action button --}}
                <a href="{{ $item['route'] }}"
                   id="{{ $item['id'] }}"
                   class="flex flex-col items-center -mt-5 transition-transform active:scale-95">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg"
                         style="background: linear-gradient(135deg, #2ECF89, #003F2F); box-shadow: 0px 4px 16px rgba(46,207,137,0.4);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="3" width="7" height="7" rx="1" stroke="white" stroke-width="2"/>
                            <rect x="14" y="3" width="7" height="7" rx="1" stroke="white" stroke-width="2"/>
                            <rect x="3" y="14" width="7" height="7" rx="1" stroke="white" stroke-width="2"/>
                            <path d="M14 14h2v2h-2zM18 14h3M14 18h3M18 18h3v3" stroke="white" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <span class="text-xs mt-1 font-semibold" style="color: #2ECF89;">{{ $item['label'] }}</span>
                </a>
            @else
                <a href="{{ $item['route'] }}"
                   id="{{ $item['id'] }}"
                   class="flex flex-col items-center gap-1 px-3 py-1 rounded-xl transition-all"
                   style="color: {{ $isActive ? '#003F2F' : '#9CA3AF' }};">
                    @if ($item['icon'] === 'home')
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"
                                  stroke="currentColor" stroke-width="2" stroke-linejoin="round"
                                  fill="{{ $isActive ? 'rgba(0,63,47,0.12)' : 'none' }}"/>
                            <polyline points="9 22 9 12 15 12 15 22" stroke="currentColor" stroke-width="2"/>
                        </svg>
                    @elseif ($item['icon'] === 'trophy')
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 9H3V3h3M18 9h3V3h-3M8 21h8M12 17v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M6 3h12v8a6 6 0 0 1-12 0V3z" stroke="currentColor" stroke-width="2"
                                  fill="{{ $isActive ? 'rgba(0,63,47,0.12)' : 'none' }}"/>
                        </svg>
                    @elseif ($item['icon'] === 'gift')
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="2" y="7" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"
                                  fill="{{ $isActive ? 'rgba(0,63,47,0.12)' : 'none' }}"/>
                            <path d="M16 7c0-2.21-1.79-4-4-4S8 4.79 8 7" stroke="currentColor" stroke-width="2"/>
                            <path d="M12 7v14M2 12h20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    @elseif ($item['icon'] === 'clock')
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"
                                    fill="{{ $isActive ? 'rgba(0,63,47,0.12)' : 'none' }}"/>
                            <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    @endif
                    <span class="text-xs font-{{ $isActive ? 'semibold' : 'medium' }}">{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    </div>
</nav>
