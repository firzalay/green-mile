<?php

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('event discovery access', function () {
    it('redirects unauthenticated users to login for events list', function () {
        $this->get(route('events.index'))
            ->assertRedirect(route('login'));
    });

    it('redirects unauthenticated users to login for event details', function () {
        $this->get(route('events.show', 1))
            ->assertRedirect(route('login'));
    });

    it('allows authenticated users to view events list', function () {
        $user = User::factory()->create();
        $event = Event::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->get(route('events.index'))
            ->assertOk()
            ->assertViewIs('events.index')
            ->assertSee($event->name);
    });

    it('allows authenticated users to view event details', function () {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'is_active' => true,
            'description' => 'Aksi lari peduli bumi',
            'total_rewards' => 'Rp 10.000.000',
            'max_points' => 500,
        ]);

        $this->actingAs($user)
            ->get(route('events.show', $event->id))
            ->assertOk()
            ->assertViewIs('events.show')
            ->assertSee($event->name)
            ->assertSee('Aksi lari peduli bumi')
            ->assertSee('Rp 10.000.000')
            ->assertSee('500 pts');
    });
});

describe('join event flow', function () {
    it('allows an authenticated user to join an active event', function () {
        $user = User::factory()->create();
        $event = Event::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->post(route('events.join', $event->id))
            ->assertRedirect(route('events.show', $event->id))
            ->assertSessionHas('success', "Berhasil bergabung ke {$event->name}");

        $this->assertDatabaseHas('event_participants', [
            'event_id' => $event->id,
            'user_id' => $user->id,
            'completed_checkpoints' => 0,
            'current_event_points' => 0,
        ]);
    });

    it('prevents a user from joining the same event twice', function () {
        $user = User::factory()->create();
        $event = Event::factory()->create(['is_active' => true]);

        EventParticipant::factory()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->post(route('events.join', $event->id))
            ->assertRedirect(route('events.show', $event->id))
            ->assertSessionHas('error', 'Kamu sudah bergabung dalam event ini.');
    });
});

describe('dashboard integration with joined event', function () {
    it('shows the joined active event on the dashboard with correct stats', function () {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'name' => 'GreenRun Surabaya 2026',
            'is_active' => true,
            'total_checkpoints' => 10,
        ]);

        EventParticipant::factory()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'completed_checkpoints' => 4,
            'current_event_points' => 200,
        ]);

        EventParticipant::factory()->create([
            'event_id' => $event->id,
            'current_event_points' => 500,
            'completed_checkpoints' => 10,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('GreenRun Surabaya 2026')
            ->assertSee('4 / 10')
            ->assertSee('200')
            ->assertSee('#2');
    });
});
