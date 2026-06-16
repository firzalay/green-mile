<x-app-layout title="Detail Reward – GreenMile" :user="auth()->user()">
    <div class="px-4 py-6 max-w-4xl mx-auto space-y-6 animate-fade-in">
        {{-- Breadcrumb & Back --}}
        <div class="flex items-center gap-2 text-xs text-gray-400">
            <a href="{{ route('organizer.dashboard') }}" class="hover:text-emerald">Dashboard</a>
            <span>/</span>
            <a href="{{ route('organizer.events.index') }}" class="hover:text-emerald">Event</a>
            <span>/</span>
            <a href="{{ route('organizer.events.show', $reward->event_id) }}" class="hover:text-emerald truncate max-w-[150px]">{{ $reward->event->name }}</a>
            <span>/</span>
            <a href="{{ route('organizer.events.rewards.index', $reward->event_id) }}" class="hover:text-emerald">Reward</a>
            <span>/</span>
            <span class="text-gray-500 font-semibold truncate max-w-[150px]">{{ $reward->name }}</span>
        </div>

        {{-- Header Navigation & Actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-150 pb-5">
            <div class="flex items-center gap-3">
                <a href="{{ route('organizer.events.rewards.index', $reward->event_id) }}" class="p-2 rounded-xl bg-gray-50 border border-gray-200 hover:bg-gray-100 transition-colors text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl font-black text-gray-900 tracking-tight">{{ $reward->name }}</h1>
                        @if($reward->is_active)
                            <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald/10 text-emerald" style="color: #2ECF89;">Active</span>
                        @else
                            <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-gray-500/10 text-gray-500">Inactive</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $reward->event->name }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('organizer.rewards.edit', $reward->id) }}" id="btn-edit-reward" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-gray-750 bg-white border border-gray-200 hover:bg-gray-50 transition-all shadow-sm">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"></path>
                    </svg>
                    Edit
                </a>
                <button type="button" 
                        onclick="confirmDelete('{{ route('organizer.rewards.destroy', $reward->id) }}', '{{ $reward->name }}', {{ $reward->total_redeemed }})"
                        id="btn-delete-reward"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-red-600 hover:bg-red-700 transition-all shadow-sm" style="background-color: #DC2626;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"></path>
                    </svg>
                    Hapus
                </button>
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
            <div class="p-4 rounded-xl text-sm font-semibold text-red-650 bg-red-50 border border-red-200 flex items-center gap-2" style="color: #DC2626; background-color: #FEF2F2; border-color: #FCA5A5;">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Main Details Card --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Details Block --}}
            <div class="md:col-span-2 bg-white rounded-2xl p-6 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 space-y-6">
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Informasi Reward</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="block text-xs text-gray-400">Nama Reward</span>
                            <span class="font-bold text-sm text-gray-800" id="detail-name">{{ $reward->name }}</span>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-400">Poin Dibutuhkan</span>
                            <span class="font-bold text-emerald text-sm" id="detail-points" style="color: #2ECF89;">{{ number_format($reward->required_points) }} Poin</span>
                        </div>
                        <div class="mt-2">
                            <span class="block text-xs text-gray-400">Stok Tersedia</span>
                            <span class="font-bold text-sm {{ $reward->stock > 0 ? 'text-gray-800' : 'text-red-600' }}" id="detail-stock">{{ $reward->stock }} Pcs</span>
                        </div>
                        <div class="mt-2">
                            <span class="block text-xs text-gray-400">Total Penukaran</span>
                            <span class="font-bold text-sm text-gray-800" id="detail-redeemed">{{ $reward->total_redeemed }} Kali</span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Deskripsi</h4>
                    <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line" id="detail-description">{{ $reward->description }}</p>
                </div>
            </div>

            {{-- Image & Quick Stats --}}
            <div class="bg-white rounded-2xl p-6 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex flex-col items-center justify-center gap-4 text-center">
                <div class="w-full aspect-square max-w-[200px] rounded-xl overflow-hidden bg-gray-50 border border-gray-200">
                    @if($reward->image)
                        <img src="{{ $reward->image }}" alt="{{ $reward->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-forest/5 text-forest font-bold text-5xl" style="background-color: rgba(0,63,47,0.05); color: #003F2F;">
                            🎁
                        </div>
                    @endif
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">{{ $reward->name }}</h4>
                    <p class="text-xs text-gray-400 mt-1">Dibuat pada {{ $reward->created_at->format('d M Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Native Delete Confirmation Modal Overlay --}}
    <div id="delete-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm hidden" role="dialog" aria-modal="true">
        <div class="bg-white rounded-2xl p-6 max-w-sm w-full space-y-4 shadow-xl animate-fade-in-up">
            <div class="w-12 h-12 rounded-xl bg-red-50 border border-red-200 flex items-center justify-center text-red-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-800">Hapus Reward?</h3>
                <p class="text-sm text-gray-500 mt-1" id="delete-modal-body">Apakah Anda yakin ingin menghapus reward ini? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-250 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-all">
                    Batal
                </button>
                <form id="delete-form" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" id="btn-confirm-delete" class="w-full px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-red-650 hover:bg-red-700 transition-all shadow-sm" style="background-color: #DC2626;">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(url, name, totalRedeemed) {
            const body = document.getElementById('delete-modal-body');
            const confirmBtn = document.getElementById('btn-confirm-delete');
            
            if (totalRedeemed > 0) {
                body.innerHTML = `Reward <span class="font-bold text-gray-800">${name}</span> tidak dapat dihapus karena sudah memiliki riwayat penukaran oleh peserta (<span class="font-bold text-red-600">${totalRedeemed} penukaran</span>).`;
                confirmBtn.disabled = true;
                confirmBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                body.innerHTML = `Apakah Anda yakin ingin menghapus reward <span class="font-bold text-gray-800">${name}</span>? Tindakan ini tidak dapat dibatalkan.`;
                confirmBtn.disabled = false;
                confirmBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
            
            document.getElementById('delete-form').action = url;
            document.getElementById('delete-modal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('delete-modal').classList.add('hidden');
        }
    </script>
</x-app-layout>
