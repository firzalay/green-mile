<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['scan_checkpoint', 'join_event', 'redeem_reward']);
        $points = match ($type) {
            'scan_checkpoint' => 50,
            'join_event' => 0,
            'redeem_reward' => -200,
        };

        $descriptions = [
            'scan_checkpoint' => 'berhasil scan Checkpoint '.fake()->numberBetween(1, 5),
            'join_event' => 'bergabung ke event',
            'redeem_reward' => 'menukar reward '.fake()->randomElement(['Voucher Kopi', 'Tumbler Ramah Lingkungan', 'Kaos GreenRun']),
        ];

        return [
            'user_id' => User::factory(),
            'event_id' => Event::factory(),
            'activity_type' => $type,
            'description' => $descriptions[$type],
            'points' => $points,
            'created_at' => now()->subMinutes(fake()->numberBetween(1, 1440)),
        ];
    }
}
