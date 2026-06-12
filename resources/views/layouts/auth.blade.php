<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Jejak Hijau') }}</title>

        <meta name="description" content="GreenRun – Platform lari berbasis dampak lingkungan. Bergabunglah dan jadikan setiap langkahmu berarti.">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased min-h-screen">
        <div class="min-h-screen flex flex-col lg:flex-row">

            {{-- Hero Section (Left) --}}
            <div class="auth-hero relative hidden lg:flex lg:w-1/2 xl:w-3/5 flex-col justify-between p-10 xl:p-14"
                 style="background-image: url('{{ asset('images/hero-forest.jpg') }}'); background-size: cover; background-position: center;">

                {{-- Overlay is handled via auth-hero::after in CSS --}}

                {{-- Content on top of overlay --}}
                <div class="relative z-10">
                    {{-- Logo --}}
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 group">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                             style="background: rgba(46,207,137,0.25); border: 1px solid rgba(46,207,137,0.4);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"
                                      fill="#7BE0B3"/>
                                <path d="M17 8c-1.1 0-2 .9-2 2v4c0 1.1.9 2 2 2s2-.9 2-2v-4c0-1.1-.9-2-2-2z"
                                      fill="#2ECF89" opacity="0.8"/>
                                <path d="M7 8c-1.1 0-2 .9-2 2v4c0 1.1.9 2 2 2s2-.9 2-2v-4c0-1.1-.9-2-2-2z"
                                      fill="#2ECF89" opacity="0.6"/>
                            </svg>
                        </div>
                        <span class="text-white font-bold text-xl tracking-tight">Jejak Hijau</span>
                    </a>
                </div>

                {{-- Hero tagline --}}
                <div class="relative z-10 animate-fade-in-up">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full mb-5 text-xs font-semibold tracking-wide uppercase"
                         style="background: rgba(46,207,137,0.2); border: 1px solid rgba(46,207,137,0.35); color: #7BE0B3;">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Platform Lari Ramah Lingkungan
                    </div>

                    <h1 class="text-white font-bold leading-tight mb-4"
                        style="font-size: 42px;">
                        Setiap Langkahmu<br>
                        <span style="color: #2ECF89;">Memberi Dampak</span>
                    </h1>

                    <p class="text-white/70 text-base leading-relaxed mb-8 max-w-md">
                        Bergabunglah dengan ribuan pelari yang peduli lingkungan. Lari lebih bermakna — setiap kilometer menggerakkan aksi nyata untuk bumi.
                    </p>

                    {{-- Statistics --}}
                    <div class="flex flex-wrap gap-3">
                        <x-stat-card value="25K+" label="Runners" />
                        <x-stat-card value="50+" label="Event Handled" />
                        <x-stat-card value="20+" label="Sponsor" />
                    </div>
                </div>
            </div>

            {{-- Auth Form Section (Right) --}}
            <div class="flex flex-1 flex-col justify-center items-center px-6 py-10 sm:px-10 lg:px-12 xl:px-16"
                 style="background: #F8F5F0;">

                {{-- Mobile logo --}}
                <div class="lg:hidden mb-8 text-center">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                             style="background: #003F2F;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"
                                      fill="#7BE0B3"/>
                            </svg>
                        </div>
                        <span class="font-bold text-xl tracking-tight" style="color: #003F2F;">Jejak Hijau</span>
                    </a>
                </div>

                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>

                {{-- Footer --}}
                <p class="mt-10 text-center text-xs" style="color: #6B7280;">
                    &copy; {{ date('Y') }} Jejak Hijau. Hak cipta dilindungi.
                </p>
            </div>
        </div>
    </body>
</html>
