<x-app-layout title="Riwayat Penukaran – Jejak Hijau" :user="$user">
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

        {{-- Alerts for success/error flash --}}
        @if (session('success'))
            <div class="p-4 rounded-xl border animate-fade-in-up flex items-start gap-3"
                 style="background: rgba(34, 197, 94, 0.08); border-color: rgba(34, 197, 94, 0.25); color: #15803d;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mt-0.5">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <polyline points="22 4 12 14.01 9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div>
                    <p class="font-bold text-sm">Berhasil</p>
                    <p class="text-xs mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- Section Title --}}
        <section class="animate-fade-in-up">
            <h2 class="font-bold text-2xl" style="color: #111827;">Riwayat Penukaran</h2>
            <p class="text-sm mt-1" style="color: #6B7280;">
                Daftar lengkap seluruh transaksi penukaran poin Anda untuk reward Jejak Hijau.
            </p>
        </section>

        {{-- History Log List --}}
        <section class="bg-white rounded-2xl border border-gray-150 overflow-hidden shadow-sm animate-fade-in-up animate-delay-100">
            @if($redemptions->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4"
                         style="background: rgba(107, 114, 128, 0.06); border: 2px dashed rgba(107, 114, 128, 0.2);">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" stroke="#9CA3AF" stroke-width="2"/>
                            <path d="M12 6v6l4 2" stroke="#9CA3AF" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-base mb-1" style="color: #111827;">Belum Ada Transaksi</h3>
                    <p class="text-xs max-w-xs" style="color: #6B7280;">
                        Belum ada riwayat penukaran reward. Kumpulkan poin dan tukarkan dengan reward pertama Anda!
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100" style="background: #F9FAFB;">
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Reward</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Penukaran</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Poin Digunakan</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($redemptions as $redemption)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($redemption->reward->image)
                                                <img src="{{ $redemption->reward->image }}" 
                                                     alt="{{ $redemption->reward->name }}" 
                                                     class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                                            @else
                                                <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold text-xs flex-shrink-0"
                                                     style="background: linear-gradient(135deg, #003F2F, #2ECF89);">
                                                    GR
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-bold text-sm text-gray-800">{{ $redemption->reward->name }}</p>
                                                <p class="text-xs text-gray-400">ID Penukaran: #{{ $redemption->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $redemption->redeemed_at ? $redemption->redeemed_at->translatedFormat('d F Y, H:i') : $redemption->created_at->translatedFormat('d F Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-black text-right text-gray-800">
                                        -{{ number_format($redemption->points_used) }} pts
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $status = strtolower($redemption->status);
                                        @endphp
                                        @if($status === 'completed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold" 
                                                  style="background: rgba(34, 197, 94, 0.12); color: #16A34A; border: 1px solid rgba(34, 197, 94, 0.3);">
                                                Completed
                                            </span>
                                        @elseif($status === 'processed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold" 
                                                  style="background: rgba(59, 130, 246, 0.12); color: #2563EB; border: 1px solid rgba(59, 130, 246, 0.3);">
                                                Processed
                                            </span>
                                        @elseif($status === 'cancelled')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold" 
                                                  style="background: rgba(239, 68, 68, 0.12); color: #DC2626; border: 1px solid rgba(239, 68, 68, 0.3);">
                                                Cancelled
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold" 
                                                  style="background: rgba(245, 158, 11, 0.12); color: #D97706; border: 1px solid rgba(245, 158, 11, 0.3);">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
