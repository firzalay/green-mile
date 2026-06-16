<x-app-layout title="Leaderboard – GreenMile" :user="auth()->user()">
    <div class="space-y-6 max-w-2xl mx-auto pb-12">
        {{-- Header Section --}}
        <section class="animate-fade-in-up flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl" style="color: #111827;">Leaderboard</h2>
                <p class="text-sm mt-1" style="color: #6B7280;">
                    Pantau peringkat dan kompetisi sehat antar peserta secara real-time.
                </p>
            </div>

            @if($hasJoinedEvents)
                {{-- Event Switcher Dropdown --}}
                <div class="flex-shrink-0 min-w-[200px]">
                    <label for="event-switcher" class="sr-only">Pilih Event</label>
                    <select id="event-switcher" 
                            onchange="window.location.href = '/events/' + this.value + '/leaderboard'"
                            class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:border-emerald focus:ring-emerald/20 text-sm font-semibold text-gray-800 bg-white cursor-pointer shadow-sm focus:outline-none transition-all">
                        @foreach($joinedEvents as $je)
                            <option value="{{ $je->id }}" {{ $je->id === $event->id ? 'selected' : '' }}>
                                {{ $je->name }} ({{ $je->status }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </section>

        @if(! $hasJoinedEvents)
            {{-- Empty State: Not joined any events at all --}}
            <section class="bg-white rounded-2xl p-8 text-center shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 space-y-5 animate-fade-in-up">
                <div class="w-16 h-16 bg-emerald/10 text-emerald rounded-full flex items-center justify-center mx-auto" style="color: #2ECF89;">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="space-y-1">
                    <h3 class="text-lg font-bold text-gray-800">Belum Bergabung Event</h3>
                    <p class="text-sm text-gray-500 max-w-sm mx-auto">
                        Anda belum bergabung pada event aktif apa pun. Silakan daftarkan diri Anda pada event yang tersedia untuk masuk dalam leaderboard.
                    </p>
                </div>
                <div>
                    <a href="{{ route('events.index') }}" 
                       class="inline-flex items-center justify-center h-10 px-5 text-sm font-bold rounded-xl text-white shadow-sm transition-all"
                       style="background: #003F2F; hover:background: #002f23;">
                        Daftar Event Sekarang
                    </a>
                </div>
            </section>
        @else
            {{-- Leaderboard Header Info --}}
            <div class="flex items-center justify-between text-xs text-gray-400 animate-fade-in-up">
                <span>Event: <strong class="text-gray-750 font-bold">{{ $event->name }}</strong></span>
                @if(strtolower($event->status) === 'ongoing')
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-emerald rounded-full animate-ping" style="background-color: #2ECF89;"></div>
                        <label class="flex items-center gap-1.5 cursor-pointer select-none">
                            <input type="checkbox" id="auto-refresh-toggle" class="rounded text-emerald focus:ring-emerald border-gray-300 w-3.5 h-3.5 cursor-pointer">
                            <span class="font-semibold text-gray-600">Auto Refresh (30s)</span>
                        </label>
                    </div>
                @endif
            </div>

            {{-- Top 3 Podium --}}
            @if(empty($search) && $top3->isNotEmpty())
                <section class="bg-white rounded-2xl p-6 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 animate-fade-in-up">
                    <h3 class="sr-only">Top 3 Podium</h3>
                    <div class="grid grid-cols-3 items-end gap-2 max-w-sm mx-auto pt-4 pb-2">
                        
                        {{-- 2nd Place (Left) --}}
                        @if(isset($top3[1]))
                            @php
                                $secondUser = $top3[1]->user;
                                $secondInitials = collect(explode(' ', $secondUser->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->implode('');
                            @endphp
                            <div class="flex flex-col items-center">
                                <div class="relative mb-2">
                                    <div class="w-14 h-14 rounded-full overflow-hidden border-2 border-slate-300 shadow-sm bg-gray-50 flex items-center justify-center">
                                        @if($secondUser->avatar)
                                            <img src="{{ $secondUser->avatar }}" alt="{{ $secondUser->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center font-bold text-slate-500 text-sm bg-slate-100">
                                                {{ $secondInitials }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 bg-slate-400 text-white w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold border border-white">
                                        2
                                    </div>
                                </div>
                                <p class="font-bold text-xs text-gray-800 text-center truncate max-w-[80px]">{{ $secondUser->name }}</p>
                                <p class="text-[10px] font-bold text-emerald mt-0.5" style="color: #2ECF89;">{{ number_format($top3[1]->total_points) }} Poin</p>
                                
                                {{-- Podium Step --}}
                                <div class="w-full bg-slate-100 border-t border-slate-200 rounded-t-xl mt-3 flex items-center justify-center text-slate-400 font-bold text-base" style="height: 65px;">
                                    🥈
                                </div>
                            </div>
                        @endif

                        {{-- 1st Place (Center) --}}
                        @if(isset($top3[0]))
                            @php
                                $firstUser = $top3[0]->user;
                                $firstInitials = collect(explode(' ', $firstUser->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->implode('');
                            @endphp
                            <div class="flex flex-col items-center">
                                <div class="relative mb-2">
                                    <div class="w-18 h-18 rounded-full overflow-hidden border-2 border-amber-400 shadow-md bg-gray-50 flex items-center justify-center">
                                        @if($firstUser->avatar)
                                            <img src="{{ $firstUser->avatar }}" alt="{{ $firstUser->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center font-bold text-amber-600 text-base bg-amber-100">
                                                {{ $firstInitials }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 bg-amber-400 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold border border-white">
                                        1
                                    </div>
                                </div>
                                <p class="font-bold text-sm text-gray-900 text-center truncate max-w-[90px]">{{ $firstUser->name }}</p>
                                <p class="text-xs font-black text-amber-500 mt-0.5">{{ number_format($top3[0]->total_points) }} Poin</p>
                                
                                {{-- Podium Step --}}
                                <div class="w-full bg-amber-50 border-t border-amber-200 rounded-t-xl mt-3 flex items-center justify-center text-amber-500 font-bold text-lg" style="height: 90px;">
                                    🥇
                                </div>
                            </div>
                        @endif

                        {{-- 3rd Place (Right) --}}
                        @if(isset($top3[2]))
                            @php
                                $thirdUser = $top3[2]->user;
                                $thirdInitials = collect(explode(' ', $thirdUser->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->implode('');
                            @endphp
                            <div class="flex flex-col items-center">
                                <div class="relative mb-2">
                                    <div class="w-14 h-14 rounded-full overflow-hidden border-2 border-amber-700/30 shadow-sm bg-gray-50 flex items-center justify-center">
                                        @if($thirdUser->avatar)
                                            <img src="{{ $thirdUser->avatar }}" alt="{{ $thirdUser->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center font-bold text-amber-800/80 text-sm bg-amber-100/60">
                                                {{ $thirdInitials }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 bg-amber-600 text-white w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold border border-white">
                                        3
                                    </div>
                                </div>
                                <p class="font-bold text-xs text-gray-800 text-center truncate max-w-[80px]">{{ $thirdUser->name }}</p>
                                <p class="text-[10px] font-bold text-emerald mt-0.5" style="color: #2ECF89;">{{ number_format($top3[2]->total_points) }} Poin</p>
                                
                                {{-- Podium Step --}}
                                <div class="w-full bg-amber-800/5 border-t border-amber-850/10 rounded-t-xl mt-3 flex items-center justify-center text-amber-750 font-bold text-sm" style="height: 50px;">
                                    🥉
                                </div>
                            </div>
                        @endif

                    </div>
                </section>
            @endif

            {{-- Summary Statistics Card --}}
            <section class="grid grid-cols-2 sm:grid-cols-4 gap-4 animate-fade-in-up">
                {{-- Total Participants --}}
                <div class="bg-white p-4 rounded-2xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 space-y-1">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Peserta</span>
                    <p class="text-xl font-black text-gray-800" id="stat-participants">{{ number_format($totalParticipants) }}</p>
                </div>
                {{-- Your Rank --}}
                <div class="bg-white p-4 rounded-2xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 space-y-1">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Rank Anda</span>
                    <p class="text-xl font-black text-emerald" id="stat-user-rank" style="color: #2ECF89;">{{ $currentUserRank }}</p>
                </div>
                {{-- Highest Score --}}
                <div class="bg-white p-4 rounded-2xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 space-y-1">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Skor Tertinggi</span>
                    <p class="text-xl font-black text-gray-800" id="stat-highest-score">{{ number_format($highestScore) }}</p>
                </div>
                {{-- Average Score --}}
                <div class="bg-white p-4 rounded-2xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 space-y-1">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Rata-Rata</span>
                    <p class="text-xl font-black text-gray-800" id="stat-average-score">{{ number_format($averageScore) }}</p>
                </div>
            </section>

            {{-- Search & Filter --}}
            <section class="animate-fade-in-up">
                <form method="GET" action="{{ route('events.leaderboard', $event->id) }}" class="flex items-center gap-3">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}" 
                               placeholder="Cari nama atau username peserta..."
                               class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-300 focus:border-emerald focus:ring-emerald/20 focus:outline-none focus:ring-4 transition-all text-sm text-gray-800 bg-white shadow-sm">
                    </div>
                    @if(!empty($search))
                        <a href="{{ route('events.leaderboard', $event->id) }}" 
                           class="h-10 px-4 flex items-center justify-center text-xs font-bold rounded-xl border border-gray-250 text-gray-650 bg-white hover:bg-gray-50 transition-all">
                            Reset
                        </a>
                    @endif
                    <button type="submit" 
                            class="h-10 px-4 inline-flex items-center justify-center font-bold text-xs rounded-xl text-white shadow-sm transition-all"
                            style="background: #003F2F; hover:background: #002f23;">
                        Cari
                    </button>
                </form>
            </section>

            {{-- Rankings Table / List --}}
            <section class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 animate-fade-in-up">
                @if($paginatedList->isEmpty())
                    <div class="text-center py-12 px-4 space-y-2">
                        <p class="text-sm font-semibold text-gray-500">Belum ada peserta yang masuk leaderboard.</p>
                        <p class="text-xs text-gray-400">Silakan daftarkan aksi scan checkpoint Anda.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                    <th class="py-3.5 px-4 w-16 text-center">Rank</th>
                                    <th class="py-3.5 px-4">Peserta</th>
                                    <th class="py-3.5 px-4 text-center">Scan CP</th>
                                    <th class="py-3.5 px-4 text-right pr-6">Total Poin</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($paginatedList as $item)
                                    @php
                                        $isCurrentUser = $item->user_id === auth()->id();
                                        $runner = $item->user;
                                        $initials = collect(explode(' ', $runner->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->implode('');
                                    @endphp
                                    <tr class="transition-colors hover:bg-gray-50 {{ $isCurrentUser ? 'bg-emerald/5 hover:bg-emerald/10' : '' }}"
                                        style="{{ $isCurrentUser ? 'background-color: rgba(46,207,137,0.08);' : '' }}">
                                        
                                        {{-- Rank --}}
                                        <td class="py-4 px-4 text-center font-black {{ $item->computed_rank <= 3 ? 'text-amber-500' : 'text-gray-500' }}">
                                            #{{ $item->computed_rank }}
                                        </td>
                                        
                                        {{-- Participant Info --}}
                                        <td class="py-4 px-4">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-200 shadow-sm flex-shrink-0 flex items-center justify-center font-bold text-xs bg-gray-100 text-gray-500">
                                                    @if($runner->avatar)
                                                        <img src="{{ $runner->avatar }}" alt="{{ $runner->name }}" class="w-full h-full object-cover">
                                                    @else
                                                        {{ $initials }}
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="font-bold text-gray-800 leading-none truncate {{ $isCurrentUser ? 'text-emerald-800' : '' }}">
                                                        {{ $runner->name }}
                                                    </p>
                                                    <p class="text-[10px] text-gray-400 mt-1">
                                                        {{ '@' . $runner->username }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Completed Checkpoints --}}
                                        <td class="py-4 px-4 text-center font-semibold text-gray-600">
                                            {{ $item->total_scans }} CP
                                        </td>

                                        {{-- Points --}}
                                        <td class="py-4 px-4 text-right pr-6 font-black text-gray-900 {{ $isCurrentUser ? 'text-emerald font-black' : '' }}"
                                            style="{{ $isCurrentUser ? 'color: #003F2F;' : '' }}">
                                            {{ number_format($item->total_points) }}
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Links --}}
                    @if($paginatedList->hasPages())
                        <div class="p-4 bg-white border-t border-gray-50">
                            {{ $paginatedList->links() }}
                        </div>
                    @endif
                @endif
        @endif
    </div>

    @if($hasJoinedEvents && strtolower($event->status) === 'ongoing')
        {{-- Auto-refresh script --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const checkbox = document.getElementById('auto-refresh-toggle');
                let intervalId = null;

                function startTimer() {
                    intervalId = setInterval(() => {
                        window.location.reload();
                    }, 30000);
                }

                if (checkbox) {
                    const isEnabled = localStorage.getItem('leaderboard_auto_refresh') !== 'false';
                    checkbox.checked = isEnabled;

                    if (isEnabled) {
                        startTimer();
                    }

                    checkbox.addEventListener('change', function() {
                        localStorage.setItem('leaderboard_auto_refresh', checkbox.checked);
                        if (checkbox.checked) {
                            startTimer();
                        } else {
                            clearInterval(intervalId);
                        }
                    });
                }
            });
        </script>
    @endif
</x-app-layout>
