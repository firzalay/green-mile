<?php

namespace App\Actions\Reward;

use App\Models\Activity;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RedeemRewardAction
{
    /**
     * Redeem a reward for the given user.
     *
     * @throws \Exception
     */
    public function execute(User $user, int $rewardId): Reward
    {
        $reward = null;

        DB::transaction(function () use ($user, $rewardId, &$reward) {
            $reward = Reward::lockForUpdate()->find($rewardId);

            if (! $reward || ! $reward->is_active) {
                throw new \Exception('Reward tidak ditemukan atau tidak aktif.');
            }

            if ($reward->stock <= 0) {
                throw new \Exception('Reward sedang tidak tersedia.');
            }

            if ($user->points < $reward->required_points) {
                throw new \Exception('Poin Anda tidak mencukupi untuk menukarkan reward ini.');
            }

            $reward->decrement('stock');
            $reward->increment('total_redeemed');

            RewardRedemption::create([
                'user_id' => $user->id,
                'reward_id' => $reward->id,
                'points_used' => $reward->required_points,
                'status' => 'pending',
                'redeemed_at' => now(),
            ]);

            Activity::create([
                'user_id' => $user->id,
                'event_id' => null,
                'activity_type' => 'redeem_reward',
                'description' => 'menukarkan '.$reward->required_points.' poin untuk '.$reward->name,
                'points' => -$reward->required_points,
            ]);
        });

        return $reward;
    }
}
