<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Scan QR Checkpoint – Jejak Hijau</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        #reader__dashboard,
        #reader__header_message {
            display: none !important;
        }

        #reader__scan_region {
            border: none !important;
        }

        #reader video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            border-radius: 1rem;
        }

        /* Overlay & Scanning elements */
        .overlay {
            position: absolute;
            inset: 0;
            pointer-events: none;
        }

        .scan-box {
            position: absolute;
            width: 220px;
            height: 220px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .scan-box::before {
            content: "";
            position: absolute;
            inset: -1000px;
            border: 1000px solid rgba(0, 0, 0, .45);
        }

        .corner {
            position: absolute;
            width: 28px;
            height: 28px;
            border: 4px solid #2ECF89;
        }

        .top-left {
            top: -4px;
            left: -4px;
            border-right: none;
            border-bottom: none;
        }

        .top-right {
            top: -4px;
            right: -4px;
            border-left: none;
            border-bottom: none;
        }

        .bottom-left {
            bottom: -4px;
            left: -4px;
            border-right: none;
            border-top: none;
        }

        .bottom-right {
            bottom: -4px;
            right: -4px;
            border-left: none;
            border-top: none;
        }

        .scan-line {
            position: absolute;
            left: 0;
            width: 100%;
            height: 3px;
            background: #2ECF89;
            box-shadow: 0 0 10px #2ECF89;
            animation: scan 2s linear infinite;
        }

        @keyframes scan {
            0% {
                top: 0;
            }
            100% {
                top: calc(100% - 3px);
            }
        }
    </style>
</head>
<body class="antialiased" style="background: #F8F5F0; font-family: 'Inter', sans-serif;">

    {{-- Mobile Container (matches GreenRun's mobile layout style) --}}
    <div class="min-h-screen flex flex-col" style="max-width: 480px; margin: 0 auto; position: relative;">
        
        {{-- Top Bar Header --}}
        <x-dashboard-header :user="auth()->user()" />

        {{-- Main Scroll Content Area --}}
        <main class="flex-1 px-4 pb-28 pt-4 overflow-y-auto">
            
            <div class="max-w-md mx-auto space-y-6 px-1">
                {{-- Header Card --}}
                <div class="bg-white rounded-3xl p-6 shadow-card border border-gray-100/80 animate-fade-in-up">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-emerald/10 text-emerald flex-shrink-0">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="3" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2" />
                                <rect x="14" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2" />
                                <rect x="3" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2" />
                                <path d="M14 14h2v2h-2zM18 14h3M14 18h3M18 18h3v3" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-lg text-forest leading-tight">Scan Checkpoint</h2>
                            <p class="text-xs text-gray-500 mt-1">Scan QR Code pada checkpoint untuk menyelesaikan rute dan klaim poin Anda.</p>
                        </div>
                    </div>
                </div>

                {{-- Scanner Panel Card --}}
                <div class="bg-white rounded-3xl overflow-hidden shadow-card border border-gray-100/80 animate-fade-in-up animate-delay-100">
                    
                    {{-- State 1: Scanning --}}
                    <div id="state-scanning" class="p-6 flex flex-col items-center">
                        
                        {{-- Camera Feed Container --}}
                        <div class="relative w-full max-w-[280px] aspect-square rounded-2xl overflow-hidden bg-black border border-emerald/20 shadow-inner flex items-center justify-center">
                            
                            {{-- HTML5 QR Code element --}}
                            <div id="reader" class="w-full h-full"></div>

                            {{-- Scanning Overlay Frame --}}
                            <div class="overlay">
                                <div class="scan-box">
                                    <div class="corner top-left"></div>
                                    <div class="corner top-right"></div>
                                    <div class="corner bottom-left"></div>
                                    <div class="corner bottom-right"></div>

                                    <div class="scan-line"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Scanner Feedback / Status --}}
                        <div class="mt-6 text-center w-full">
                            <p id="scanner-instruction" class="text-sm font-semibold text-gray-700">Arahkan kamera ke QR Code checkpoint</p>
                            <p class="text-xs text-gray-400 mt-1">Pastikan pencahayaan cukup dan QR Code terlihat jelas.</p>
                            
                            <div class="mt-4 flex flex-col gap-2 max-w-xs mx-auto">
                                <select id="camera-select" class="hidden w-full border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-600 bg-gray-50 focus:outline-none focus:border-emerald"></select>
                            </div>
                        </div>
                    </div>

                    {{-- State 2: Processing (Loading Spinner) --}}
                    <div id="state-processing" class="hidden p-12 flex flex-col items-center justify-center text-center">
                        <div class="relative w-16 h-16 mb-4">
                            <div class="absolute inset-0 rounded-full border-4 border-emerald/10"></div>
                            <div class="absolute inset-0 rounded-full border-4 border-t-emerald animate-spin"></div>
                        </div>
                        <h3 class="font-bold text-base text-forest mb-1">Memproses QR Code...</h3>
                        <p class="text-xs text-gray-500 max-w-xs">Mohon tunggu sebentar, sedang memverifikasi data dan mendaftarkan poin Anda.</p>
                    </div>

                    {{-- State 3: Success Card --}}
                    <div id="state-success" class="hidden p-8 flex flex-col items-center text-center">
                        <div class="w-16 h-16 rounded-full bg-emerald/10 text-emerald flex items-center justify-center mb-4 border border-emerald/20">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                                <polyline points="22 4 12 14.01 9 11.01" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-xl text-forest mb-2">Scan Berhasil!</h3>
                        <p id="success-message" class="text-sm text-gray-600 mb-6">Checkpoint berhasil discan!</p>

                        <div class="w-full bg-gray-50 rounded-2xl p-5 border border-gray-100 text-left mb-6 space-y-3 shadow-inner">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 font-medium">Checkpoint</span>
                                <span id="res-checkpoint-name" class="font-bold text-forest">-</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 font-medium">Poin Diperoleh</span>
                                <span id="res-points-awarded" class="font-bold text-emerald text-base">-</span>
                            </div>
                            <div class="h-px bg-gray-200/60 my-2"></div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 font-medium">Total Poin Event</span>
                                <span id="res-total-points" class="font-bold text-forest text-base">-</span>
                            </div>
                        </div>

                        <button id="btn-scan-again-success" type="button" class="btn-primary w-full shadow-lg" style="background: linear-gradient(135deg, #2ECF89 0%, #003F2F 100%); box-shadow: 0px 8px 24px rgba(46,207,137,0.35);">
                            Scan Lagi
                        </button>
                    </div>

                    {{-- State 4: Error Card --}}
                    <div id="state-error" class="hidden p-8 flex flex-col items-center text-center">
                        <div class="w-16 h-16 rounded-full bg-danger/10 text-danger flex items-center justify-center mb-4 border border-danger/20">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2.5" />
                                <line x1="12" y1="8" x2="12" y2="12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                                <line x1="12" y1="16" x2="12.01" y2="16" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-lg text-forest mb-2">Scan Gagal</h3>
                        <p id="error-message" class="text-sm text-gray-600 mb-6">QR Code tidak valid atau checkpoint tidak aktif.</p>

                        <button id="btn-scan-again-error" type="button" class="btn-primary w-full" style="background: var(--color-forest);">
                            Coba Lagi
                        </button>
                    </div>

                    {{-- State 5: Blocked/No Camera --}}
                    <div id="state-blocked" class="hidden p-8 flex flex-col items-center text-center">
                        <div class="w-16 h-16 rounded-full bg-warning/10 text-warning flex items-center justify-center mb-4 border border-warning/20">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <circle cx="12" cy="13" r="4" stroke="currentColor" stroke-width="2" />
                                <line x1="1" y1="1" x2="23" y2="23" stroke="currentColor" stroke-width="2" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-lg text-forest mb-2">Akses Kamera Dibutuhkan</h3>
                        <p class="text-sm text-gray-600 mb-6 max-w-xs">
                            Fitur ini memerlukan akses kamera perangkat untuk men-scan QR Code checkpoint. Izinkan akses kamera di browser Anda.
                        </p>

                        <button id="btn-retry-permission" type="button" class="btn-primary w-full" style="background: var(--color-forest);">
                            Izinkan Kamera
                        </button>
                    </div>

                </div>
            </div>

        </main>

        {{-- Bottom Navigation Bar --}}
        <x-bottom-navigation />
    </div>

    {{-- HTML5 QR Code Library --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const states = {
                scanning: document.getElementById('state-scanning'),
                processing: document.getElementById('state-processing'),
                success: document.getElementById('state-success'),
                error: document.getElementById('state-error'),
                blocked: document.getElementById('state-blocked')
            };

            const cameraSelect = document.getElementById('camera-select');
            const btnScanAgainSuccess = document.getElementById('btn-scan-again-success');
            const btnScanAgainError = document.getElementById('btn-scan-again-error');
            const btnRetryPermission = document.getElementById('btn-retry-permission');

            let html5QrCode = null;
            let isProcessing = false;
            let currentCameraId = null;

            function showState(stateName) {
                Object.keys(states).forEach(key => {
                    states[key].classList.toggle('hidden', key !== stateName);
                });
            }

            function onScanFailure(error) {
                // Ignore failure noise
            }

            async function onScanSuccess(decodedText) {
                if (isProcessing) return;
                isProcessing = true;

                try {
                    if (html5QrCode && html5QrCode.isScanning) {
                        await html5QrCode.stop();
                    }
                } catch (e) {
                    console.warn(e);
                }

                processScannedToken(decodedText);
            }

            async function processScannedToken(token) {
                showState('processing');

                try {
                    const response = await fetch("{{ route('scanner.scan') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                            "Accept": "application/json"
                        },
                        body: JSON.stringify({ qr_token: token })
                    });

                    const body = await response.json();
                    isProcessing = false;

                    if (response.ok && body.status === 'success') {
                        document.getElementById('res-checkpoint-name').textContent = body.checkpoint_name;
                        document.getElementById('res-points-awarded').textContent = '+' + body.points_awarded + ' Poin';
                        document.getElementById('res-total-points').textContent = body.total_points + ' Poin';
                        document.getElementById('success-message').textContent = body.message;
                        showState('success');
                    } else {
                        document.getElementById('error-message').textContent = body.message || 'QR Code tidak valid';
                        showState('error');
                    }
                } catch (error) {
                    console.error(error);
                    isProcessing = false;
                    document.getElementById('error-message').textContent = 'Koneksi gagal';
                    showState('error');
                }
            }

            async function stopScanner() {
                try {
                    if (html5QrCode && html5QrCode.isScanning) {
                        await html5QrCode.stop();
                    }
                } catch (err) {
                    console.warn(err);
                }
            }

            async function startScanning() {
                showState('scanning');
                isProcessing = false;

                try {
                    await stopScanner();

                    if (!html5QrCode) {
                        html5QrCode = new Html5Qrcode("reader");
                    }

                    const devices = await Html5Qrcode.getCameras();
                    console.log("Available cameras:", devices);

                    if (!devices.length) {
                        showState('blocked');
                        return;
                    }

                    let preferredCamera = devices.find(device =>
                        device.label.toLowerCase().includes('back')
                    ) || devices.find(device =>
                        device.label.toLowerCase().includes('rear')
                    ) || devices[0];

                    currentCameraId = preferredCamera.id;
                    cameraSelect.innerHTML = '';

                    devices.forEach(device => {
                        const option = document.createElement('option');
                        option.value = device.id;
                        option.textContent = device.label || 'Camera';
                        if (device.id === currentCameraId) {
                            option.selected = true;
                        }
                        cameraSelect.appendChild(option);
                    });

                    if (devices.length > 1) {
                        cameraSelect.classList.remove('hidden');
                    }

                    await html5QrCode.start(
                        currentCameraId,
                        {
                            fps: 10,
                            qrbox: {
                                width: 220,
                                height: 220
                            }
                        },
                        onScanSuccess,
                        onScanFailure
                    );

                    console.log('Scanner started successfully');
                } catch (error) {
                    console.error('Scanner start failed:', error);
                    showState('blocked');
                }
            }

            cameraSelect.addEventListener('change', async (e) => {
                currentCameraId = e.target.value;

                try {
                    await stopScanner();
                    await html5QrCode.start(
                        currentCameraId,
                        {
                            fps: 10,
                            qrbox: {
                                width: 220,
                                height: 220
                            }
                        },
                        onScanSuccess,
                        onScanFailure
                    );
                } catch (error) {
                    console.error(error);
                    showState('blocked');
                }
            });

            btnScanAgainSuccess.addEventListener('click', startScanning);
            btnScanAgainError.addEventListener('click', startScanning);
            btnRetryPermission.addEventListener('click', startScanning);

            startScanning();
        });
    </script>
</body>
</html>