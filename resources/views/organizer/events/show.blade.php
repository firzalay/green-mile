<x-app-layout title="Detail Event – Jejak Hijau" :user="$user">
    <div class="px-4 py-6 max-w-5xl mx-auto space-y-6">
        {{-- Header Navigation & Actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-150 pb-5">
            <div class="flex items-center gap-3">
                <a href="{{ route('organizer.events.index') }}" class="p-2 rounded-xl bg-gray-50 border border-gray-200 hover:bg-gray-100 transition-colors text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl font-black text-gray-900 tracking-tight">{{ $event->name }}</h1>
                        @php
                            $status = $event->status;
                        @endphp
                        @if($status === 'Ongoing')
                            <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald/10 text-emerald" style="color: #2ECF89;">Ongoing</span>
                        @elseif($status === 'Upcoming')
                            <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-500/10 text-amber-600">Upcoming</span>
                        @elseif($status === 'Draft')
                            <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-blue-500/10 text-blue-600">Draft</span>
                        @else
                            <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-gray-500/10 text-gray-600">Finished</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $event->location }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('organizer.events.edit', $event->id) }}" id="btn-edit-event" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-gray-750 bg-white border border-gray-200 hover:bg-gray-50 transition-all shadow-sm">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"></path>
                    </svg>
                    Edit Event
                </a>
                <button type="button" 
                        onclick="confirmDelete('{{ route('organizer.events.destroy', $event->id) }}')"
                        id="btn-delete-event"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-red-600 hover:bg-red-700 transition-all shadow-sm" style="background-color: #DC2626;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"></path>
                    </svg>
                    Hapus
                </button>
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

        {{-- Banner & Meta Description --}}
        <div class="bg-white rounded-2xl border border-gray-150 overflow-hidden shadow-sm">
            @if($event->banner)
                <div class="h-64 md:h-80 w-full relative">
                    <img src="{{ $event->banner }}" alt="{{ $event->name }}" class="w-full h-full object-cover">
                </div>
            @endif
            <div class="p-6 space-y-4">
                <h3 class="font-bold text-base text-gray-800">Deskripsi Event</h3>
                <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $event->description ?? 'Tidak ada deskripsi.' }}</p>
            </div>
        </div>

        {{-- Join Code Section --}}
        <div class="bg-white p-6 rounded-2xl border border-gray-150 shadow-sm space-y-4 animate-fade-in-up">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Kode Akses Event</span>
                    <div class="flex items-center gap-3">
                        <span class="text-2xl font-black tracking-wider text-gray-800" id="event-join-code">{{ $event->join_code }}</span>
                        <button type="button" 
                                onclick="copyJoinCode()" 
                                id="btn-copy-code"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-emerald bg-emerald/10 border border-emerald/20 hover:bg-emerald/25 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 7.5V6.108c0-1.135.845-2.098 1.976-2.192.373-.03.748-.057 1.123-.08M15.75 18H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08M15.75 18.75v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5A3.375 3.375 0 006.375 7.5H5.25m11.9-3.664A2.251 2.251 0 0015 2.25h-1.5a2.251 2.251 0 00-2.15 1.586m5.8 0c.065.21.1.433.1.664v.75h-6V4.5c0-.231.035-.454.1-.664M6.75 7.5H4.875c-.621 0-1.125.504-1.125 1.125v12c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V16.5a9 9 0 00-9-9z"></path>
                            </svg>
                            Salin Kode
                        </button>
                    </div>
                </div>
                <div>
                    <form method="POST" action="{{ route('organizer.events.regenerate-code', $event->id) }}">
                        @csrf
                        <button type="submit" 
                                id="btn-regenerate-code"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-gray-750 bg-white border border-gray-200 hover:bg-gray-50 transition-all shadow-sm">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path>
                            </svg>
                            Perbarui Kode Akses
                        </button>
                    </form>
                </div>
            </div>
            <p class="text-xs text-gray-400">Peserta memerlukan kode ini untuk bergabung dengan event. Memperbarui kode akan membatalkan kode lama secara instan, namun peserta yang sudah terdaftar akan tetap terdaftar.</p>
        </div>

        {{-- Metrics Dashboard Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Total Participants --}}
            <div class="bg-white p-5 rounded-2xl border border-gray-150 shadow-sm space-y-1">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Peserta</span>
                <p class="text-3xl font-black text-gray-800" id="metric-participants">{{ $event->participants_count }} <span class="text-xs font-normal text-gray-400">/ {{ $event->max_participants ?? '∞' }}</span></p>
            </div>

            {{-- Total Checkpoints --}}
            <div class="bg-white p-5 rounded-2xl border border-gray-150 shadow-sm space-y-1">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Checkpoint</span>
                <p class="text-3xl font-black text-gray-800" id="metric-checkpoints">{{ $event->checkpoints_count }}</p>
            </div>

            {{-- Checkpoint Completed Scans --}}
            <div class="bg-white p-5 rounded-2xl border border-gray-150 shadow-sm space-y-1">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Scan</span>
                <p class="text-3xl font-black text-gray-800" id="metric-scans">{{ $totalCheckpointsCompleted }}</p>
            </div>

            {{-- Total Points Distributed --}}
            <div class="bg-white p-5 rounded-2xl border border-gray-150 shadow-sm space-y-1">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Poin Terdistribusi</span>
                <p class="text-3xl font-black text-gray-800" id="metric-points">{{ number_format($totalPoints) }}</p>
            </div>
        </div>

        {{-- Point Pool & Distribution Stats --}}
        <div class="bg-white p-6 rounded-2xl border border-gray-150 shadow-sm space-y-4 animate-fade-in-up">
            <div class="flex items-center justify-between border-b pb-3 border-gray-100 mb-2">
                <h3 class="font-bold text-base text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"></path>
                    </svg>
                    Statistik Distribusi Poin
                </h3>
                <span class="text-xs px-2.5 py-1 rounded-full font-bold {{ $event->remaining_point_pool > 0 ? 'bg-emerald/10 text-emerald' : 'bg-red-100 text-red-700' }}">
                    {{ $event->remaining_point_pool > 0 ? 'Available' : 'Exhausted' }}
                </span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                {{-- Total Point Pool --}}
                <div class="space-y-1">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Total Point Pool</span>
                    <p class="text-2xl font-black text-gray-800" id="metric-point-pool">{{ number_format($event->point_pool) }}</p>
                </div>

                {{-- Remaining Point Pool --}}
                <div class="space-y-1">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Remaining Pool</span>
                    <p class="text-2xl font-black text-emerald" id="metric-remaining-pool">{{ number_format($event->remaining_point_pool) }}</p>
                </div>

                {{-- Distributed Point --}}
                <div class="space-y-1">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Distributed Point</span>
                    <p class="text-2xl font-black text-forest" id="metric-distributed-pool" style="color: #003F2F;">{{ number_format($event->point_pool - $event->remaining_point_pool) }}</p>
                </div>

                {{-- Total Scan --}}
                <div class="space-y-1">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Total Scan</span>
                    <p class="text-2xl font-black text-gray-800" id="metric-total-scans-pool">{{ number_format($totalCheckpointsCompleted) }}</p>
                </div>

                {{-- Average Point per Scan --}}
                <div class="space-y-1">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Rata-rata Poin / Scan</span>
                    @php
                        $distributed = $event->point_pool - $event->remaining_point_pool;
                        $average = $totalCheckpointsCompleted > 0 ? round($distributed / $totalCheckpointsCompleted, 1) : 0;
                    @endphp
                    <p class="text-2xl font-black text-orange-500" id="metric-average-points">{{ number_format($average, 1) }}</p>
                </div>
            </div>
        </div>

        {{-- Grid Detail Layout (Checkpoints & Leaderboard) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Checkpoint List Section (Left column, takes 2/3) --}}
            <div class="lg:col-span-2 space-y-6">
                <section class="bg-white p-6 rounded-2xl border border-gray-150 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b pb-3 border-gray-100">
                        <h3 class="font-bold text-base text-gray-900">Checkpoints</h3>
                        <a href="{{ route('organizer.events.checkpoints.index', $event->id) }}" class="text-xs font-semibold text-emerald hover:underline">
                            Kelola Checkpoint →
                        </a>
                    </div>
                    @if($event->checkpoints->isEmpty())
                        <p class="text-sm text-gray-400 text-center py-6">Belum ada checkpoint yang dibuat untuk event ini.</p>
                    @else
                        <div class="divide-y divide-gray-100">
                            @foreach($event->checkpoints as $index => $checkpoint)
                                <div class="py-3.5 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-7 h-7 rounded-lg bg-forest/5 text-forest flex items-center justify-center font-bold text-xs" style="background-color: rgba(0,63,47,0.05); color: #003F2F;">
                                            {{ $checkpoint->order ?? ($index + 1) }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-sm text-gray-800">{{ $checkpoint->name }}</h4>
                                            <p class="text-xs text-gray-400">{{ $checkpoint->location }}</p>
                                        </div>
                                    </div>
                                    <span class="text-xs font-semibold text-emerald bg-emerald/10 px-2 py-0.5 rounded" style="color: #2ECF89; background-color: rgba(46,207,137,0.1);">
                                        +{{ $checkpoint->points ?? 50 }} Poin
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>

                {{-- Rewards Section --}}
                <section class="bg-white p-6 rounded-2xl border border-gray-150 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b pb-3 border-gray-100">
                        <h3 class="font-bold text-base text-gray-900">Rewards</h3>
                        <a href="{{ route('organizer.events.rewards.index', $event->id) }}" id="btn-manage-rewards" class="text-xs font-semibold text-emerald hover:underline" style="color: #2ECF89;">
                            Kelola Reward →
                        </a>
                    </div>
                    @if($event->rewards->isEmpty())
                        <div class="text-center py-6">
                            <p class="text-sm text-gray-400 mb-3">Belum ada reward yang dibuat untuk event ini.</p>
                            <a href="{{ route('organizer.events.rewards.create', $event->id) }}" id="btn-create-first-reward" class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-bold text-white bg-forest hover:bg-forest/90 transition-all shadow-sm" style="background-color: #003F2F;">
                                Buat Reward Pertama
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($event->rewards as $reward)
                                <div class="p-3 rounded-xl border border-gray-100 flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-50 flex-shrink-0">
                                        @if($reward->image)
                                            <img src="{{ $reward->image }}" alt="{{ $reward->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-forest/5 text-forest font-bold text-xs" style="background-color: rgba(0,63,47,0.05); color: #003F2F;">
                                                🎁
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-bold text-sm text-gray-800 truncate">{{ $reward->name }}</h4>
                                        <p class="text-xs text-gray-400">{{ number_format($reward->required_points) }} Poin • Stok: {{ $reward->stock }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>
            </div>

            {{-- Leaderboard Section (Right column, takes 1/3) --}}
            <div class="space-y-6">
                <section class="bg-white p-6 rounded-2xl border border-gray-150 shadow-sm space-y-4">
                    <h3 class="font-bold text-base text-gray-900 border-b pb-3 border-gray-100">Leaderboard Event</h3>
                    
                    @if($event->leaderboard->isEmpty())
                        <p class="text-sm text-gray-400 text-center py-6">Belum ada peserta yang mengikuti event ini.</p>
                    @else
                        <div class="space-y-3.5 max-h-[360px] overflow-y-auto pr-1">
                            @foreach($event->leaderboard->take(10) as $rank => $participant)
                                @php
                                    $initials = collect(explode(' ', $participant->user->name ?? 'User'))
                                        ->map(fn ($w) => strtoupper(substr($w, 0, 1)))
                                        ->take(2)->implode('');
                                @endphp
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        {{-- Rank Badge --}}
                                        <div class="w-5 text-center font-black text-sm text-gray-400">
                                            @if($rank === 0)
                                                <span class="text-yellow-500">🥇</span>
                                            @elseif($rank === 1)
                                                <span class="text-gray-400">🥈</span>
                                            @elseif($rank === 2)
                                                <span class="text-amber-600">🥉</span>
                                            @else
                                                {{ $rank + 1 }}
                                            @endif
                                        </div>
                                        
                                        {{-- Initials Avatar --}}
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs text-white flex-shrink-0"
                                             style="background: linear-gradient(135deg, #003F2F, #2ECF89);">
                                            {{ $initials }}
                                        </div>

                                        <div class="min-w-0">
                                            <h4 class="font-bold text-xs text-gray-800 truncate leading-tight">{{ $participant->user->name }}</h4>
                                            <p class="text-[10px] text-gray-400">{{ $participant->completed_checkpoints }} / {{ $event->total_checkpoints }} CP</p>
                                        </div>
                                    </div>
                                    
                                    <span class="text-xs font-bold text-gray-800">{{ number_format($participant->current_event_points) }} pts</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </div>

    {{-- Native Delete Confirmation Modal Overlay --}}
    <div id="delete-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm hidden" role="dialog" aria-modal="true">
        <div class="bg-white rounded-2xl border border-gray-150 p-6 max-w-sm w-full space-y-4 shadow-xl animate-fade-in-up">
            <div class="w-12 h-12 rounded-xl bg-red-50 border border-red-200 flex items-center justify-center text-red-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-800">Hapus Event?</h3>
                <p class="text-sm text-gray-500 mt-1">Apakah Anda yakin ingin menghapus event ini? Tindakan ini tidak dapat dibatalkan.</p>
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
        function confirmDelete(url) {
            document.getElementById('delete-form').action = url;
            document.getElementById('delete-modal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('delete-modal').classList.add('hidden');
        }

        function copyJoinCode() {
            const codeElement = document.getElementById('event-join-code');
            if (codeElement) {
                navigator.clipboard.writeText(codeElement.textContent.trim()).then(() => {
                    const btn = document.getElementById('btn-copy-code');
                    const originalHTML = btn.innerHTML;
                    btn.innerHTML = `
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path>
                        </svg>
                        Tersalin!
                    `;
                    setTimeout(() => {
                        btn.innerHTML = originalHTML;
                    }, 2000);
                });
            }
        }
    </script>
</x-app-layout>
