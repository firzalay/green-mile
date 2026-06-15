<?php

namespace Database\Seeders;

use App\Models\Checkpoint;
use App\Models\CheckpointScan;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeaderboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Get or create organizer
        $organizer = User::where('role', 'organizer')->first();
        if (! $organizer) {
            $organizer = User::factory()->create([
                'role' => 'organizer',
                'name' => 'Leaderboard Organizer',
                'email' => 'leaderboard_org@example.com',
            ]);
        }

        // 2. Create Event with high max_points and point_pool
        $event = Event::create([
            'name' => 'GreenRun Championship 2026',
            'location' => 'Gelora Bung Karno, Jakarta',
            'organizer_id' => $organizer->id,
            'total_checkpoints' => 50,
            'description' => 'Championship event to challenge the best environment-loving runners.',
            'banner' => 'https://images.unsplash.com/photo-1502224562085-639556652f33?auto=format&fit=crop&q=80&w=800',
            'total_rewards' => 'Rp 50.000.000',
            'max_points' => 3000,
            'total_point_pool' => 500000,
            'status' => 'ongoing',
            'join_code' => 'CHAMP26',
        ]);

        // 3. Create 50 Checkpoints, each offering 50 points
        $checkpoints = [];
        for ($i = 1; $i <= 50; $i++) {
            $checkpoints[] = Checkpoint::create([
                'event_id' => $event->id,
                'name' => 'CP-'.sprintf('%02d', $i),
                'location' => 'Titik '.$i,
                'description' => 'Checkpoint '.$i.' untuk event GreenRun Championship.',
                'sequence' => $i,
                'points' => 50,
                'status' => 'active',
                'qr_token' => 'qr-token-champ-'.$i,
            ]);
        }

        // 4. Create 100 Participant Users
        $participants = [];
        for ($i = 1; $i <= 100; $i++) {
            $participants[] = User::factory()->create([
                'role' => 'participant',
                'name' => 'Runner '.$i,
                'username' => 'runner'.$i,
                'email' => 'runner'.$i.'@example.com',
            ]);
        }

        // 5. Register all 100 participants to the event
        $registrations = [];
        foreach ($participants as $user) {
            $registrations[] = EventParticipant::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'completed_checkpoints' => 0,
                'current_event_points' => 0,
                'total_points' => 0,
                'joined_at' => now()->subDays(fake()->numberBetween(1, 15)),
                'status' => 'joined',
            ]);
        }

        // 6. Distribute exactly 500 scans to verify ranking rules
        // Deterministic scan counts:
        // - Runner 1 (index 0): 50 scans (2500 pts)
        // - Runner 2 (index 1): 48 scans (2400 pts)
        // - Runner 3 (index 2): 46 scans (2300 pts)
        // - Runners 4-13 (10 users): 15 scans (750 pts) = 150 scans
        // - Runners 14-33 (20 users): 8 scans (400 pts) = 160 scans
        // - Runners 34-53 (20 users): 4 scans (200 pts) = 80 scans
        // - Runners 54-69 (16 users): 1 scan (50 pts) = 16 scans
        // - Runners 70-100 (31 users): 0 scans (0 pts) = 0 scans
        // Total scans = 50 + 48 + 46 + 150 + 160 + 80 + 16 = 500 scans
        $scanDistribution = [];
        for ($i = 0; $i < 100; $i++) {
            if ($i === 0) {
                $scanDistribution[$i] = 50;
            } elseif ($i === 1) {
                $scanDistribution[$i] = 48;
            } elseif ($i === 2) {
                $scanDistribution[$i] = 46;
            } elseif ($i >= 3 && $i <= 12) {
                $scanDistribution[$i] = 15;
            } elseif ($i >= 13 && $i <= 32) {
                $scanDistribution[$i] = 8;
            } elseif ($i >= 33 && $i <= 52) {
                $scanDistribution[$i] = 4;
            } elseif ($i >= 53 && $i <= 68) {
                $scanDistribution[$i] = 1;
            } else {
                $scanDistribution[$i] = 0;
            }
        }

        $baseTime = now()->subHours(100);

        foreach ($scanDistribution as $index => $scansCount) {
            $reg = $registrations[$index];
            $user = $reg->user;

            for ($s = 0; $s < $scansCount; $s++) {
                $checkpoint = $checkpoints[$s];
                // Increment time so each scan has a distinct chronological order
                $scannedAt = (clone $baseTime)->addMinutes($index * 10 + $s);

                CheckpointScan::create([
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'checkpoint_id' => $checkpoint->id,
                    'points_awarded' => $checkpoint->points,
                    'scanned_at' => $scannedAt,
                ]);

                $reg->increment('completed_checkpoints');
                $reg->increment('current_event_points', $checkpoint->points);
                $reg->increment('total_points', $checkpoint->points);
            }

            if ($reg->completed_checkpoints >= $event->total_checkpoints) {
                $reg->update(['status' => 'completed']);
            }
        }
    }
}
