<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'GreenMile') }}</title>

        <meta name="description" content="GreenRun – Platform lari berbasis dampak lingkungan.">

        {{-- PWA Meta Tags & Manifest --}}
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#1A3A2A">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <link rel="apple-touch-icon" href="/icons/icon-192x192.png">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js')
                        .then(reg => console.log('Service Worker registered', reg))
                        .catch(err => console.log('Service Worker registration failed', err));
                });
            }

            let deferredPrompt;
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                const installContainer = document.getElementById('pwa-install-container');
                if (installContainer) {
                    installContainer.classList.remove('hidden');
                }
            });

            window.addEventListener('DOMContentLoaded', () => {
                const installBtn = document.getElementById('pwa-install-btn');
                if (installBtn) {
                    installBtn.addEventListener('click', async () => {
                        if (!deferredPrompt) return;
                        deferredPrompt.prompt();
                        const { outcome } = await deferredPrompt.userChoice;
                        console.log(`User response to the install prompt: ${outcome}`);
                        deferredPrompt = null;
                        const installContainer = document.getElementById('pwa-install-container');
                        if (installContainer) {
                            installContainer.classList.add('hidden');
                        }
                    });
                }

                if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
                    const installContainer = document.getElementById('pwa-install-container');
                    if (installContainer) {
                        installContainer.classList.add('hidden');
                    }
                }
            });

            window.addEventListener('appinstalled', (event) => {
                console.log('App was installed.');
                const installContainer = document.getElementById('pwa-install-container');
                if (installContainer) {
                    installContainer.classList.add('hidden');
                }
                deferredPrompt = null;
            });
        </script>
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
