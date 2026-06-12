<?php

use App\Models\Checkpoint;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('authorization rules', function () {
    it('redirects guests to login', function () {
        $event = Event::factory()->create();
        $checkpoint = Checkpoint::factory()->create(['event_id' => $event->id]);

        $this->get(route('organizer.events.checkpoints.index', $event->id))->assertRedirect(route('login'));
        $this->get(route('organizer.events.checkpoints.create', $event->id))->assertRedirect(route('login'));
        $this->post(route('organizer.events.checkpoints.store', $event->id), [])->assertRedirect(route('login'));
        $this->get(route('organizer.checkpoints.show', $checkpoint->id))->assertRedirect(route('login'));
        $this->get(route('organizer.checkpoints.edit', $checkpoint->id))->assertRedirect(route('login'));
        $this->put(route('organizer.checkpoints.update', $checkpoint->id), [])->assertRedirect(route('login'));
        $this->delete(route('organizer.checkpoints.destroy', $checkpoint->id))->assertRedirect(route('login'));
    });

    it('returns 403 forbidden for participants', function () {
        $user = User::factory()->create(['role' => 'participant']);
        $event = Event::factory()->create();
        $checkpoint = Checkpoint::factory()->create(['event_id' => $event->id]);

        $this->actingAs($user)->get(route('organizer.events.checkpoints.index', $event->id))->assertStatus(403);
        $this->actingAs($user)->get(route('organizer.events.checkpoints.create', $event->id))->assertStatus(403);
        $this->actingAs($user)->post(route('organizer.events.checkpoints.store', $event->id), [])->assertStatus(403);
        $this->actingAs($user)->get(route('organizer.checkpoints.show', $checkpoint->id))->assertStatus(403);
        $this->actingAs($user)->get(route('organizer.checkpoints.edit', $checkpoint->id))->assertStatus(403);
        $this->actingAs($user)->put(route('organizer.checkpoints.update', $checkpoint->id), [])->assertStatus(403);
        $this->actingAs($user)->delete(route('organizer.checkpoints.destroy', $checkpoint->id))->assertStatus(403);
    });

    it('prevents organizers from accessing other organizers checkpoints', function () {
        $organizer1 = User::factory()->create(['role' => 'organizer']);
        $organizer2 = User::factory()->create(['role' => 'organizer']);

        $event = Event::factory()->create(['organizer_id' => $organizer1->id]);
        $checkpoint = Checkpoint::factory()->create(['event_id' => $event->id]);

        // Access other organizer's checkpoints index/create/store
        $this->actingAs($organizer2)->get(route('organizer.events.checkpoints.index', $event->id))->assertStatus(403);
        $this->actingAs($organizer2)->get(route('organizer.events.checkpoints.create', $event->id))->assertStatus(403);
        $this->actingAs($organizer2)->post(route('organizer.events.checkpoints.store', $event->id), [])->assertStatus(403);

        // Access other organizer's checkpoint flat paths
        $this->actingAs($organizer2)->get(route('organizer.checkpoints.show', $checkpoint->id))->assertStatus(403);
        $this->actingAs($organizer2)->get(route('organizer.checkpoints.edit', $checkpoint->id))->assertStatus(403);
        $this->actingAs($organizer2)->put(route('organizer.checkpoints.update', $checkpoint->id), [])->assertStatus(403);
        $this->actingAs($organizer2)->delete(route('organizer.checkpoints.destroy', $checkpoint->id))->assertStatus(403);
    });
});

describe('organizer checkpoint management crud operations', function () {
    it('allows organizer to list checkpoints of their event in order of sequence', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $checkpoint2 = Checkpoint::factory()->create([
            'event_id' => $event->id,
            'name' => 'Second Checkpoint',
            'sequence' => 2,
        ]);
        $checkpoint1 = Checkpoint::factory()->create([
            'event_id' => $event->id,
            'name' => 'First Checkpoint',
            'sequence' => 1,
        ]);

        $response = $this->actingAs($organizer)->get(route('organizer.events.checkpoints.index', $event->id));

        $response->assertOk()
            ->assertSee('First Checkpoint')
            ->assertSee('Second Checkpoint');

        // Let's assert the ordering in sequence in view
        $data = $response->viewData('checkpoints');
        expect($data->first()->id)->toBe($checkpoint1->id);
        expect($data->last()->id)->toBe($checkpoint2->id);
    });

    it('allows organizer to render create checkpoint page with next sequence number', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        Checkpoint::factory()->create([
            'event_id' => $event->id,
            'sequence' => 3,
        ]);

        $response = $this->actingAs($organizer)->get(route('organizer.events.checkpoints.create', $event->id));

        $response->assertOk();
        expect($response->viewData('nextSequence'))->toBe(4);
    });

    it('validates attributes and creates checkpoint successfully', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $payload = [
            'name' => 'New Checkpoint Point',
            'location' => 'Gate 3 coordinates',
            'description' => 'Verify bib numbers here.',
            'sequence' => 1,
            'points' => 100,
            'status' => 'active',
        ];

        $response = $this->actingAs($organizer)->post(route('organizer.events.checkpoints.store', $event->id), $payload);

        $response->assertRedirect(route('organizer.events.checkpoints.index', $event->id));

        $this->assertDatabaseHas('checkpoints', [
            'event_id' => $event->id,
            'name' => 'New Checkpoint Point',
            'location' => 'Gate 3 coordinates',
            'description' => 'Verify bib numbers here.',
            'sequence' => 1,
            'points' => 100,
            'status' => 'active',
        ]);
    });

    it('allows organizer to view checkpoint details', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $checkpoint = Checkpoint::factory()->create([
            'event_id' => $event->id,
            'name' => 'Checkpoint Alpha',
            'description' => 'Main checkpoint explanation',
        ]);

        $response = $this->actingAs($organizer)->get(route('organizer.checkpoints.show', $checkpoint->id));

        $response->assertOk()
            ->assertSee('Checkpoint Alpha')
            ->assertSee('Main checkpoint explanation');
    });

    it('allows organizer to render edit checkpoint page', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $checkpoint = Checkpoint::factory()->create(['event_id' => $event->id]);

        $response = $this->actingAs($organizer)->get(route('organizer.checkpoints.edit', $checkpoint->id));

        $response->assertOk();
    });

    it('validates attributes and updates checkpoint successfully', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $checkpoint = Checkpoint::factory()->create([
            'event_id' => $event->id,
            'name' => 'Old Name',
            'status' => 'active',
        ]);

        $payload = [
            'name' => 'Updated Name',
            'location' => 'Updated Location',
            'description' => 'Updated Description',
            'sequence' => 5,
            'points' => 150,
            'status' => 'inactive',
        ];

        $response = $this->actingAs($organizer)->put(route('organizer.checkpoints.update', $checkpoint->id), $payload);

        $response->assertRedirect(route('organizer.checkpoints.show', $checkpoint->id));

        $this->assertDatabaseHas('checkpoints', [
            'id' => $checkpoint->id,
            'name' => 'Updated Name',
            'location' => 'Updated Location',
            'description' => 'Updated Description',
            'sequence' => 5,
            'points' => 150,
            'status' => 'inactive',
        ]);
    });

    it('allows organizer to delete a checkpoint', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $checkpoint = Checkpoint::factory()->create(['event_id' => $event->id]);

        $response = $this->actingAs($organizer)->delete(route('organizer.checkpoints.destroy', $checkpoint->id));

        $response->assertRedirect(route('organizer.events.checkpoints.index', $event->id));

        $this->assertDatabaseMissing('checkpoints', [
            'id' => $checkpoint->id,
        ]);
    });
});
