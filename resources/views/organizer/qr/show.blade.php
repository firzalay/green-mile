<x-app-layout title="Preview QR Code – GreenMile" :user="auth()->user()">
    <div class="px-4 py-6 max-w-4xl mx-auto space-y-6">
        {{-- Breadcrumb & Back --}}
        <div class="flex items-center gap-2 text-xs text-gray-400">
            <a href="{{ route('organizer.dashboard') }}" class="hover:text-emerald">Dashboard</a>
            <span>/</span>
            <a href="{{ route('organizer.events.index') }}" class="hover:text-emerald">Event</a>
            <span>/</span>
            <a href="{{ route('organizer.events.show', $checkpoint->event_id) }}" class="hover:text-emerald truncate max-w-[150px]">{{ $checkpoint->event->name }}</a>
            <span>/</span>
            <a href="{{ route('organizer.events.checkpoints.index', $checkpoint->event_id) }}" class="hover:text-emerald">Checkpoint</a>
            <span>/</span>
            <a href="{{ route('organizer.checkpoints.show', $checkpoint->id) }}" class="hover:text-emerald truncate max-w-[150px]">{{ $checkpoint->name }}</a>
            <span>/</span>
            <span class="text-gray-500 font-semibold">QR Code</span>
        </div>

        {{-- Header Navigation & Actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-150 pb-5">
            <div class="flex items-center gap-3">
                <a href="{{ route('organizer.checkpoints.show', $checkpoint->id) }}" class="p-2 rounded-xl bg-gray-50 border border-gray-200 hover:bg-gray-100 transition-colors text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-black text-gray-900 tracking-tight">QR Code Checkpoint</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Kelola aset pemindaian QR Code untuk checkpoint.</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl text-sm font-semibold text-emerald bg-emerald/10 border border-emerald/20 flex items-center gap-2" style="color: #2ECF89;">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl text-sm font-semibold text-red-650 bg-red-55/10 border border-red-200 flex items-center gap-2" style="color: #DC2626;">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Main Layout Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- QR Preview Card --}}
            <div class="md:col-span-2 bg-white rounded-2xl border border-gray-150 p-8 shadow-sm flex flex-col items-center justify-center space-y-6">
                @if($qrCode)
                    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-md flex items-center justify-center max-w-[280px]">
                        {!! $qrCode !!}
                    </div>
                    <div class="text-center space-y-1">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Isi Token QR</p>
                        <p class="font-mono text-sm bg-gray-50 border border-gray-150 px-3 py-1 rounded-lg text-gray-700 select-all">{{ $checkpoint->qr_token }}</p>
                    </div>
                @else
                    <div class="text-center py-12 space-y-4">
                        <div class="w-16 h-16 rounded-2xl bg-gray-50 border border-gray-150 flex items-center justify-center text-gray-400 mx-auto">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 15h.008v.008H15V15zm0 2.25h.008v.008H15v-.008zM17.25 15h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zM15 19.5h.008v.008H15V19.5zm0-4.5h.008v.008H15V15z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">QR Code belum dibuat</p>
                            <p class="text-xs text-gray-500 mt-1">Sistem belum menghasilkan token pemindaian untuk checkpoint ini.</p>
                        </div>
                        @if(strtolower($checkpoint->status) === 'active')
                            <form action="{{ route('organizer.checkpoints.generate-qr', $checkpoint->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-forest hover:bg-forest/90 transition-all shadow-sm" style="background-color: #003F2F;">
                                    Generate QR Code
                                </button>
                            </form>
                        @else
                            <div class="px-3 py-2 rounded-lg bg-yellow-50 border border-yellow-250 text-xs font-semibold text-yellow-800 max-w-[280px] mx-auto">
                                QR Code hanya dapat dibuat untuk checkpoint dengan status <span class="font-bold">Active</span>.
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Metadata & Info Card --}}
            <div class="bg-white rounded-2xl border border-gray-150 p-6 shadow-sm flex flex-col justify-between space-y-6">
                <div class="space-y-5">
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Informasi Checkpoint</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="block text-xs text-gray-400">Event</span>
                                <span class="font-bold text-sm text-gray-800">{{ $checkpoint->event->name }}</span>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400">Checkpoint</span>
                                <span class="font-bold text-sm text-gray-800">#{{ $checkpoint->sequence }} - {{ $checkpoint->name }}</span>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400">Status QR</span>
                                @if($checkpoint->qr_token)
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald/10 text-emerald" style="color: #2ECF89;">Generated</span>
                                @else
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded-md text-[10px] font-bold bg-gray-500/10 text-gray-500">Not Generated</span>
                                @endif
                            </div>
                            @if($checkpoint->qr_token)
                                <div>
                                    <span class="block text-xs text-gray-400">Tanggal Generate</span>
                                    <span class="font-semibold text-sm text-gray-755">{{ $checkpoint->updated_at->translatedFormat('d F Y, H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($checkpoint->qr_token)
                        <div class="border-t border-gray-100 pt-4 space-y-3">
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</h3>
                            <div class="flex flex-col gap-2.5">
                                <a href="{{ route('organizer.checkpoints.download-qr', $checkpoint->id) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-forest hover:bg-forest/90 transition-all shadow-sm" style="background-color: #003F2F;">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"></path>
                                    </svg>
                                    Unduh PNG/SVG
                                </a>
                                <a href="{{ route('organizer.checkpoints.print-qr', $checkpoint->id) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-gray-750 bg-white border border-gray-200 hover:bg-gray-50 transition-all shadow-sm">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.821L12.075 19.177m0 0L17.43 13.82M12.075 19.177V3.75M12 21h.008v.008H12V21z"></path>
                                    </svg>
                                    Cetak QR Code
                                </a>
                                @if(strtolower($checkpoint->status) === 'active')
                                    <button type="button" onclick="openRegenerateModal()" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-red-600 bg-red-50 border border-red-200 hover:bg-red-100 transition-all shadow-sm" style="color: #DC2626;">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path>
                                        </svg>
                                        Regenerate QR Code
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <div class="border-t border-gray-100 pt-4 text-xs text-gray-400">
                    <a href="{{ route('organizer.checkpoints.show', $checkpoint->id) }}" class="text-emerald hover:underline font-semibold block text-center">Kembali ke Checkpoint</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Regenerate Confirmation Modal --}}
    @if($checkpoint->qr_token && strtolower($checkpoint->status) === 'active')
        <div id="regenerate-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm hidden" role="dialog" aria-modal="true">
            <div class="bg-white rounded-2xl border border-gray-150 p-6 max-w-sm w-full space-y-4 shadow-xl animate-fade-in-up">
                <div class="w-12 h-12 rounded-xl bg-yellow-50 border border-yellow-200 flex items-center justify-center text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Regenerate QR Code?</h3>
                    <p class="text-sm text-gray-500 mt-1">QR lama tidak akan dapat digunakan kembali.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="closeRegenerateModal()" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-250 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-all">
                        Batal
                    </button>
                    <form action="{{ route('organizer.checkpoints.regenerate-qr', $checkpoint->id) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-forest hover:bg-forest/90 transition-all shadow-sm" style="background-color: #003F2F;">
                            Ya, Perbarui
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <script>
        function openRegenerateModal() {
            const modal = document.getElementById('regenerate-modal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeRegenerateModal() {
            const modal = document.getElementById('regenerate-modal');
            if (modal) modal.classList.add('hidden');
        }
    </script>
</x-app-layout>
