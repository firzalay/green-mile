<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\EventParticipant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the participant's home dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        /** @var EventParticipant|null $activeParticipation */
        $activeParticipation = $user->eventParticipants()
            ->whereHas('event', fn ($q) => $q->where('is_active', true))
            ->with('event')
            ->first();

        $leaderboardPreview = null;
        $userRank = null;

        if ($activeParticipation !== null) {
            $leaderboardPreview = $activeParticipation->event
                ->leaderboard()
                ->limit(3)
                ->get();

            $userRank = $activeParticipation->rank;
        }

        $totalPoints = $user->eventParticipants()->sum('current_event_points');

        return view('dashboard.index', [
            'user' => $user,
            'activeParticipation' => $activeParticipation,
            'leaderboardPreview' => $leaderboardPreview,
            'userRank' => $userRank,
            'totalPoints' => $totalPoints,
        ]);
    }
}
