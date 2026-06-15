<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\CheckpointScan;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure we have participants
        $participants = User::where('role', 'participant')->get();
        if ($participants->count() < 20) {
            User::factory(20 - $participants->count())->create([
                'role' => 'participant',
                'status' => 'active',
            ]);
            $participants = User::where('role', 'participant')->get();
        }

        $seededParticipants = $participants->take(20);

        // Clear existing participant tables to ensure exact counts
        DB::table('event_participants')->delete();
        DB::table('checkpoint_scans')->delete();
        DB::table('reward_redemptions')->delete();
        DB::table('activities')->delete();

        $events = Event::where('status', '!=', 'draft')->get();
        if ($events->isEmpty()) {
            return;
        }

        // 2. Seed exactly 30 Event Registrations (EventParticipant)
        $registrationsCount = 0;
        $registrations = collect();

        while ($registrationsCount < 30) {
            $user = $seededParticipants->random();
            $event = $events->random();

            $exists = $registrations->contains(fn ($r) => $r->user_id === $user->id && $r->event_id === $event->id);
            if (! $exists) {
                $joinedAt = now()->subDays(fake()->numberBetween(1, 10));
                $ep = EventParticipant::create([
                    'event_id' => $event->id,
                    'user_id' => $user->id,
                    'completed_checkpoints' => 0,
                    'current_event_points' => 0,
                    'total_points' => 0,
                    'joined_at' => $joinedAt,
                    'status' => 'joined',
                ]);
                $registrations->push($ep);
                $registrationsCount++;

                // Log join activity
                Activity::create([
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'activity_type' => 'join_event',
                    'description' => 'bergabung ke event '.$event->name,
                    'points' => 0,
                    'created_at' => $joinedAt,
                ]);
            }
        }

        // 3. Seed exactly 100 CheckpointScan Records
        $scanCount = 0;
        $scannedPairs = collect();

        while ($scanCount < 100) {
            $ep = $registrations->random();
            $event = $ep->event;
            $checkpoints = $event->checkpoints;

            if ($checkpoints->isEmpty()) {
                continue;
            }

            $checkpoint = $checkpoints->random();

            $exists = $scannedPairs->contains(fn ($p) => $p['user_id'] === $ep->user_id && $p['checkpoint_id'] === $checkpoint->id);
            if (! $exists) {
                $scannedAt = now()->subMinutes(fake()->numberBetween(5, 5000));
                CheckpointScan::create([
                    'user_id' => $ep->user_id,
                    'event_id' => $event->id,
                    'checkpoint_id' => $checkpoint->id,
                    'points_awarded' => $checkpoint->points,
                    'scanned_at' => $scannedAt,
                ]);

                // Update participant progress
                $ep->increment('completed_checkpoints');
                $ep->increment('current_event_points', $checkpoint->points);
                $ep->increment('total_points', $checkpoint->points);

                // Log activity
                Activity::create([
                    'user_id' => $ep->user_id,
                    'event_id' => $event->id,
                    'activity_type' => 'scan_checkpoint',
                    'description' => 'berhasil scan '.$checkpoint->name,
                    'points' => $checkpoint->points,
                    'created_at' => $scannedAt,
                ]);

                $scannedPairs->push([
                    'user_id' => $ep->user_id,
                    'checkpoint_id' => $checkpoint->id,
                ]);
                $scanCount++;
            }
        }

        // Update registration status to 'completed' for finished ones
        foreach ($registrations as $ep) {
            $totalEventCheckpoints = $ep->event->total_checkpoints ?: 8;
            if ($ep->completed_checkpoints >= $totalEventCheckpoints) {
                $ep->update(['status' => 'completed']);
            }
        }

        // 4. Seed exactly 15 Reward Redemptions
        $rewards = Reward::where('is_active', true)->get();
        if ($rewards->isEmpty()) {
            return;
        }

        $redemptionCount = 0;
        $attempts = 0; // Avoid infinite loop if points are exhausted
        while ($redemptionCount < 15 && $attempts < 200) {
            $attempts++;
            $ep = $registrations->random();
            $reward = $rewards->random();

            $user = User::find($ep->user_id);
            if ($user->points >= $reward->required_points && $reward->stock > 0) {
                $redeemedAt = now()->subMinutes(fake()->numberBetween(1, 1000));

                RewardRedemption::create([
                    'user_id' => $user->id,
                    'reward_id' => $reward->id,
                    'points_used' => $reward->required_points,
                    'status' => 'pending',
                    'redeemed_at' => $redeemedAt,
                ]);

                $reward->decrement('stock');
                $reward->increment('total_redeemed');

                // Log activity
                Activity::create([
                    'user_id' => $user->id,
                    'event_id' => null,
                    'activity_type' => 'redeem_reward',
                    'description' => 'menukarkan '.$reward->required_points.' poin untuk '.$reward->name,
                    'points' => -$reward->required_points,
                    'created_at' => $redeemedAt,
                ]);

                $redemptionCount++;
            }
        }
    }
}
