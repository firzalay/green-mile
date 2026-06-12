@props(['title' => config('app.name', 'Jejak Hijau'), 'user'])

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

    $currentRoute = $user->isOrganizer()
        ? (request()->routeIs('organizer.dashboard') ? 'organizer-home'
            : (request()->routeIs('organizer.placeholder') && request()->route('action') === 'create-event' ? 'organizer-create'
            : (request()->routeIs('organizer.placeholder') && request()->route('action') === 'checkpoints' ? 'organizer-checkpoints'
            : (request()->routeIs('organizer.placeholder') && request()->route('action') === 'qr-generation' ? 'organizer-qr'
            : (request()->routeIs('organizer.placeholder') && request()->route('action') === 'participants' ? 'organizer-participants'
            : '')))))
        : (request()->routeIs('dashboard') ? 'home'
            : (request()->routeIs('events*') ? 'events'
            : (request()->routeIs('qr*') ? 'qr'
            : (request()->routeIs('leaderboard*') ? 'leaderboard'
            : (request()->routeIs('reward*') ? 'reward'
            : (request()->routeIs('history*') ? 'history'
            : ''))))));

    $navItems = $user->isOrganizer()
        ? [
            ['id' => 'sidebar-organizer-dashboard', 'route' => route('organizer.dashboard'), 'key' => 'organizer-home', 'label' => 'Dashboard', 'icon' => 'home'],
            ['id' => 'sidebar-organizer-create',    'route' => route('organizer.placeholder', 'create-event'), 'key' => 'organizer-create', 'label' => 'Buat Event', 'icon' => 'plus-circle'],
            ['id' => 'sidebar-organizer-checkpoints', 'route' => route('organizer.placeholder', 'checkpoints'), 'key' => 'organizer-checkpoints', 'label' => 'Kelola Checkpoint', 'icon' => 'map-pin'],
            ['id' => 'sidebar-organizer-qr',        'route' => route('organizer.placeholder', 'qr-generation'), 'key' => 'organizer-qr', 'label' => 'Generate QR', 'icon' => 'qr'],
            ['id' => 'sidebar-organizer-participants', 'route' => route('organizer.placeholder', 'participants'), 'key' => 'organizer-participants', 'label' => 'Lihat Peserta', 'icon' => 'users'],
        ]
        : [
            ['id' => 'sidebar-home',        'route' => route('dashboard'), 'key' => 'home',        'label' => 'Dashboard',    'icon' => 'home'],
            ['id' => 'sidebar-events',      'route' => route('events.index'), 'key' => 'events',    'label' => 'Daftar Event', 'icon' => 'calendar'],
            ['id' => 'sidebar-leaderboard', 'route' => '#',                'key' => 'leaderboard', 'label' => 'Leaderboard',  'icon' => 'trophy'],
            ['id' => 'sidebar-qr',          'route' => '#',                'key' => 'qr',          'label' => 'QR Scanner',   'icon' => 'qr'],
            ['id' => 'sidebar-reward',      'route' => '#',                'key' => 'reward',      'label' => 'Reward',       'icon' => 'gift'],
            ['id' => 'sidebar-history',     'route' => '#',                'key' => 'history',     'label' => 'Riwayat',      'icon' => 'clock'],
            ['id' => 'sidebar-profile',     'route' => '#',                'key' => 'profile',     'label' => 'Profil',       'icon' => 'user'],
            ['id' => 'sidebar-settings',    'route' => '#',                'key' => 'settings',    'label' => 'Pengaturan',   'icon' => 'settings'],
        ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title }}</title>
        <meta name="description" content="GreenRun – Dashboard peserta. Pantau progres event larimu.">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased" style="background: #F3F4F6; font-family: 'Inter', sans-serif; margin: 0;">

        {{-- ============================================================
             DESKTOP LAYOUT (lg+): Sidebar + Top Navbar + Content
        ============================================================ --}}
        <div class="hidden lg:flex min-h-screen">

            {{-- ── Sidebar ── --}}
            <aside id="desktop-sidebar"
                   class="flex flex-col flex-shrink-0 sticky top-0 h-screen transition-all duration-300"
                   style="width: 280px; background: #003F2F;">

                {{-- Logo --}}
                <div class="flex items-center gap-3 px-6 py-5" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background: rgba(46,207,137,0.2); border: 1px solid rgba(46,207,137,0.35);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 3C9 3 6.5 4.5 5 7c-.5 1-1 2.5-1 4s.5 3.5 2 5l6 5 6-5c1.5-1.5 2-3.5 2-5s-.5-3-1-4C17.5 4.5 15 3 12 3z"
                                  fill="#2ECF89"/>
                            <path d="M12 8v5M9.5 11l2.5 2 2.5-2" stroke="#003F2F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-white text-base leading-none tracking-tight">Jejak Hijau</p>
                        <p class="text-xs mt-0.5" style="color: rgba(123,224,179,0.7);">GreenRun Platform</p>
                    </div>
                </div>

                {{-- User Card --}}
                <div class="mx-4 my-4 rounded-2xl p-3.5 flex items-center gap-3"
                     style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm text-white flex-shrink-0"
                         style="background: linear-gradient(135deg, #2ECF89, #7BE0B3);">
                        {{ $initials }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-white text-sm leading-tight truncate">{{ $user->name }}</p>
                        <p class="text-xs truncate" style="color: rgba(255,255,255,0.5);">{{ $user->email }}</p>
                    </div>
                </div>

                {{-- Navigation --}}
                <nav class="flex-1 px-3 pb-4 overflow-y-auto">
                    <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-widest"
                       style="color: rgba(255,255,255,0.3);">Menu</p>

                    @foreach ($navItems as $item)
                        @php
                            $isActive = $currentRoute === $item['key'];
                        @endphp
                        <a href="{{ $item['route'] }}"
                           id="{{ $item['id'] }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl mb-1 transition-all duration-150 group"
                           style="
                               background: {{ $isActive ? 'rgba(46,207,137,0.15)' : 'transparent' }};
                               color: {{ $isActive ? '#2ECF89' : 'rgba(255,255,255,0.6)' }};
                               border: {{ $isActive ? '1px solid rgba(46,207,137,0.2)' : '1px solid transparent' }};
                           ">
                            {{-- Icon --}}
                            <span class="flex-shrink-0">
                                @if ($item['icon'] === 'home')
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" fill="{{ $isActive ? 'rgba(46,207,137,0.15)' : 'none' }}"/>
                                        <polyline points="9 22 9 12 15 12 15 22" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                @elseif ($item['icon'] === 'calendar')
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2" fill="{{ $isActive ? 'rgba(46,207,137,0.15)' : 'none' }}"/>
                                        <line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                @elseif ($item['icon'] === 'plus-circle')
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="{{ $isActive ? 'rgba(46,207,137,0.15)' : 'none' }}"/>
                                        <line x1="12" y1="8" x2="12" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <line x1="8" y1="12" x2="16" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                @elseif ($item['icon'] === 'map-pin')
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" stroke="currentColor" stroke-width="2" fill="{{ $isActive ? 'rgba(46,207,137,0.15)' : 'none' }}"/>
                                        <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                @elseif ($item['icon'] === 'users')
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 7a4 4 0 1 0 0-8 4 4 0 0 0 0 8z" stroke="currentColor" stroke-width="2" fill="{{ $isActive ? 'rgba(46,207,137,0.15)' : 'none' }}"/>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                @elseif ($item['icon'] === 'trophy')
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 9H3V3h3M18 9h3V3h-3M8 21h8M12 17v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M6 3h12v8a6 6 0 0 1-12 0V3z" stroke="currentColor" stroke-width="2" fill="{{ $isActive ? 'rgba(46,207,137,0.15)' : 'none' }}"/>
                                    </svg>
                                @elseif ($item['icon'] === 'qr')
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="3" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                                        <rect x="14" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                                        <rect x="3" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                                        <path d="M14 14h2v2h-2zM18 14h3M14 18h3M18 18h3v3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                @elseif ($item['icon'] === 'gift')
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="2" y="7" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2" fill="{{ $isActive ? 'rgba(46,207,137,0.15)' : 'none' }}"/>
                                        <path d="M16 7c0-2.21-1.79-4-4-4S8 4.79 8 7" stroke="currentColor" stroke-width="2"/>
                                        <path d="M12 7v14M2 12h20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                @elseif ($item['icon'] === 'clock')
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="{{ $isActive ? 'rgba(46,207,137,0.15)' : 'none' }}"/>
                                        <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                @elseif ($item['icon'] === 'user')
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2" fill="{{ $isActive ? 'rgba(46,207,137,0.15)' : 'none' }}"/>
                                        <path d="M4 20c0-4 3.582-7 8-7s8 3 8 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                @elseif ($item['icon'] === 'settings')
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                @endif
                            </span>
                            <span class="text-sm font-medium">{{ $item['label'] }}</span>
                            @if ($isActive)
                                <span class="ml-auto w-1.5 h-1.5 rounded-full flex-shrink-0" style="background: #2ECF89;"></span>
                            @endif
                        </a>
                    @endforeach
                </nav>

                {{-- Logout --}}
                <div class="px-3 pb-5" style="border-top: 1px solid rgba(255,255,255,0.08); padding-top: 12px;">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                id="sidebar-logout"
                                class="flex items-center gap-3 px-3 py-2.5 w-full rounded-xl transition-all duration-150 hover:bg-red-500/10 text-left"
                                style="color: rgba(255,255,255,0.4);">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="text-sm font-medium">Keluar</span>
                        </button>
                    </form>
                </div>
            </aside>

            {{-- ── Main Area (Top Navbar + Content) ── --}}
            <div class="flex flex-col flex-1 min-w-0">

                {{-- Desktop Top Navbar --}}
                <header class="flex items-center justify-between px-8 flex-shrink-0 sticky top-0 z-20"
                        style="height: 72px; background: #FFFFFF; border-bottom: 1px solid #E5E7EB; box-shadow: 0px 1px 4px rgba(0,0,0,0.04);">

                    {{-- Page title / greeting --}}
                    <div>
                        <h1 class="font-bold text-lg leading-none" style="color: #111827;">
                            {{ $greeting }}, {{ $firstName }} 👋
                        </h1>
                        <p class="text-xs mt-0.5" style="color: #9CA3AF;">
                            {{ now()->translatedFormat('l, d F Y') }}
                        </p>
                    </div>

                    {{-- Right: Notification + Avatar --}}
                    <div class="flex items-center gap-3">

                        {{-- Notification bell --}}
                        <button id="btn-notification"
                                type="button"
                                aria-label="Notifikasi"
                                class="w-10 h-10 rounded-xl flex items-center justify-center relative transition-all hover:bg-gray-100"
                                style="color: #6B7280;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            {{-- Unread dot --}}
                            <span class="absolute top-2 right-2 w-2 h-2 rounded-full" style="background: #EF4444;"></span>
                        </button>

                        {{-- Avatar + name --}}
                        <a href="#"
                           id="nav-profile-avatar-desktop"
                           class="flex items-center gap-2.5 pl-3 transition-opacity hover:opacity-80"
                           style="border-left: 1px solid #E5E7EB;">
                            <div class="text-right hidden sm:block">
                                <p class="text-sm font-semibold leading-none" style="color: #111827;">{{ $firstName }}</p>
                                <p class="text-xs mt-0.5" style="color: #9CA3AF;">Peserta</p>
                            </div>
                            <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm text-white"
                                 style="background: linear-gradient(135deg, #003F2F 0%, #2ECF89 100%);">
                                {{ $initials }}
                            </div>
                        </a>
                    </div>
                </header>

                {{-- Desktop Content Area --}}
                <main class="flex-1 overflow-y-auto p-8">
                    <div class="max-w-5xl mx-auto">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        {{-- ============================================================
             MOBILE LAYOUT (< lg): Top Bar + Content + Bottom Nav
        ============================================================ --}}
        <div class="lg:hidden min-h-screen flex flex-col" style="max-width: 480px; margin: 0 auto; background: #F8F5F0;">

            {{-- Mobile Top Bar --}}
            <x-dashboard-header :user="$user" />

            {{-- Mobile Content --}}
            <main class="flex-1 px-4 pb-28 pt-4 overflow-y-auto">
                {{ $slot }}
            </main>

            {{-- Mobile Bottom Nav --}}
            @if ($user->role !== 'organizer')
                <x-bottom-navigation />
            @endif
        </div>

    </body>
</html>
