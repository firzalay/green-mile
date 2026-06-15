<?php

namespace App\Http\Controllers\Participant;

use App\Actions\Reward\RedeemRewardAction;
use App\Http\Controllers\Controller;
use App\Models\Reward;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RewardController extends Controller
{
    /**
     * Display a listing of all active rewards.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $rewards = Reward::where('is_active', true)->get();

        return view('rewards.index', compact('user', 'rewards'));
    }

    /**
     * Display the details of the specified reward.
     */
    public function show(Request $request, int $id): View
    {
        $user = $request->user();
        $reward = Reward::where('is_active', true)->findOrFail($id);

        return view('rewards.show', compact('user', 'reward'));
    }

    /**
     * Redeem the specified reward.
     */
    public function redeem(Request $request, int $id, RedeemRewardAction $action): RedirectResponse
    {
        $user = $request->user();

        if ($user->role !== 'participant') {
            abort(403, 'Hanya peserta yang dapat menukarkan reward.');
        }

        try {
            $action->execute($user, $id);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('rewards.history')
            ->with('success', 'Reward berhasil ditukarkan.');
    }

    /**
     * Display the participant's redemption history.
     */
    public function history(Request $request): View
    {
        $user = $request->user();
        $redemptions = $user->rewardRedemptions()
            ->with('reward')
            ->latest()
            ->get();

        return view('rewards.history', compact('user', 'redemptions'));
    }
}
