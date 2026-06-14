<x-app-layout title="Katalog Reward – Jejak Hijau" :user="$user">
    <div class="space-y-6">
        {{-- Header Section --}}
        <section class="animate-fade-in-up flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl" style="color: #111827;">Katalog Reward</h2>
                <p class="text-sm mt-1" style="color: #6B7280;">
                    Tukarkan poin hasil checkpoint event Anda dengan reward ramah lingkungan yang menarik!
                </p>
            </div>
            <div>
                <a href="{{ route('rewards.history') }}" 
                   id="btn-reward-history"
                   class="btn-secondary inline-flex items-center justify-center gap-2 h-11 px-5 text-sm font-semibold rounded-xl">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                        <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    Riwayat Penukaran
                </a>
            </div>
        </section>

        {{-- Point Summary Widget --}}
        <section class="bg-white rounded-2xl p-6 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 animate-fade-in-up">
            <div class="space-y-1">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Total Poin Saya</span>
                <p class="text-3xl font-black text-gray-800" id="user-points-balance">
                    {{ number_format($user->points) }} <span class="text-sm font-bold text-gray-400">Poin</span>
                </p>
            </div>
            <div class="text-xs text-gray-500 max-w-sm">
                Kumpulkan poin lebih banyak dengan mengikuti event ramah lingkungan lainnya dan memindai kode QR checkpoint.
            </div>
        </section>

        {{-- Catalog Grid --}}
        @if($rewards->isEmpty())
            <section class="flex flex-col items-center justify-center py-16 text-center animate-fade-in-up">
                <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4"
                     style="background: rgba(107, 114, 128, 0.08); border: 2px dashed rgba(107, 114, 128, 0.3);">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="2" y="7" width="20" height="14" rx="2" stroke="#6B7280" stroke-width="2"/>
                        <path d="M16 7c0-2.21-1.79-4-4-4S8 4.79 8 7" stroke="#6B7280" stroke-width="2"/>
                        <path d="M12 7v14M2 12h20" stroke="#6B7280" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <h3 class="font-bold text-lg mb-1" style="color: #111827;">Belum Ada Reward</h3>
                <p class="text-sm max-w-xs" style="color: #6B7280;">
                    Belum ada reward yang tersedia saat ini. Silakan kembali lagi nanti!
                </p>
            </section>
        @else
            <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in-up animate-delay-100">
                @foreach($rewards as $reward)
                    @php
                        $isOutOfStock = $reward->stock <= 0;
                    @endphp
                    <div class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex flex-col group">
                        {{-- Image Banner --}}
                        <div class="relative h-48 w-full overflow-hidden bg-emerald-950">
                            @if($reward->image)
                                <img src="{{ $reward->image }}" 
                                     alt="{{ $reward->name }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-white/30 font-bold"
                                     style="background: linear-gradient(135deg, #003F2F 0%, #2ECF89 100%);">
                                    GreenRun Reward
                                </div>
                            @endif
                            
                            {{-- Stock Badge --}}
                            <div class="absolute top-3 right-3">
                                @if($isOutOfStock)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-600 text-white shadow-sm" style="background-color: #EF4444;">
                                        Habis
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-600 text-white shadow-sm" style="background-color: #22C55E;">
                                        Stok: {{ $reward->stock }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Details --}}
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                            <div class="space-y-2">
                                <h3 class="font-bold text-base leading-tight group-hover:text-emerald transition-colors" style="color: #111827;">
                                    {{ $reward->name }}
                                </h3>
                                <p class="text-xs text-gray-500 line-clamp-2">
                                    {{ $reward->description }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                                <div>
                                    <p class="text-[10px] uppercase font-bold text-gray-400">Poin Dibutuhkan</p>
                                    <p class="text-sm font-extrabold text-emerald-700" style="color: #003F2F;">{{ number_format($reward->required_points) }} pts</p>
                                </div>

                                @if($isOutOfStock)
                                    <button disabled 
                                            class="btn-secondary h-9 px-4 text-xs opacity-50 cursor-not-allowed">
                                        Stok Habis
                                    </button>
                                @else
                                    <a href="{{ route('rewards.show', $reward->id) }}" 
                                       id="btn-reward-detail-{{ $reward->id }}"
                                       class="btn-primary h-9 px-4 text-xs inline-flex items-center justify-center font-bold">
                                        Lihat Detail
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </section>
        @endif
    </div>
</x-app-layout>
