<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventParticipant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    /**
     * Redirect to the first joined active/ongoing event leaderboard or show empty state if none.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // Get events joined by participant
        $joinedEvents = Event::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->where('status', '!=', 'draft')
            ->get();

        if ($joinedEvents->isEmpty()) {
            return view('participant.leaderboard.show', [
                'event' => null,
                'joinedEvents' => collect(),
                'hasJoinedEvents' => false,
            ]);
        }

        // Try to get first ongoing event
        $activeEvent = $joinedEvents->first(fn ($e) => strtolower($e->status) === 'ongoing');

        // Fall back to first finished/upcoming event if no active ongoing event is found
        $targetEvent = $activeEvent ?: $joinedEvents->first();

        return redirect()->route('events.leaderboard', $targetEvent->id);
    }

    /**
     * Show the leaderboard for a specific event.
     */
    public function show(Request $request, Event $event): View|RedirectResponse
    {
        $user = $request->user();

        // 1. Check if user is registered for the event
        $participantCheck = EventParticipant::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->exists();

        if (! $participantCheck) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda belum bergabung pada event ini.');
        }

        // Get all events joined by the participant to populate the switcher dropdown
        $joinedEvents = Event::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->where('status', '!=', 'draft')
            ->get();

        // 2. Fetch all participants sorted by leaderboard rules
        // Ties-breaker: total_points DESC, total_scans DESC, last_scan_at ASC, joined_at ASC
        $allParticipants = EventParticipant::where('event_id', $event->id)
            ->with('user')
            ->select('event_participants.*')
            ->selectSub(function ($q) {
                $q->selectRaw('count(*)')
                    ->from('checkpoint_scans')
                    ->whereColumn('checkpoint_scans.user_id', 'event_participants.user_id')
                    ->whereColumn('checkpoint_scans.event_id', 'event_participants.event_id');
            }, 'total_scans')
            ->selectSub(function ($q) {
                $q->selectRaw('max(scanned_at)')
                    ->from('checkpoint_scans')
                    ->whereColumn('checkpoint_scans.user_id', 'event_participants.user_id')
                    ->whereColumn('checkpoint_scans.event_id', 'event_participants.event_id');
            }, 'last_scan_at')
            ->orderByDesc('total_points')
            ->orderByDesc('total_scans')
            ->orderByRaw('CASE WHEN last_scan_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('last_scan_at')
            ->orderBy('joined_at')
            ->get();

        // 3. Compute dynamic ranks
        $rankedParticipants = $allParticipants->map(function ($participant, $index) {
            $participant->computed_rank = $index + 1;

            return $participant;
        });

        // 4. Compute statistics (on the whole list)
        $totalParticipants = $rankedParticipants->count();
        $currentUserEP = $rankedParticipants->first(fn ($p) => $p->user_id === $user->id);
        $currentUserRank = $currentUserEP ? '#'.$currentUserEP->computed_rank : '-';
        $highestScore = $rankedParticipants->first()?->total_points ?? 0;
        $averageScore = $totalParticipants > 0 ? round($rankedParticipants->avg('total_points')) : 0;

        // 5. Segregate Top 3 for Podium
        $top3 = $rankedParticipants->take(3);

        // 6. Search filtering
        $search = $request->input('search');
        $filteredParticipants = $rankedParticipants;

        if (! empty($search)) {
            $filteredParticipants = $rankedParticipants->filter(function ($p) use ($search) {
                return str_contains(strtolower($p->user->name), strtolower($search)) ||
                       str_contains(strtolower($p->user->username), strtolower($search));
            });
        }

        // If search is active, the table displays all matching participants (including top 3).
        // If search is not active, the table starts from rank #4 (excluding podium).
        $listParticipants = empty($search)
            ? $filteredParticipants->slice(3)
            : $filteredParticipants;

        // 7. Paginate the list
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;
        $currentPageItems = $listParticipants->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedList = new LengthAwarePaginator(
            $currentPageItems,
            $listParticipants->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('participant.leaderboard.show', [
            'event' => $event,
            'joinedEvents' => $joinedEvents,
            'hasJoinedEvents' => true,
            'top3' => $top3,
            'paginatedList' => $paginatedList,
            'totalParticipants' => $totalParticipants,
            'currentUserRank' => $currentUserRank,
            'highestScore' => $highestScore,
            'averageScore' => $averageScore,
            'search' => $search,
        ]);
    }
}
