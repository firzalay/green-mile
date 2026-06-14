<?php

use App\Models\Checkpoint;
use App\Models\CheckpointScan;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('leaderboard access control', function () {
    it('redirects guest users to login', function () {
        $this->get(route('leaderboard.index'))->assertRedirect(route('login'));

        $event = Event::factory()->ongoing()->create();
        $this->get(route('events.leaderboard', $event->id))->assertRedirect(route('login'));
    });

    it('denies access to participants who have not joined the event', function () {
        $participant = User::factory()->create(['role' => 'participant']);
        $event = Event::factory()->ongoing()->create();

        // Acting as participant but hasn't joined -> redirects/flashes error
        $response = $this->actingAs($participant)->get(route('events.leaderboard', $event->id));
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error', 'Anda belum bergabung pada event ini.');
    });

    it('allows access to participants who have joined the event', function () {
        $participant = User::factory()->create(['role' => 'participant']);
        $event = Event::factory()->ongoing()->create();

        EventParticipant::create([
            'event_id' => $event->id,
            'user_id' => $participant->id,
            'status' => 'joined',
        ]);

        $this->actingAs($participant)->get(route('events.leaderboard', $event->id))->assertOk();
    });
});

describe('leaderboard index redirect', function () {
    it('shows empty state if user has not joined any events', function () {
        $participant = User::factory()->create(['role' => 'participant']);

        $response = $this->actingAs($participant)->get(route('leaderboard.index'));
        $response->assertOk()
            ->assertSee('Belum Bergabung Event')
            ->assertSee('Daftar Event Sekarang');
    });

    it('redirects to the ongoing event leaderboard if joined', function () {
        $participant = User::factory()->create(['role' => 'participant']);
        $ongoingEvent = Event::factory()->ongoing()->create(['name' => 'Event Ongoing']);
        $finishedEvent = Event::factory()->finished()->create(['name' => 'Event Finished']);

        EventParticipant::create([
            'event_id' => $ongoingEvent->id,
            'user_id' => $participant->id,
        ]);

        EventParticipant::create([
            'event_id' => $finishedEvent->id,
            'user_id' => $participant->id,
        ]);

        $response = $this->actingAs($participant)->get(route('leaderboard.index'));
        // Redirects to ongoing event's leaderboard
        $response->assertRedirect(route('events.leaderboard', $ongoingEvent->id));
    });
});

