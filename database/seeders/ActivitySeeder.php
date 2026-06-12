<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $participants = User::where('role', 'participant')->get();
        $events = Event::where('is_active', true)->get();

        if ($participants->isEmpty() || $events->isEmpty()) {
            return;
        }

        for ($i = 0; $i < 25; $i++) {
            $user = $participants->random();
            $event = $events->random();
            $type = fake()->randomElement(['scan_checkpoint', 'join_event', 'redeem_reward']);

            $points = match ($type) {
                'scan_checkpoint' => 50,
                'join_event' => 0,
                'redeem_reward' => -200,
            };

            $descriptions = [
                'scan_checkpoint' => 'berhasil scan Checkpoint '.fake()->numberBetween(1, $event->total_checkpoints ?: 5),
                'join_event' => 'bergabung ke event',
                'redeem_reward' => 'menukar reward '.fake()->randomElement(['Voucher Kopi', 'Tumbler Ramah Lingkungan', 'Kaos GreenRun']),
            ];

            $createdAt = $i < 12
                ? now()->subMinutes(fake()->numberBetween(5, 600))
                : now()->subDays(fake()->numberBetween(1, 15));

            Activity::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'activity_type' => $type,
                'description' => $descriptions[$type],
                'points' => $points,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }
}
