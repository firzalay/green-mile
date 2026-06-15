<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
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

        $activeEvent = $joinedEvents->first(fn ($e) => strtolower($e->status) === 'ongoing');
        $targetEvent = $activeEvent ?: $joinedEvents->first();

        return redirect()->route('events.leaderboard', $targetEvent->id);
    }

    /**
     * Show the leaderboard for a specific event.
     */
    public function show(Request $request, Event $event): View|RedirectResponse
    {
        $user = $request->user();

        $participantCheck = EventParticipant::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->exists();

        if (! $participantCheck) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda belum bergabung pada event ini.');
        }

        $joinedEvents = Event::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->where('status', '!=', 'draft')
            ->get();

        $rankedParticipants = EventParticipant::rankedLeaderboard($event->id);

        $totalParticipants = $rankedParticipants->count();
        $currentUserEP = $rankedParticipants->first(fn ($p) => $p->user_id === $user->id);
        $currentUserRank = $currentUserEP ? '#'.$currentUserEP->computed_rank : '-';
        $highestScore = $rankedParticipants->first()?->total_points ?? 0;
        $averageScore = $totalParticipants > 0 ? round($rankedParticipants->avg('total_points')) : 0;

        $top3 = $rankedParticipants->take(3);

        $search = $request->input('search');
        $filteredParticipants = $rankedParticipants;

        if (! empty($search)) {
            $filteredParticipants = $rankedParticipants->filter(function ($p) use ($search) {
                return str_contains(strtolower($p->user->name), strtolower($search)) ||
                       str_contains(strtolower($p->user->username), strtolower($search));
            });
        }

        $listParticipants = empty($search)
            ? $filteredParticipants->slice(3)
            : $filteredParticipants;

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
