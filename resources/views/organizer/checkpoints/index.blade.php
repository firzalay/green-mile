<x-app-layout title="Kelola Checkpoint – Jejak Hijau" :user="auth()->user()">
    <div class="px-4 py-6 max-w-5xl mx-auto space-y-6 animate-fade-in">
        {{-- Breadcrumb & Back --}}
        <div class="flex items-center gap-2 text-xs text-gray-400">
            <a href="{{ route('organizer.dashboard') }}" class="hover:text-emerald">Dashboard</a>
            <span>/</span>
            <a href="{{ route('organizer.events.index') }}" class="hover:text-emerald">Event</a>
            <span>/</span>
            <a href="{{ route('organizer.events.show', $event->id) }}" class="hover:text-emerald truncate max-w-[150px]">{{ $event->name }}</a>
            <span>/</span>
            <span class="text-gray-500 font-semibold">Checkpoint</span>
        </div>

        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5">
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Checkpoint Event</h1>
                <p class="text-sm text-gray-500 mt-1">Mengelola rute dan checkpoint untuk event <span class="font-semibold text-gray-700">{{ $event->name }}</span>.</p>
            </div>
            <div>
                <a href="{{ route('organizer.events.checkpoints.create', $event->id) }}" id="btn-tambah-checkpoint" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-forest hover:bg-forest/90 transition-all shadow-sm" style="background-color: #003F2F;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                    </svg>
                    Tambah Checkpoint
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

        <!-- Point Distribution Configuration Card -->
        <div class="bg-white rounded-2xl p-5 shadow-sm space-y-4 hover:shadow-md transition-shadow">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald/10 text-emerald flex items-center justify-center flex-shrink-0" style="background-color: rgba(46,207,137,0.1); color: #2ECF89;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 21l8.904-4.473L21 9l-3.486-3.486L9.813 15.904z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-gray-800">Skema Distribusi Poin</h3>
                        <p class="text-xs text-gray-500">Poin didistribusikan ke checkpoint secara otomatis/kustom.</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-4 text-xs">
                    <div class="p-3 bg-gray-50 rounded-xl text-right">
                        <span class="block text-[10px] uppercase font-bold tracking-wider text-gray-400">Total Point Pool</span>
                        <span class="text-sm font-extrabold text-gray-800">{{ number_format($event->total_point_pool) }} Pts</span>
                    </div>

                    @php
                        $allocatedPoints = $checkpoints->sum('point');
                        $remainingPoints = $event->total_point_pool - $allocatedPoints;
                    @endphp

                    <div class="p-3 bg-gray-50 rounded-xl text-right">
                        <span class="block text-[10px] uppercase font-bold tracking-wider text-gray-400">Poin Teralokasi</span>
                        <span class="text-sm font-extrabold text-gray-800">{{ number_format($allocatedPoints) }} Pts</span>
                    </div>
                    
                    <div class="p-3 rounded-xl text-right {{ $remainingPoints < 0 ? 'bg-red-50 text-red-700 border border-red-100' : ($remainingPoints === 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-blue-50 text-blue-700 border border-blue-100') }}" style="{{ $remainingPoints === 0 ? 'background-color: rgba(46,207,137,0.05); color: #003F2F;' : '' }}">
                        <span class="block text-[10px] uppercase font-bold tracking-wider {{ $remainingPoints === 0 ? 'text-emerald-600' : 'text-gray-400' }}">Sisa Poin</span>
                        <span class="text-sm font-extrabold">{{ number_format($remainingPoints) }} Pts</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Loading Skeleton Placeholder (DoD loading state requirement) --}}
        <div id="loading-skeleton" class="hidden space-y-4">
            @for($i = 0; $i < 3; $i++)
                <div class="bg-white rounded-2xl p-5 flex items-center justify-between gap-4 animate-pulse">
                    <div class="flex items-center gap-4 min-w-0 flex-1">
                        <div class="w-10 h-10 rounded-xl bg-gray-200"></div>
                        <div class="space-y-2 flex-1">
                            <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                        </div>
                    </div>
                    <div class="w-24 h-8 bg-gray-200 rounded-xl"></div>
                </div>
            @endfor
        </div>

        {{-- Checkpoints Grid / List --}}
        @if($checkpoints->isEmpty())
            <div class="bg-white rounded-2xl p-12 text-center shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 max-w-md mx-auto animate-fade-in-up">
                <div class="w-16 h-16 rounded-2xl bg-gray-50 border border-dashed border-gray-250 flex items-center justify-center mx-auto mb-4 text-gray-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Belum Ada Checkpoint</h3>
                <p class="text-sm text-gray-400 mt-1 mb-6">Belum ada checkpoint pada event ini. Tambahkan checkpoint pertama untuk memulai jalur GreenRun.</p>
                <a href="{{ route('organizer.events.checkpoints.create', $event->id) }}" class="btn-primary inline-flex items-center gap-2">
                    Tambah Checkpoint Pertama
                </a>
            </div>
        @else
            <div class="space-y-4" id="checkpoints-list">
                @foreach($checkpoints as $index => $checkpoint)
                    @php
                        $isActive = strtolower($checkpoint->status) === 'active';
                    @endphp
                    <div class="bg-white rounded-2xl p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                        <div class="flex items-start gap-4 min-w-0">
                            {{-- Sequence Number Badge --}}
                            <div class="w-10 h-10 rounded-xl bg-forest/5 text-forest flex items-center justify-center font-black text-sm flex-shrink-0" style="background-color: rgba(0,63,47,0.05); color: #003F2F;">
                                {{ $checkpoint->sequence }}
                            </div>

                            <div class="space-y-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h2 class="font-bold text-base text-gray-800 truncate">{{ $checkpoint->name }}</h2>
                                    @if($isActive)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald/10 text-emerald" style="color: #2ECF89;">Active</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-500/10 text-gray-500">Inactive</span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-400 flex items-center gap-1">
                                    <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"></path>
                                    </svg>
                                    {{ $checkpoint->location ?? 'Tidak ada detail lokasi' }}
                                </p>
                                @if($checkpoint->description)
                                    <p class="text-sm text-gray-600 line-clamp-1 mt-1">{{ $checkpoint->description }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Metadata & Actions --}}
                        <div class="flex items-center justify-between md:justify-end gap-6 border-t md:border-t-0 pt-3 md:pt-0 border-gray-100 flex-shrink-0">
                            {{-- Info Row --}}
                            <div class="flex gap-4 text-xs text-gray-400 text-right">
                                <div>
                                    <span class="block text-[10px] uppercase font-bold tracking-wider text-gray-400 leading-none mb-1">Poin</span>
                                    <span class="font-bold text-gray-800">{{ number_format($checkpoint->point) }} Pts</span>
                                </div>
                                <div>
                                    <span class="block text-[10px] uppercase font-bold tracking-wider text-gray-400 leading-none mb-1">Tipe Distribusi</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $checkpoint->is_custom_point ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $checkpoint->is_custom_point ? 'Kustom' : 'Otomatis' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Action buttons --}}
                            <div class="flex items-center gap-3">
                                <a href="{{ route('organizer.checkpoints.show', $checkpoint->id) }}" class="text-xs font-bold text-gray-650 hover:text-emerald transition-colors">
                                    Detail
                                </a>
                                <a href="{{ route('organizer.checkpoints.edit', $checkpoint->id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors">
                                    Edit
                                </a>
                                <button type="button" 
                                        onclick="confirmDelete('{{ route('organizer.checkpoints.destroy', $checkpoint->id) }}', '{{ $checkpoint->name }}')"
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
                <h3 class="text-lg font-bold text-gray-800">Hapus Checkpoint?</h3>
                <p class="text-sm text-gray-500 mt-1">Apakah Anda yakin ingin menghapus checkpoint <span id="delete-checkpoint-name" class="font-bold text-gray-800"></span>? Data yang telah dihapus tidak dapat dikembalikan.</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-250 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-all">
                    Batal
                </button>
                <form id="delete-form" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-red-650 hover:bg-red-700 transition-all shadow-sm" style="background-color: #DC2626;">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(url, name) {
            document.getElementById('delete-checkpoint-name').textContent = name;
            document.getElementById('delete-form').action = url;
            document.getElementById('delete-modal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('delete-modal').classList.add('hidden');
        }
    </script>
</x-app-layout>
