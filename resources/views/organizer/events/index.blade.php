<x-app-layout title="Kelola Event – Jejak Hijau" :user="$user">
    <div class="px-4 py-6 max-w-6xl mx-auto space-y-6">
        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Kelola Event</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola dan pantau seluruh event GreenRun yang Anda selenggarakan.</p>
            </div>
            <div>
                <a href="{{ route('organizer.events.create') }}" id="btn-buat-event-index" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-forest hover:bg-forest/90 transition-all shadow-sm" style="background-color: #003F2F;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                    </svg>
                    Buat Event Baru
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl text-sm font-semibold text-emerald bg-emerald/10 border border-emerald/20 flex items-center gap-2 animate-fade-in">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Events List Grid --}}
        @if($events->isEmpty())
            <div class="bg-white rounded-2xl p-12 text-center shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 max-w-md mx-auto">
                <div class="w-16 h-16 rounded-2xl bg-gray-50 border border-dashed border-gray-250 flex items-center justify-center mx-auto mb-4 text-gray-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Belum Ada Event</h3>
                <p class="text-sm text-gray-400 mt-1 mb-6">Anda belum membuat event apapun. Mulai dengan membuat event pertama Anda.</p>
                <a href="{{ route('organizer.events.create') }}" class="btn-primary inline-flex items-center gap-2">
                    Buat Event Pertama
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($events as $event)
                    @php
                        $status = $event->status;
                    @endphp
                    <div class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between group">
                        {{-- Banner / Header --}}
                        <div class="relative h-40 bg-gray-100 flex-shrink-0">
                            @if($event->banner)
                                <img src="{{ $event->banner }}" alt="{{ $event->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300" style="background: linear-gradient(135deg, #003F2F 0%, #2ECF89 100%); opacity: 0.85;">
                                    <svg class="w-12 h-12 text-white/50" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 004.5 4.5H18a3.75 3.75 0 001.332-7.257 3 3 0 00-3.758-3.848 5.25 5.25 0 00-10.233 2.33A4.502 4.502 0 002.25 15z"></path>
                                    </svg>
                                </div>
                            @endif

                            {{-- Status Badge --}}
                            <div class="absolute top-3 right-3">
                                @if($status === 'Ongoing')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald text-white shadow-sm" style="background-color: #2ECF89;">Ongoing</span>
                                @elseif($status === 'Upcoming')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500 text-white shadow-sm">Upcoming</span>
                                @elseif($status === 'Draft')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-600 text-white shadow-sm">Draft</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-500 text-white shadow-sm">Finished</span>
                                @endif
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                            <div class="space-y-2">
                                <h2 class="font-bold text-lg text-gray-800 group-hover:text-emerald transition-colors line-clamp-1">{{ $event->name }}</h2>
                                <p class="text-xs text-gray-400 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"></path>
                                    </svg>
                                    {{ $event->location }}
                                </p>
                                <p class="text-sm text-gray-600 line-clamp-2">{{ $event->description ?? 'Tidak ada deskripsi.' }}</p>
                            </div>

                            {{-- Info Row --}}
                            <div class="grid grid-cols-2 gap-2 border-t border-gray-100 pt-3 text-xs text-gray-400">
                                <div>
                                    <span class="block text-[10px] uppercase font-bold tracking-wider text-gray-400 leading-none mb-1">Mulai</span>
                                    <span class="font-semibold text-gray-700">{{ $event->start_date ? $event->start_date->translatedFormat('d M Y') : '-' }}</span>
                                </div>
                                <div>
                                    <span class="block text-[10px] uppercase font-bold tracking-wider text-gray-400 leading-none mb-1">Berakhir</span>
                                    <span class="font-semibold text-gray-700">{{ $event->end_date ? $event->end_date->translatedFormat('d M Y') : '-' }}</span>
                                </div>
                                <div class="mt-2">
                                    <span class="block text-[10px] uppercase font-bold tracking-wider text-gray-400 leading-none mb-1">Checkpoint</span>
                                    <span class="font-semibold text-gray-700">{{ $event->checkpoints_count }} Checkpoints</span>
                                </div>
                                <div class="mt-2">
                                    <span class="block text-[10px] uppercase font-bold tracking-wider text-gray-400 leading-none mb-1">Peserta</span>
                                    <span class="font-semibold text-gray-700">{{ $event->participants_count }} / {{ $event->max_participants ?? '∞' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Footer Actions --}}
                        <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between gap-2 flex-shrink-0">
                            <a href="{{ route('organizer.events.show', $event->id) }}" class="text-xs font-bold text-gray-600 hover:text-emerald transition-colors">
                                Kelola Detail
                            </a>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('organizer.events.edit', $event->id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors">
                                    Edit
                                </a>
                                <button type="button" 
                                        onclick="confirmDelete('{{ route('organizer.events.destroy', $event->id) }}', '{{ $event->name }}')"
                                        class="text-xs font-bold text-red-600 hover:text-red-800 transition-colors">
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
                <h3 class="text-lg font-bold text-gray-800">Hapus Event?</h3>
                <p class="text-sm text-gray-500 mt-1">Apakah Anda yakin ingin menghapus event <span id="delete-event-name" class="font-bold text-gray-800"></span>? Tindakan ini tidak dapat dibatalkan.</p>
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
            document.getElementById('delete-event-name').textContent = name;
            document.getElementById('delete-form').action = url;
            document.getElementById('delete-modal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('delete-modal').classList.add('hidden');
        }
    </script>
</x-app-layout>
