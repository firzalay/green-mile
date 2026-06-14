@props(['title' => config('app.name', 'Jejak Hijau')])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title }}</title>

        <meta name="description" content="GreenRun – Platform lari berbasis dampak lingkungan. Bergabunglah dan jadikan setiap langkahmu berarti.">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased min-h-screen">
        <div class="min-h-screen flex flex-col lg:flex-row">

            {{-- Hero Section (Left) --}}
            <div class="auth-hero relative hidden lg:flex lg:w-1/2 xl:w-3/5 flex-col justify-between p-10 xl:p-14"
                 style="background-image: url('{{ asset('images/hero-forest.jpg') }}'); background-size: cover; background-position: center;">

                {{-- Content on top of overlay --}}
                <div class="relative z-10">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                             style="background: rgba(46,207,137,0.25);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 3C9 3 6.5 4.5 5 7c-.5 1-1 2.5-1 4s.5 3.5 2 5l6 5 6-5c1.5-1.5 2-3.5 2-5s-.5-3-1-4C17.5 4.5 15 3 12 3z"
                                      fill="#2ECF89" opacity="0.9"/>
                                <path d="M12 7v10M9 9l3-2 3 2" stroke="#003F2F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <span class="text-white font-bold text-xl tracking-tight">Jejak Hijau</span>
                    </a>
                </div>

                {{-- Hero tagline --}}
                <div class="relative z-10 animate-fade-in-up">
       
                    <h1 class="text-white font-bold leading-tight mb-4"
                        style="font-size: 42px; text-shadow: 0 2px 16px rgba(0,0,0,0.3);">
                        Setiap Langkahmu<br>
                        Memberi Dampak</span>
                    </h1>

                    <p class="text-white/70 text-base leading-relaxed mb-8 max-w-md">
                        Bergabunglah dengan ribuan pelari yang peduli lingkungan. Lari lebih bermakna setiap kilometer menggerakkan aksi nyata untuk bumi.
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
                                <path d="M12 3C9 3 6.5 4.5 5 7c-.5 1-1 2.5-1 4s.5 3.5 2 5l6 5 6-5c1.5-1.5 2-3.5 2-5s-.5-3-1-4C17.5 4.5 15 3 12 3z"
                                      fill="#2ECF89" opacity="0.9"/>
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
