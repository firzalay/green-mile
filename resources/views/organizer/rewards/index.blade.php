<x-app-layout title="Kelola Reward – GreenMile" :user="auth()->user()">
    <div class="px-4 py-6 max-w-5xl mx-auto space-y-6 animate-fade-in">
        {{-- Breadcrumb & Back --}}
        <div class="flex items-center gap-2 text-xs text-gray-400">
            <a href="{{ route('organizer.dashboard') }}" class="hover:text-emerald">Dashboard</a>
            <span>/</span>
            <a href="{{ route('organizer.events.index') }}" class="hover:text-emerald">Event</a>
            <span>/</span>
            <a href="{{ route('organizer.events.show', $event->id) }}" class="hover:text-emerald truncate max-w-[150px]">{{ $event->name }}</a>
            <span>/</span>
            <span class="text-gray-500 font-semibold">Reward</span>
        </div>

        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-150 pb-5">
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Reward Event</h1>
                <p class="text-sm text-gray-500 mt-1">Mengelola daftar reward yang dapat ditukarkan peserta untuk event <span class="font-semibold text-gray-700">{{ $event->name }}</span>.</p>
            </div>
            <div>
                <a href="{{ route('organizer.events.rewards.create', $event->id) }}" id="btn-tambah-reward" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-forest hover:bg-forest/90 transition-all shadow-sm" style="background-color: #003F2F;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                    </svg>
                    Tambah Reward
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl text-sm font-semibold text-emerald bg-emerald/10 border border-emerald/20 flex items-center gap-2">
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

        @if($errors->has('total_redeemed'))
            <div class="p-4 rounded-xl text-sm font-semibold text-red-650 bg-red-50 border border-red-200 flex items-center gap-2" style="color: #DC2626; background-color: #FEF2F2; border-color: #FCA5A5;">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span>{{ $errors->first('total_redeemed') }}</span>
            </div>
        @endif

        {{-- Rewards List --}}
        @if($rewards->isEmpty())
            <div class="bg-white rounded-2xl p-12 text-center shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 max-w-md mx-auto animate-fade-in-up">
                <div class="w-16 h-16 rounded-2xl bg-gray-50 border border-dashed border-gray-250 flex items-center justify-center mx-auto mb-4 text-gray-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h17.25c.621 0 1.125-.504 1.125-1.125V8.25c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v2.25c0 .621.504 1.125 1.125 1.125z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Belum Ada Reward</h3>
                <p class="text-sm text-gray-400 mt-1 mb-6">Belum ada reward pada event ini. Buat reward pertama agar peserta bisa menukarkan poin mereka.</p>
                <a href="{{ route('organizer.events.rewards.create', $event->id) }}" id="btn-create-reward-empty" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-forest hover:bg-forest/90 transition-all shadow-sm" style="background-color: #003F2F;">
                    Buat Reward Pertama
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="rewards-list">
                @foreach($rewards as $reward)
                    <div class="bg-white rounded-2xl p-5 flex flex-col justify-between gap-4 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                        <div class="flex items-start gap-4">
                            {{-- Reward Image --}}
                            <div class="w-20 h-20 rounded-xl overflow-hidden bg-gray-50 border border-gray-100 flex-shrink-0">
                                @if($reward->image)
                                    <img src="{{ $reward->image }}" alt="{{ $reward->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-forest/5 text-forest font-bold text-lg" style="background-color: rgba(0,63,47,0.05); color: #003F2F;">
                                        🎁
                                    </div>
                                @endif
                            </div>

                            <div class="space-y-1 min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <h2 class="font-bold text-base text-gray-800 truncate">{{ $reward->name }}</h2>
                                    @if($reward->is_active)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald/10 text-emerald" style="color: #2ECF89;">Active</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-500/10 text-gray-500">Inactive</span>
                                    @endif
                                </div>
                                <p class="text-xs text-emerald font-extrabold" style="color: #2ECF89;">
                                    {{ number_format($reward->required_points) }} Poin
                                </p>
                                <p class="text-xs text-gray-400 line-clamp-2 mt-1">
                                    {{ $reward->description }}
                                </p>
                            </div>
                        </div>

                        {{-- Metadata & Actions --}}
                        <div class="flex items-center justify-between border-t pt-3 border-gray-100 flex-shrink-0">
                            {{-- Info Row --}}
                            <div class="flex gap-4 text-xs text-gray-400">
                                <div>
                                    <span class="block text-[10px] uppercase font-bold tracking-wider text-gray-400 leading-none mb-1">Stok</span>
                                    <span class="font-bold {{ $reward->stock > 0 ? 'text-gray-800' : 'text-red-600' }}">{{ $reward->stock }} Pcs</span>
                                </div>
                                <div>
                                    <span class="block text-[10px] uppercase font-bold tracking-wider text-gray-400 leading-none mb-1">Ditukarkan</span>
                                    <span class="font-semibold text-gray-700">{{ $reward->total_redeemed }} Kali</span>
                                </div>
                            </div>

                            {{-- Action buttons --}}
                            <div class="flex items-center gap-3">
                                <a href="{{ route('organizer.rewards.show', $reward->id) }}" class="text-xs font-bold text-gray-650 hover:text-emerald transition-colors">
                                    Detail
                                </a>
                                <a href="{{ route('organizer.rewards.edit', $reward->id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors">
                                    Edit
                                </a>
                                <button type="button" 
                                        onclick="confirmDelete('{{ route('organizer.rewards.destroy', $reward->id) }}', '{{ $reward->name }}', {{ $reward->total_redeemed }})"
                                        class="text-xs font-bold text-red-650 hover:text-red-800 transition-colors">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
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
