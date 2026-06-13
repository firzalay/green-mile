<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class RewardHistoryController extends Controller
{
    /**
     * Display the participant's redemption history.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $redemptions = $user->rewardRedemptions()
            ->with('reward')
            ->latest()
            ->get();

        return view('rewards.history', compact('user', 'redemptions'));
    }
}
