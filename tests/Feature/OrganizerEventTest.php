<?php

use App\Models\Checkpoint;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

describe('authorization rules', function () {
    it('redirects guests to login', function () {
        $this->get(route('organizer.events.index'))->assertRedirect(route('login'));
        $this->get(route('organizer.events.create'))->assertRedirect(route('login'));
        $this->post(route('organizer.events.store'), [])->assertRedirect(route('login'));
    });

    it('returns 403 forbidden for participants', function () {
        $user = User::factory()->create(['role' => 'participant']);
        $this->actingAs($user)->get(route('organizer.events.index'))->assertStatus(403);
        $this->actingAs($user)->get(route('organizer.events.create'))->assertStatus(403);
        $this->actingAs($user)->post(route('organizer.events.store'), [])->assertStatus(403);
    });

    it('prevents organizers from accessing other organizers events', function () {
        $organizer1 = User::factory()->create(['role' => 'organizer']);
        $organizer2 = User::factory()->create(['role' => 'organizer']);

        $event = Event::factory()->create(['organizer_id' => $organizer1->id]);

        $this->actingAs($organizer2)->get(route('organizer.events.show', $event->id))->assertStatus(403);
        $this->actingAs($organizer2)->get(route('organizer.events.edit', $event->id))->assertStatus(403);
        $this->actingAs($organizer2)->put(route('organizer.events.update', $event->id), [])->assertStatus(403);
        $this->actingAs($organizer2)->delete(route('organizer.events.destroy', $event->id))->assertStatus(403);
    });
});

describe('organizer event management crud operations', function () {
    it('allows organizer to list their events', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $otherOrganizer = User::factory()->create(['role' => 'organizer']);

        $event1 = Event::factory()->create([
            'organizer_id' => $organizer->id,
            'name' => 'Event Organizer A',
        ]);
        $event2 = Event::factory()->create([
            'organizer_id' => $otherOrganizer->id,
            'name' => 'Event Organizer B',
        ]);

        $response = $this->actingAs($organizer)->get(route('organizer.events.index'));

        $response->assertOk()
            ->assertSee('Event Organizer A')
            ->assertDontSee('Event Organizer B');
    });

    it('allows organizer to render create event page', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $this->actingAs($organizer)->get(route('organizer.events.create'))->assertOk();
    });

    it('validates event attributes and creates it in draft state', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        Storage::fake('public');

        $payload = [
            'name' => 'New Eco Marathon',
            'location' => 'Surabaya City Hall',
            'start_date' => now()->addDays(5)->format('Y-m-d'),
            'end_date' => now()->addDays(6)->format('Y-m-d'),
            'banner' => UploadedFile::fake()->image('banner.jpg'),
            'description' => 'Eco-marathon description here.',
            'total_rewards' => 'Rp 5.000.000',
            'max_points' => 300,
            'max_participants' => 100,
            'point_pool' => 50000,
        ];

        $response = $this->actingAs($organizer)->post(route('organizer.events.store'), $payload);

        $response->assertRedirect(route('organizer.events.index'));

        $this->assertDatabaseHas('events', [
            'name' => 'New Eco Marathon',
            'location' => 'Surabaya City Hall',
            'organizer_id' => $organizer->id,
            'status' => 'draft',
            'total_point_pool' => 50000,
        ]);

        $event = Event::where('name', 'New Eco Marathon')->first();
        expect($event->getRawOriginal('banner'))->not->toBeNull();
        Storage::disk('public')->assertExists($event->getRawOriginal('banner'));
    });

    it('allows organizer to show event details', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $response = $this->actingAs($organizer)->get(route('organizer.events.show', $event->id));

        $response->assertOk()
            ->assertSee($event->name)
            ->assertSee($event->location);
    });

    it('allows organizer to edit and update event details and status', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['organizer_id' => $organizer->id, 'status' => 'draft']);

        // Create a checkpoint so that the validation 'SUM(checkpoint_points) == total_point_pool' passes when publishing
        Checkpoint::factory()->create([
            'event_id' => $event->id,
            'points' => $event->total_point_pool,
        ]);

        Storage::fake('public');

        $updatePayload = [
            'name' => 'Updated Eco Marathon',
            'location' => 'Jakarta City Hall',
            'start_date' => now()->addDays(10)->format('Y-m-d'),
            'end_date' => now()->addDays(11)->format('Y-m-d'),
            'banner' => UploadedFile::fake()->image('banner-updated.jpg'),
            'description' => 'Updated description here.',
            'total_rewards' => 'Rp 10.000.000',
            'max_points' => 400,
            'max_participants' => 150,
            'status' => 'published',
            'point_pool' => 60000,
        ];

        $response = $this->actingAs($organizer)->put(route('organizer.events.update', $event->id), $updatePayload);

        $response->assertRedirect(route('organizer.events.show', $event->id));

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'name' => 'Updated Eco Marathon',
            'location' => 'Jakarta City Hall',
            'status' => 'published',
            'total_point_pool' => 60000,
        ]);

        $event->refresh();
        expect($event->getRawOriginal('banner'))->not->toBeNull();
        Storage::disk('public')->assertExists($event->getRawOriginal('banner'));
    });

    it('allows organizer to delete event', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $response = $this->actingAs($organizer)->delete(route('organizer.events.destroy', $event->id));

        $response->assertRedirect(route('organizer.events.index'));

        $this->assertDatabaseMissing('events', [
            'id' => $event->id,
        ]);
    });
});
