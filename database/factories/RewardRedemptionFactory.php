<?php

namespace Database\Factories;

use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RewardRedemption>
 */
class RewardRedemptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'reward_id' => Reward::factory(),
            'points_used' => $this->faker->numberBetween(100, 1000),
            'status' => 'pending',
            'redeemed_at' => now(),
        ];
    }
}
