<?php

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('dashboard access', function () {
    it('redirects unauthenticated users to login', function () {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    });

    it('allows authenticated users to access the dashboard', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewIs('dashboard.index');
    });
});

describe('dashboard with no active event', function () {
    it('renders the no-active-event empty state', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('activeParticipation', null)
            ->assertSee('Belum Ada Event Aktif');
    });

    it('shows empty state when user only has inactive events', function () {
        $user = User::factory()->create();
        $event = Event::factory()->inactive()->create();
        EventParticipant::factory()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Belum Ada Event Aktif');
    });
});

describe('dashboard with an active event', function () {
    it('shows the active event card with correct event data', function () {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'name' => 'GreenRun Nusantara 2026',
            'is_active' => true,
        ]);
        EventParticipant::factory()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'completed_checkpoints' => 3,
            'current_event_points' => 150,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('GreenRun Nusantara 2026')
            ->assertSee('Event Aktif')
            ->assertViewHas('activeParticipation');
    });

    it('passes correct points data to the view', function () {
        $user = User::factory()->create();
        $event = Event::factory()->create(['is_active' => true]);
        EventParticipant::factory()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'completed_checkpoints' => 5,
            'current_event_points' => 250,
            'total_points' => 750,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('activeParticipation', fn ($p) => $p->current_event_points === 250);
        $response->assertViewHas('totalPoints', 250);
    });

    it('shows leaderboard preview for active event', function () {
        $user = User::factory()->create();
        $event = Event::factory()->create(['is_active' => true]);

        EventParticipant::factory()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'current_event_points' => 300,
            'rank' => 1,
        ]);

        // Add more participants for leaderboard
        EventParticipant::factory(2)->create([
            'event_id' => $event->id,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('leaderboardPreview', fn ($l) => $l->isNotEmpty());
    });

    it('does not show another users active event on the dashboard', function () {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $event = Event::factory()->create([
            'name' => 'Eco Sprint Jakarta',
            'is_active' => true,
        ]);

        // Only other user is a participant
        EventParticipant::factory()->create([
            'event_id' => $event->id,
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('activeParticipation', null)
            ->assertSee('Belum Ada Event Aktif');
    });
});

describe('root redirect', function () {
    it('redirects guests from / to login', function () {
        $this->get('/')
            ->assertRedirect(route('login'));
    });

    it('redirects authenticated users from / to dashboard', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/')
            ->assertRedirect(route('dashboard'));
    });
});
