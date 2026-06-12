<?php

use App\Models\Activity;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('organizer dashboard access control', function () {
    it('redirects unauthenticated users to login', function () {
        $this->get(route('organizer.dashboard'))
            ->assertRedirect(route('login'));
    });

    it('returns 403 forbidden for participant role users', function () {
        $user = User::factory()->create([
            'role' => 'participant',
        ]);

        $this->actingAs($user)
            ->get(route('organizer.dashboard'))
            ->assertStatus(403);
    });

    it('allows organizer role users to access the dashboard', function () {
        $user = User::factory()->create([
            'role' => 'organizer',
        ]);

        $this->actingAs($user)
            ->get(route('organizer.dashboard'))
            ->assertOk()
            ->assertViewIs('organizer.dashboard.index');
    });
});

describe('organizer dashboard features', function () {
    it('displays the empty state when organizer has no events', function () {
        $user = User::factory()->create([
            'role' => 'organizer',
        ]);

        $this->actingAs($user)
            ->get(route('organizer.dashboard'))
            ->assertOk()
            ->assertSee('Belum Ada Event')
            ->assertSee('Belum ada event yang dibuat. Mulai buat event pertama Anda');
    });

    it('displays statistics and events correctly', function () {
        $organizer = User::factory()->create([
            'role' => 'organizer',
        ]);

        // Create events owned by organizer
        $event1 = Event::factory()->create([
            'user_id' => $organizer->id,
            'name' => 'GreenRun East Eco',
            'is_active' => true,
            'total_checkpoints' => 5,
        ]);

        $event2 = Event::factory()->create([
            'user_id' => $organizer->id,
            'name' => 'GreenRun West Eco',
            'is_active' => false,
        ]);

        // Seed participants and activity logs
        $participant1 = User::factory()->create(['role' => 'participant']);
        $participant2 = User::factory()->create(['role' => 'participant']);

        EventParticipant::create([
            'event_id' => $event1->id,
            'user_id' => $participant1->id,
            'completed_checkpoints' => 2,
            'current_event_points' => 100,
        ]);

        EventParticipant::create([
            'event_id' => $event1->id,
            'user_id' => $participant2->id,
            'completed_checkpoints' => 4,
            'current_event_points' => 200,
        ]);

        // Create activity logs for today scan
        Activity::create([
            'user_id' => $participant1->id,
            'event_id' => $event1->id,
            'activity_type' => 'scan_checkpoint',
            'description' => 'berhasil scan Checkpoint 1',
            'points' => 50,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($organizer)->get(route('organizer.dashboard'));

        $response->assertOk()
            ->assertSee('GreenRun East Eco')
            ->assertSee('GreenRun West Eco')
            ->assertSee('Total Event')
            ->assertSee('2') // Total Event: 2
            ->assertSee('Event Aktif')
            ->assertSee('1') // Active Event: 1
            ->assertSee('Total Peserta')
            ->assertSee('2') // Total unique participants: 2
            ->assertSee('Scan Hari Ini')
            ->assertSee('1') // Scans today: 1
            ->assertSee('Performa Terkini')
            ->assertSee('60%'); // (2 + 4) / (2 * 5) = 6 / 10 = 60%
    });

    it('renders quick action links to placeholders correctly', function () {
        $organizer = User::factory()->create([
            'role' => 'organizer',
        ]);

        $this->actingAs($organizer)
            ->get(route('organizer.placeholder', 'create-event'))
            ->assertOk()
            ->assertViewIs('organizer.placeholder')
            ->assertSee('Buat Event Baru')
            ->assertSee('Modul ini sedang dalam proses pengembangan');
    });
});

describe('role-dependent authentication redirects', function () {
    it('redirects logged-in participants from root to participant dashboard', function () {
        $user = User::factory()->create(['role' => 'participant']);

        $this->actingAs($user)
            ->get('/')
            ->assertRedirect(route('dashboard'));
    });

    it('redirects logged-in organizers from root to organizer dashboard', function () {
        $user = User::factory()->create(['role' => 'organizer']);

        $this->actingAs($user)
            ->get('/')
            ->assertRedirect(route('organizer.dashboard'));
    });
});