describe('leaderboard ranking calculation and rules', function () {
    it('sorts participants according to the ties-breaker ranking rules', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->ongoing()->create([
            'organizer_id' => $organizer->id,
            'max_points' => 3000,
        ]);

        // Create 5 checkpoints
        $cps = [];
        for ($i = 1; $i <= 5; $i++) {
            $cps[] = Checkpoint::create([
                'event_id' => $event->id,
                'name' => 'CP '.$i,
                'points' => 100,
                'sequence' => $i,
                'status' => 'active',
            ]);
        }

        // Participant A: 300 Points, 3 Scans (Scanned at 10:00, 10:10, 10:20)
        $userA = User::factory()->create(['role' => 'participant', 'name' => 'Runner A', 'username' => 'runnera']);
        $epA = EventParticipant::create([
            'event_id' => $event->id,
            'user_id' => $userA->id,
            'completed_checkpoints' => 3,
            'current_event_points' => 300,
            'total_points' => 300,
        ]);
        foreach ([$cps[0], $cps[1], $cps[2]] as $index => $cp) {
            CheckpointScan::create([
                'user_id' => $userA->id,
                'event_id' => $event->id,
                'checkpoint_id' => $cp->id,
                'points_awarded' => 100,
                'scanned_at' => now()->subMinutes(60 - $index * 10), // 10:00, 10:10, 10:20 relative
            ]);
        }

        // Participant B (Ties primary - same points: 300 pts, but fewer scans: 2 scans of 150 pts each)
        // User B has 300 points, 2 scans -> Should rank below A (who has 3 scans)
        $userB = User::factory()->create(['role' => 'participant', 'name' => 'Runner B', 'username' => 'runnerb']);
        $epB = EventParticipant::create([
            'event_id' => $event->id,
            'user_id' => $userB->id,
            'completed_checkpoints' => 2,
            'current_event_points' => 300,
            'total_points' => 300,
        ]);
        foreach ([$cps[0], $cps[1]] as $index => $cp) {
            CheckpointScan::create([
                'user_id' => $userB->id,
                'event_id' => $event->id,
                'checkpoint_id' => $cp->id,
                'points_awarded' => 150,
                'scanned_at' => now()->subMinutes(60 - $index * 10),
            ]);
        }

        // Participant C (Ties primary & secondary - same points: 300 pts, same scans: 3 scans, but earlier scan time)
        // User C scanned their last checkpoint at 10:15 (User A finished at 10:20) -> User C should rank above User A
        $userC = User::factory()->create(['role' => 'participant', 'name' => 'Runner C', 'username' => 'runnerc']);
        $epC = EventParticipant::create([
            'event_id' => $event->id,
            'user_id' => $userC->id,
            'completed_checkpoints' => 3,
            'current_event_points' => 300,
            'total_points' => 300,
        ]);
        foreach ([$cps[0], $cps[1], $cps[2]] as $index => $cp) {
            CheckpointScan::create([
                'user_id' => $userC->id,
                'event_id' => $event->id,
                'checkpoint_id' => $cp->id,
                'points_awarded' => 100,
                'scanned_at' => now()->subMinutes(70 - $index * 10), // finishes 10 mins earlier than A
            ]);
        }

        // Order should be:
        // Rank 1: Runner C (300 pts, 3 scans, last scan earlier)
        // Rank 2: Runner A (300 pts, 3 scans, last scan later)
        // Rank 3: Runner B (300 pts, 2 scans)

        $response = $this->actingAs($userA)->get(route('events.leaderboard', $event->id));
        $response->assertOk();

        // Check the statistics
        $response->assertSee('Peserta')
            ->assertSee('3') // total participants
            ->assertSee('Rank Anda')
            ->assertSee('#2') // user A rank
            ->assertSee('Skor Tertinggi')
            ->assertSee('300')
            ->assertSee('Rata-Rata')
            ->assertSee('300');
    });

    it('filters participants correctly by name when search query is supplied', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->ongoing()->create(['organizer_id' => $organizer->id]);

        $user1 = User::factory()->create(['role' => 'participant', 'name' => 'Ahmad Budi', 'username' => 'ahmadbudi']);
        EventParticipant::create([
            'event_id' => $event->id,
            'user_id' => $user1->id,
            'total_points' => 500,
        ]);

        $user2 = User::factory()->create(['role' => 'participant', 'name' => 'Siti Aminah', 'username' => 'sitiaminah']);
        EventParticipant::create([
            'event_id' => $event->id,
            'user_id' => $user2->id,
            'total_points' => 400,
        ]);

        $viewer = User::factory()->create(['role' => 'participant', 'name' => 'Viewer User', 'username' => 'viewer']);
        EventParticipant::create([
            'event_id' => $event->id,
            'user_id' => $viewer->id,
            'total_points' => 0,
        ]);

        // Accessing page without search shows both
        $this->actingAs($viewer)->get(route('events.leaderboard', $event->id))
            ->assertSee('Ahmad Budi')
            ->assertSee('Siti Aminah');

        // Searching for 'Siti'
        $this->actingAs($viewer)->get(route('events.leaderboard', $event->id).'?search=Siti')
            ->assertSee('Siti Aminah')
            ->assertDontSee('Ahmad Budi');
    });

    it('filters participants correctly by username when search query is supplied', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->ongoing()->create(['organizer_id' => $organizer->id]);

        $user1 = User::factory()->create(['role' => 'participant', 'name' => 'Ahmad Budi', 'username' => 'ahmadbudi']);
        EventParticipant::create([
            'event_id' => $event->id,
            'user_id' => $user1->id,
            'total_points' => 500,
        ]);

        $user2 = User::factory()->create(['role' => 'participant', 'name' => 'Siti Aminah', 'username' => 'sitiaminah']);
        EventParticipant::create([
            'event_id' => $event->id,
            'user_id' => $user2->id,
            'total_points' => 400,
        ]);

        $viewer = User::factory()->create(['role' => 'participant', 'name' => 'Viewer User', 'username' => 'viewer']);
        EventParticipant::create([
            'event_id' => $event->id,
            'user_id' => $viewer->id,
            'total_points' => 0,
        ]);

        // Searching for '@ahmadbudi' username (search = ahmad)
        $this->actingAs($viewer)->get(route('events.leaderboard', $event->id).'?search=ahmad')
            ->assertSee('Ahmad Budi')
            ->assertDontSee('Siti Aminah');
    });
});
