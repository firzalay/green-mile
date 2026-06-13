<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Reward;
use App\Models\RewardRedemption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
    public function redeem(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();

        if ($user->role !== 'participant') {
            abort(403, 'Hanya peserta yang dapat menukarkan reward.');
        }

        $error = DB::transaction(function () use ($user, $id, &$reward) {
            $reward = Reward::lockForUpdate()->find($id);

            if (! $reward || ! $reward->is_active) {
                return 'Reward tidak ditemukan atau tidak aktif.';
            }

            if ($reward->stock <= 0) {
                return 'Reward sedang tidak tersedia.';
            }

            if ($user->points < $reward->required_points) {
                return 'Poin Anda tidak mencukupi untuk menukarkan reward ini.';
            }

            // Deduct stock
            $reward->decrement('stock');

            // Create redemption record
            RewardRedemption::create([
                'user_id' => $user->id,
                'reward_id' => $reward->id,
                'points_used' => $reward->required_points,
                'status' => 'pending',
                'redeemed_at' => now(),
            ]);

            // Create Activity log
            Activity::create([
                'user_id' => $user->id,
                'event_id' => null,
                'activity_type' => 'redeem_reward',
                'description' => 'menukarkan '.$reward->required_points.' poin untuk '.$reward->name,
                'points' => -$reward->required_points,
            ]);

            return null; // success
        });

        if ($error) {
            return redirect()->back()->with('error', $error);
        }

        return redirect()->route('rewards.history')
            ->with('success', 'Reward berhasil ditukarkan.');
    }
}
