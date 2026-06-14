@props(['participants', 'userRank' => null, 'currentUserId' => null])

<div class="rounded-2xl overflow-hidden" style="background: #FFFFFF; box-shadow: 0px 2px 8px rgba(0,0,0,0.08);">

    {{-- Header --}}
    <div class="flex items-center justify-between px-4 py-3 border-b" style="border-color: #F3F4F6;">
        <div class="flex items-center gap-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14l-5-4.87 6.91-1.01L12 2z"
                      fill="#F59E0B"/>
            </svg>
            <span class="font-bold text-sm" style="color: #111827;">Leaderboard</span>
        </div>
        <a href="#" id="link-lihat-semua-leaderboard"
           class="text-xs font-semibold hover:underline transition-colors"
           style="color: #2ECF89;">
            Lihat Semua →
        </a>
    </div>

    {{-- Top 3 rows --}}
    <div class="" style="border-color: #F9FAFB;">
        @foreach ($participants as $index => $participant)
            @php
                $position = $index + 1;
                $isCurrentUser = $currentUserId && $participant->user_id === $currentUserId;
                $medalColors = ['#F59E0B', '#9CA3AF', '#CD7F32'];
                $medalColor = $medalColors[$index] ?? '#6B7280';
            @endphp

            <div class="flex items-center gap-3 px-4 py-3 transition-colors {{ $isCurrentUser ? 'rounded-xl' : '' }}"
                 style="{{ $isCurrentUser ? 'background: rgba(46,207,137,0.06);' : '' }}">

                {{-- Rank --}}
                <div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0"
                     style="background: {{ $isCurrentUser ? 'rgba(46,207,137,0.15)' : '#F3F4F6' }}; color: {{ $medalColor }};">
                    @if ($position <= 3)
                        {{ $position === 1 ? '🥇' : ($position === 2 ? '🥈' : '🥉') }}
                    @else
                        {{ $position }}
                    @endif
                </div>

                {{-- Avatar initials --}}
                @php
                    $name = $participant->user->name ?? 'User';
                    $initials = collect(explode(' ', $name))
                        ->map(fn ($w) => strtoupper(substr($w, 0, 1)))
                        ->take(2)->implode('');
                @endphp
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                     style="background: linear-gradient(135deg, #003F2F, #2ECF89);">
                    {{ $initials }}
                </div>

                {{-- Name --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate {{ $isCurrentUser ? '' : '' }}"
                       style="color: {{ $isCurrentUser ? '#003F2F' : '#374151' }};">
                        {{ $name }}
                        @if ($isCurrentUser)
                            <span class="text-xs font-normal ml-1" style="color: #2ECF89;">(Kamu)</span>
                        @endif
                    </p>
                </div>

                {{-- Points --}}
                <div class="text-right flex-shrink-0">
                    <p class="font-bold text-sm" style="color: #111827;">{{ number_format($participant->current_event_points) }}</p>
                    <p class="text-xs" style="color: #9CA3AF;">pts</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- User rank footer (if not in top 3) --}}
    @if ($userRank !== null && $userRank > 3)
        <div class="px-4 py-2.5 flex items-center justify-between"
             style="background: rgba(46,207,137,0.06); border-top: 1px solid #F3F4F6;">
            <span class="text-xs" style="color: #6B7280;">Posisimu saat ini</span>
            <span class="text-sm font-bold" style="color: #003F2F;">#{{ $userRank }}</span>
        </div>
    @endif
</div>

