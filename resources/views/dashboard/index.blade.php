<x-app-layout title="Dashboard – Jejak Hijau" :user="$user">

    {{-- ========================= ACTIVE EVENT STATE ========================= --}}
    @if ($activeParticipation !== null)

        {{-- Desktop: 2-column grid; Mobile: single column --}}
        <div class="lg:grid lg:grid-cols-3 lg:gap-6 space-y-5 lg:space-y-0">

            {{-- LEFT column (spans 2 on desktop) --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Section: Active Event Card --}}
                <section class="animate-fade-in-up">
                    <x-active-event-card :participation="$activeParticipation" />
                </section>

                {{-- Section: Points Summary --}}
                <section class="animate-fade-in-up animate-delay-100">
                    <h2 class="font-bold text-sm mb-3 flex items-center gap-2" style="color: #374151;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14l-5-4.87 6.91-1.01L12 2z"
                                  fill="#F59E0B"/>
                        </svg>
                        Ringkasan Poin
                    </h2>
                    <x-points-summary
                        :current-event-points="$activeParticipation->current_event_points"
                        :total-points="$totalPoints"
                    />
                </section>

                {{-- Section: Quick Action --}}
                <section class="animate-fade-in-up animate-delay-200">
                    <x-quick-action-button href="#" />
                </section>

            </div>

            {{-- RIGHT column (spans 1 on desktop) --}}
            <div class="lg:col-span-1">

                {{-- Section: Mini Leaderboard --}}
                @if ($leaderboardPreview !== null && $leaderboardPreview->isNotEmpty())
                    <section class="animate-fade-in-up animate-delay-300">
                        <h2 class="font-bold text-sm mb-3 flex items-center gap-2" style="color: #374151;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 9H3V3h3M18 9h3V3h-3M8 21h8M12 17v4" stroke="#6B7280" stroke-width="2" stroke-linecap="round"/>
                                <path d="M6 3h12v8a6 6 0 0 1-12 0V3z" stroke="#6B7280" stroke-width="2"/>
                            </svg>
                            Top Peserta
                        </h2>
                        <x-mini-leaderboard
                            :participants="$leaderboardPreview"
                            :user-rank="$userRank"
                            :current-user-id="$user->id"
                        />
                    </section>
                @endif

            </div>

        </div>

    {{-- ========================= NO ACTIVE EVENT STATE ========================= --}}
    @else

        <section class="flex flex-col items-center justify-center py-16 text-center animate-fade-in-up">
            {{-- Illustration --}}
            <div class="w-28 h-28 rounded-full flex items-center justify-center mb-6"
                 style="background: rgba(46,207,137,0.08); border: 2px dashed rgba(46,207,137,0.3);">
                <svg width="52" height="52" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" fill="#2ECF89" opacity="0.4"/>
                    <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" stroke="#003F2F" stroke-width="1.5" stroke-linejoin="round"/>
                </svg>
            </div>

            <h3 class="font-bold text-xl mb-2" style="color: #111827;">Belum Ada Event Aktif</h3>
            <p class="text-sm leading-relaxed mb-6 max-w-sm" style="color: #6B7280;">
                Kamu belum mengikuti event yang sedang berlangsung. Temukan event terdekat dan mulai perjalanan hijaumu!
            </p>

            <a href="#" id="btn-lihat-event" class="btn-primary">
                Lihat Event Tersedia
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </section>

    @endif

    {{-- Skeleton template (hidden, for JS use) --}}
    <template id="skeleton-dashboard">
        <div class="lg:grid lg:grid-cols-3 lg:gap-6 space-y-4 lg:space-y-0" aria-busy="true">
            <div class="lg:col-span-2 space-y-4">
                <div class="rounded-2xl animate-pulse" style="background: #E5E7EB; height: 180px;"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-2xl animate-pulse" style="background: #E5E7EB; height: 110px;"></div>
                    <div class="rounded-2xl animate-pulse" style="background: #E5E7EB; height: 110px;"></div>
                </div>
                <div class="rounded-2xl animate-pulse" style="background: #E5E7EB; height: 56px;"></div>
            </div>
            <div class="lg:col-span-1">
                <div class="rounded-2xl animate-pulse" style="background: #E5E7EB; height: 220px;"></div>
            </div>
        </div>
    </template>

</x-app-layout>
