<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizer = User::where('role', 'organizer')->first();
        if (! $organizer) {
            return;
        }

        $participants = User::where('role', 'participant')->get();

        // 1. Ongoing Event
        $ongoingEvent = Event::factory()->ongoing()->create([
            'name' => 'GreenRun Surabaya',
            'location' => 'Taman Bungkul, Surabaya',
            'organizer_id' => $organizer->id,
            'total_checkpoints' => 8,
            'description' => 'Aksi lari peduli sampah plastik di Surabaya. Kumpulkan poin di setiap checkpoint dengan melakukan aksi pembersihan lingkungan.',
            'banner' => 'https://images.unsplash.com/photo-1530541930197-ff16ac917b0e?auto=format&fit=crop&q=80&w=800',
            'total_rewards' => 'Rp 15.000.000',
            'max_points' => 800,
            'total_point_pool' => 50000,
            'join_code' => 'SBY2026',
        ]);

        // 2. Upcoming Event
        $upcomingEvent = Event::factory()->upcoming()->create([
            'name' => 'GreenRun Jakarta Forest Run',
            'location' => 'Hutan Kota GBK, Jakarta',
            'organizer_id' => $organizer->id,
            'total_checkpoints' => 10,
            'description' => 'Event lari peduli emisi karbon di pusat ibu kota. Bersama kurangi polusi udara Jakarta dengan menanam pohon!',
            'banner' => 'https://images.unsplash.com/photo-1476480862126-209bfaa8edc8?auto=format&fit=crop&q=80&w=800',
            'total_rewards' => 'Rp 25.000.000',
            'max_points' => 1000,
            'join_code' => 'JKT2026',
        ]);

        // 3. Finished Event
        $finishedEvent = Event::factory()->finished()->create([
            'name' => 'GreenRun Bali Coastal Clean Run',
            'location' => 'Pantai Kuta, Bali',
            'organizer_id' => $organizer->id,
            'total_checkpoints' => 6,
            'description' => 'Aksi nyata menjaga kelestarian laut dan pantai Bali dari sampah plastik sekali pakai.',
            'banner' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&q=80&w=800',
            'total_rewards' => 'Rp 10.000.000',
            'max_points' => 600,
            'join_code' => 'BALI2026',
        ]);

        // 4. Upcoming Event 2 (Bandung Hills Run)
        $upcomingEvent2 = Event::factory()->upcoming()->create([
            'name' => 'GreenRun Bandung Hills Run',
            'location' => 'Taman Hutan Raya Juanda, Bandung',
            'organizer_id' => $organizer->id,
            'total_checkpoints' => 7,
            'description' => 'Jelajahi perbukitan Bandung sambil berkontribusi menjaga kelestarian alam hutan raya.',
            'banner' => 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&q=80&w=800',
            'total_rewards' => 'Rp 12.000.000',
            'max_points' => 700,
            'join_code' => 'BDG2026',
        ]);

        // 5. Draft Event (Jogja Cultural Trail)
        $draftEvent = Event::factory()->inactive()->create([
            'name' => 'GreenRun Jogja Cultural Trail',
            'location' => 'Candi Prambanan, Yogyakarta',
            'organizer_id' => $organizer->id,
            'total_checkpoints' => 5,
            'description' => 'Event lari budaya menyusuri situs bersejarah Jogja dan mengkampanyekan zero-waste event.',
            'banner' => 'https://images.unsplash.com/photo-1604999333679-b86d54738315?auto=format&fit=crop&q=80&w=800',
            'total_rewards' => 'Rp 8.000.000',
            'max_points' => 500,
            'status' => 'draft',
            'join_code' => 'JOG2026',
        ]);

        // Link participants to events
        foreach ([$ongoingEvent, $finishedEvent] as $event) {
            $eventParticipants = $participants->random(min(30, $participants->count()));
            foreach ($eventParticipants as $participant) {
                $completed = fake()->numberBetween(0, $event->total_checkpoints);
                $points = $completed * 50;

                EventParticipant::create([
                    'event_id' => $event->id,
                    'user_id' => $participant->id,
                    'completed_checkpoints' => $completed,
                    'current_event_points' => $points,
                    'total_points' => $points + fake()->numberBetween(0, 500),
                    'joined_at' => now()->subDays(fake()->numberBetween(1, 10)),
                    'status' => $completed === $event->total_checkpoints ? 'completed' : 'joined',
                ]);
            }
        }
    }
}
