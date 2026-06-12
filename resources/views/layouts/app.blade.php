<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Jejak Hijau') }}</title>

        <meta name="description" content="GreenRun – Platform lari berbasis dampak lingkungan.">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased" style="background: #F8F5F0; font-family: 'Inter', sans-serif;">

        {{-- Mobile-first: full screen, no scrollbar on body --}}
        <div class="min-h-screen flex flex-col" style="max-width: 480px; margin: 0 auto; position: relative;">

            {{-- Top Bar --}}
            <x-dashboard-header :user="$user ?? auth()->user()" />

            {{-- Main Content --}}
            <main class="flex-1 px-4 pb-24 pt-4 overflow-y-auto">
                {{ $slot }}
            </main>

            {{-- Bottom Navigation --}}
            <x-bottom-navigation />
        </div>
    </body>
</html>
