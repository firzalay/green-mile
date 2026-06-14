<?php

use App\Models\Checkpoint;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

describe('event creation and update validation', function () {
    it('requires point_pool on event creation', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);

        $payload = [
            'name' => 'New Event',
            'location' => 'Surabaya',
            'start_date' => now()->addDays(1)->format('Y-m-d'),
            'end_date' => now()->addDays(2)->format('Y-m-d'),
            'max_points' => 100,
        ];

        $response = $this->actingAs($organizer)
            ->post(route('organizer.events.store'), $payload);

        $response->assertSessionHasErrors('point_pool');
    });

    it('requires point_pool to be positive and non-negative', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);

        $payload = [
            'name' => 'New Event',
            'location' => 'Surabaya',
            'start_date' => now()->addDays(1)->format('Y-m-d'),
            'end_date' => now()->addDays(2)->format('Y-m-d'),
            'max_points' => 100,
            'point_pool' => 0,
        ];

        $response = $this->actingAs($organizer)
            ->post(route('organizer.events.store'), $payload);

        $response->assertSessionHasErrors('point_pool');

        $payload['point_pool'] = -100;
        $response = $this->actingAs($organizer)
            ->post(route('organizer.events.store'), $payload);

        $response->assertSessionHasErrors('point_pool');
    });

    it('initializes remaining_point_pool equal to point_pool on creation', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);

        $payload = [
            'name' => 'New Event',
            'location' => 'Surabaya',
            'start_date' => now()->addDays(1)->format('Y-m-d'),
            'end_date' => now()->addDays(2)->format('Y-m-d'),
            'max_points' => 100,
            'point_pool' => 5000,
        ];

        $response = $this->actingAs($organizer)
            ->post(route('organizer.events.store'), $payload);

        $response->assertRedirect(route('organizer.events.index'));

        $this->assertDatabaseHas('events', [
            'name' => 'New Event',
            'point_pool' => 5000,
            'remaining_point_pool' => 5000,
        ]);
    });

    it('prevents updating point_pool to less than distributed points', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create([
            'organizer_id' => $organizer->id,
            'point_pool' => 5000,
            'remaining_point_pool' => 3000,
        ]);

        $updatePayload = [
            'name' => $event->name,
            'location' => $event->location,
            'start_date' => $event->start_date->format('Y-m-d'),
            'end_date' => $event->end_date->format('Y-m-d'),
            'max_points' => $event->max_points,
            'status' => 'published',
            'point_pool' => 1500,
        ];

        $response = $this->actingAs($organizer)
            ->put(route('organizer.events.update', $event->id), $updatePayload);

        $response->assertSessionHasErrors('point_pool');
    });

    it('allows updating point_pool and adjusts remaining_point_pool correctly', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create([
            'organizer_id' => $organizer->id,
            'point_pool' => 5000,
            'remaining_point_pool' => 3000,
        ]);

        $updatePayload = [
            'name' => $event->name,
            'location' => $event->location,
            'start_date' => $event->start_date->format('Y-m-d'),
            'end_date' => $event->end_date->format('Y-m-d'),
            'max_points' => $event->max_points,
            'status' => 'published',
            'point_pool' => 6000,
        ];

        $response = $this->actingAs($organizer)
            ->put(route('organizer.events.update', $event->id), $updatePayload);

        $response->assertRedirect(route('organizer.events.show', $event->id));

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'point_pool' => 6000,
            'remaining_point_pool' => 4000,
        ]);
    });
});

describe('scan checkpoints point pool validation', function () {
    it('decrements remaining_point_pool on successful checkpoint scan', function () {
        $participant = User::factory()->create(['role' => 'participant']);
        $event = Event::factory()->create([
            'status' => 'ongoing',
            'point_pool' => 5000,
            'remaining_point_pool' => 5000,
        ]);
        $checkpoint = Checkpoint::factory()->create([
            'event_id' => $event->id,
            'status' => 'active',
            'points' => 150,
            'qr_token' => (string) Str::uuid(),
        ]);

        EventParticipant::factory()->create([
            'user_id' => $participant->id,
            'event_id' => $event->id,
        ]);

        $response = $this->actingAs($participant)
            ->postJson(route('scanner.scan'), ['qr_token' => $checkpoint->qr_token]);

        $response->assertOk();

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'remaining_point_pool' => 4850,
        ]);
    });

    it('rejects checkpoint scan if event remaining_point_pool is insufficient', function () {
        $participant = User::factory()->create(['role' => 'participant']);
        $event = Event::factory()->create([
            'status' => 'ongoing',
            'point_pool' => 100,
            'remaining_point_pool' => 50,
        ]);
        $checkpoint = Checkpoint::factory()->create([
            'event_id' => $event->id,
            'status' => 'active',
            'points' => 100,
            'qr_token' => (string) Str::uuid(),
        ]);

        EventParticipant::factory()->create([
            'user_id' => $participant->id,
            'event_id' => $event->id,
        ]);

        $response = $this->actingAs($participant)
            ->postJson(route('scanner.scan'), ['qr_token' => $checkpoint->qr_token]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'Poin event telah habis.',
            ]);

        $this->assertEquals(50, $event->fresh()->remaining_point_pool);
    });
});

describe('monitoring dashboard and event details statistics', function () {
    it('displays correct Point Pool Summary on organizer dashboard', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);

        Event::factory()->create([
            'organizer_id' => $organizer->id,
            'point_pool' => 5000,
            'remaining_point_pool' => 3500,
        ]);

        Event::factory()->create([
            'organizer_id' => $organizer->id,
            'point_pool' => 10000,
            'remaining_point_pool' => 7000,
        ]);

        $response = $this->actingAs($organizer)->get(route('organizer.dashboard'));

        $response->assertOk()
            ->assertSee('Ringkasan Point Pool')
            ->assertSee('15,000')
            ->assertSee('10,500')
            ->assertSee('4,500');
    });

    it('displays correct Point Pool and Distribution stats on event details page', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create([
            'organizer_id' => $organizer->id,
            'point_pool' => 50000,
            'remaining_point_pool' => 32500,
        ]);

        $response = $this->actingAs($organizer)->get(route('organizer.events.show', $event->id));

        $response->assertOk()
            ->assertSee('Statistik Distribusi Poin')
            ->assertSee('50,000')
            ->assertSee('32,500')
            ->assertSee('17,500');
    });
});
