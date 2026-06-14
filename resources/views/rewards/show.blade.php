<x-app-layout title="{{ $reward->name }} – Jejak Hijau" :user="$user">
    <div class="space-y-6">
        {{-- Navigation & Back --}}
        <div class="animate-fade-in-up">
            <a href="{{ route('rewards.index') }}" 
               id="btn-back-to-rewards"
               class="inline-flex items-center gap-1 text-sm font-semibold hover:underline"
               style="color: #2ECF89;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 12H5M12 19l-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Kembali ke Katalog Reward
            </a>
        </div>

        {{-- Alerts --}}
        @if (session('error'))
            <div class="p-4 rounded-xl border animate-fade-in-up flex items-start gap-3"
                 style="background: rgba(239, 68, 68, 0.08); border-color: rgba(239, 68, 68, 0.25); color: #b91c1c;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mt-0.5">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                    <line x1="12" y1="8" x2="12" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="12" y1="16" x2="12.01" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <div>
                    <p class="font-bold text-sm">Gagal</p>
                    <p class="text-xs mt-0.5">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- Details Card --}}
        <div class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 grid grid-cols-1 lg:grid-cols-3 animate-fade-in-up animate-delay-100">
            
            {{-- Main detail content (spans 2 columns) --}}
            <div class="lg:col-span-2 p-6 lg:p-8 space-y-6">
                {{-- Product Image --}}
                <div class="relative h-64 md:h-80 w-full rounded-2xl overflow-hidden bg-emerald-950 shadow-inner">
                    @if($reward->image)
                        <img src="{{ $reward->image }}" 
                             alt="{{ $reward->name }}" 
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-white/30 font-bold"
                             style="background: linear-gradient(135deg, #003F2F 0%, #2ECF89 100%);">
                            GreenRun Reward
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-black/10"></div>
                </div>

                {{-- Title & Status --}}
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        @if($reward->stock <= 0)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800" style="background-color: rgba(239, 68, 68, 0.1); color: #EF4444;">
                                Stok Habis
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800" style="background-color: rgba(34, 197, 94, 0.1); color: #22C55E;">
                                Stok Tersedia
                            </span>
                        @endif
                    </div>
                    <h3 class="font-bold text-2xl lg:text-3xl leading-tight" style="color: #111827;">{{ $reward->name }}</h3>
                </div>

                {{-- Description --}}
                <div class="space-y-2.5">
                    <h4 class="font-bold text-sm uppercase tracking-wider" style="color: #6B7280;">Deskripsi Reward</h4>
                    <p class="text-sm leading-relaxed" style="color: #374151;">
                        {{ $reward->description }}
                    </p>
                </div>

                {{-- Terms & Conditions --}}
                <div class="space-y-2.5 pt-4 border-t border-gray-100">
                    <h4 class="font-bold text-sm uppercase tracking-wider" style="color: #6B7280;">Syarat & Ketentuan</h4>
                    <ul class="text-xs space-y-1.5 list-disc pl-5" style="color: #374151;">
                        <li>Penukaran poin bersifat final dan tidak dapat dibatalkan atau ditukar kembali dengan uang tunai.</li>
                        <li>Pastikan jumlah poin Anda mencukupi sebelum menekan tombol penukaran.</li>
                        <li>Pengambilan reward fisik dapat dikoordinasikan dengan panitia organizer event setempat pada saat pelaksanaan event berlangsung.</li>
                        <li>Stok terbatas dan penukaran diproses berdasarkan urutan transaksi yang masuk terlebih dahulu.</li>
                    </ul>
                </div>
            </div>

            {{-- Sidebar details & CTA --}}
            <div class="p-6 lg:p-8 bg-gray-50 flex flex-col justify-between border-t lg:border-t-0 lg:border-l border-gray-100">
                <div class="space-y-6">
                    <h4 class="font-bold text-sm uppercase tracking-wider" style="color: #6B7280;">Detail Penukaran</h4>

                    <div class="space-y-4">
                        {{-- Required points --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background: rgba(46,207,137,0.12); color: #003F2F;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14l-5-4.87 6.91-1.01L12 2z"
                                          fill="currentColor"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs" style="color: #9CA3AF;">Poin Dibutuhkan</p>
                                <p class="text-sm font-semibold" style="color: #374151;">{{ number_format($reward->required_points) }} Poin</p>
                            </div>
                        </div>

                        {{-- Available stock --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background: rgba(46,207,137,0.12); color: #003F2F;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="2" y="7" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
                                    <path d="M16 7c0-2.21-1.79-4-4-4S8 4.79 8 7" stroke="currentColor" stroke-width="2"/>
                                    <path d="M12 7v14M2 12h20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs" style="color: #9CA3AF;">Stok Tersedia</p>
                                <p class="text-sm font-semibold" style="color: #374151;">{{ $reward->stock }} Unit</p>
                            </div>
                        </div>

                        {{-- User Point balance --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background: rgba(46,207,137,0.12); color: #003F2F;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                    <path d="M12 8v8M8 12h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs" style="color: #9CA3AF;">Poin Tersedia Anda</p>
                                <p class="text-sm font-semibold" style="color: #374151;">{{ number_format($user->points) }} Poin</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action CTA --}}
                <div class="pt-8 lg:pt-0">
                    @if($reward->stock <= 0)
                        <button disabled 
                                class="w-full btn-secondary h-12 flex items-center justify-center opacity-50 cursor-not-allowed">
                            Stok Habis
                        </button>
                    @else
                        <button type="button" 
                                onclick="openRedeemModal()"
                                id="btn-trigger-redeem"
                                class="w-full btn-primary h-12 flex items-center justify-center gap-2">
                            Tukar Reward
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- Redemption Confirmation Modal Overlay --}}
    <div id="redeem-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm hidden animate-fade-in" role="dialog" aria-modal="true">
        <div class="bg-white rounded-2xl p-6 max-w-sm w-full space-y-4 shadow-xl animate-fade-in-up">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600" style="background-color: rgba(46,207,137,0.08); border-color: rgba(46,207,137,0.25); color: #003F2F;">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <rect x="2" y="7" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
                    <path d="M16 7c0-2.21-1.79-4-4-4S8 4.79 8 7" stroke="currentColor" stroke-width="2"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-800">Tukarkan Reward?</h3>
                <p class="text-sm text-gray-500 mt-1">Apakah Anda yakin ingin menukarkan reward ini?</p>
                
                {{-- Confirmation Meta Box --}}
                <div class="mt-3 bg-gray-50 rounded-xl p-3 border border-gray-100 text-xs space-y-1.5 text-gray-600">
                    <div class="flex justify-between">
                        <span class="font-medium">Reward:</span>
                        <span class="font-bold text-gray-800" id="confirm-reward-name">{{ $reward->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">Poin Dibutuhkan:</span>
                        <span class="font-bold text-emerald-700" style="color: #003F2F;" id="confirm-reward-points">{{ number_format($reward->required_points) }} Poin</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeRedeemModal()" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-250 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-all">
                    Batal
                </button>
                <form id="redeem-form" method="POST" action="{{ route('rewards.redeem', $reward->id) }}" class="flex-1">
                    @csrf
                    <button type="submit" 
                            id="btn-confirm-redeem"
                            class="w-full px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-all shadow-sm" style="background-color: #003F2F;">
                        Konfirmasi Tukar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openRedeemModal() {
            document.getElementById('redeem-modal').classList.remove('hidden');
        }

        function closeRedeemModal() {
            document.getElementById('redeem-modal').classList.add('hidden');
        }
    </script>
</x-app-layout>
