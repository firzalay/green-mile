<?php

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('participant join event via code form', function () {
    it('redirects unauthenticated users to login', function () {
        $this->get(route('events.join'))
            ->assertRedirect(route('login'));
    });

    it('allows authenticated users to view the join code entry form', function () {
        $user = User::factory()->create(['role' => 'participant']);

        $this->actingAs($user)
            ->get(route('events.join'))
            ->assertOk()
            ->assertViewIs('events.join')
            ->assertSee('Gabung Event Baru');
    });

    it('successfully joins an active event with a valid code', function () {
        $user = User::factory()->create(['role' => 'participant']);
        $event = Event::factory()->create([
            'is_active' => true,
            'status' => 'published',
            'join_code' => 'XYZ999',
        ]);

        $this->actingAs($user)
            ->post(route('events.join.submit'), [
                'join_code' => 'xyz999', // Test case insensitivity / standardization
            ])
            ->assertRedirect(route('events.show', $event->id))
            ->assertSessionHas('success', "Berhasil bergabung ke {$event->name}");

        $this->assertDatabaseHas('event_participants', [
            'event_id' => $event->id,
            'user_id' => $user->id,
        ]);
    });

    it('fails to join when the code does not exist', function () {
        $user = User::factory()->create(['role' => 'participant']);

        $this->actingAs($user)
            ->post(route('events.join.submit'), [
                'join_code' => 'INVALID123',
            ])
            ->assertRedirect()
            ->assertSessionHas('error', 'Kode event tidak ditemukan.');
    });

    it('fails to join when the event has finished or is inactive', function () {
        $user = User::factory()->create(['role' => 'participant']);
        $finishedEvent = Event::factory()->finished()->create([
            'join_code' => 'FINISHED7',
            'is_active' => true,
        ]);

        $inactiveEvent = Event::factory()->create([
            'is_active' => false,
            'join_code' => 'INACTIVE8',
        ]);

        // Test finished event
        $this->actingAs($user)
            ->post(route('events.join.submit'), [
                'join_code' => 'FINISHED7',
            ])
            ->assertRedirect()
            ->assertSessionHas('error', 'Event sudah berakhir dan tidak menerima peserta baru.');

        // Test inactive event
        $this->actingAs($user)
            ->post(route('events.join.submit'), [
                'join_code' => 'INACTIVE8',
            ])
            ->assertRedirect()
            ->assertSessionHas('error', 'Event sudah berakhir dan tidak menerima peserta baru.');
    });

    it('fails to join if the user is already registered for the event', function () {
        $user = User::factory()->create(['role' => 'participant']);
        $event = Event::factory()->create([
            'is_active' => true,
            'status' => 'ongoing',
            'join_code' => 'JOINED123',
        ]);

        EventParticipant::factory()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->post(route('events.join.submit'), [
                'join_code' => 'JOINED123',
            ])
            ->assertRedirect()
            ->assertSessionHas('error', 'Anda sudah terdaftar pada event ini.');
    });
});

describe('organizer join code management', function () {
    it('allows organizer to regenerate event join code', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create([
            'organizer_id' => $organizer->id,
            'join_code' => 'OLDCODE123',
        ]);

        $this->actingAs($organizer)
            ->post(route('organizer.events.regenerate-code', $event->id))
            ->assertRedirect(route('organizer.events.show', $event->id))
            ->assertSessionHas('success', 'Kode akses event berhasil diperbarui.');

        $event->refresh();
        expect($event->join_code)->not->toBe('OLDCODE123')
            ->and(strlen($event->join_code))->toBe(8);
    });

    it('blocks unauthorized users from regenerating event join code', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $otherOrganizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create([
            'organizer_id' => $organizer->id,
            'join_code' => 'SECURE456',
        ]);

        // Attempting to regenerate as a different organizer
        $this->actingAs($otherOrganizer)
            ->post(route('organizer.events.regenerate-code', $event->id))
            ->assertStatus(403);

        // Attempting to regenerate as a participant
        $participant = User::factory()->create(['role' => 'participant']);
        $this->actingAs($participant)
            ->post(route('organizer.events.regenerate-code', $event->id))
            ->assertStatus(403);

        $event->refresh();
        expect($event->join_code)->toBe('SECURE456');
    });
});
