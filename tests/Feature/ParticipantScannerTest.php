<?php

use App\Models\Activity;
use App\Models\Checkpoint;
use App\Models\CheckpointScan;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

describe('scanner page access control', function () {
    it('redirects guest users to login', function () {
        $this->get(route('scanner.index'))->assertRedirect(route('login'));
        $this->post(route('scanner.scan'), ['qr_token' => 'some-token'])->assertRedirect(route('login'));
    });

    it('denies access to organizer role users', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);

        $this->actingAs($organizer)->get(route('scanner.index'))->assertStatus(403);
        $this->actingAs($organizer)->postJson(route('scanner.scan'), ['qr_token' => 'some-token'])->assertStatus(403);
    });

    it('allows access to participant role users', function () {
        $participant = User::factory()->create(['role' => 'participant']);

        $this->actingAs($participant)->get(route('scanner.index'))->assertOk();
    });
});

describe('qr scan processing', function () {
    it('requires a qr_token parameter', function () {
        $participant = User::factory()->create(['role' => 'participant']);

        $this->actingAs($participant)
            ->postJson(route('scanner.scan'), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors('qr_token');
    });

    it('rejects invalid qr token', function () {
        $participant = User::factory()->create(['role' => 'participant']);

        $this->actingAs($participant)
            ->postJson(route('scanner.scan'), ['qr_token' => 'non-existent-uuid'])
            ->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'QR Code tidak valid.',
            ]);
    });

    it('rejects inactive checkpoints', function () {
        $participant = User::factory()->create(['role' => 'participant']);
        $event = Event::factory()->create(['status' => 'ongoing']);
        $checkpoint = Checkpoint::factory()->create([
            'event_id' => $event->id,
            'status' => 'inactive',
            'qr_token' => (string) Str::uuid(),
        ]);

        $this->actingAs($participant)
            ->postJson(route('scanner.scan'), ['qr_token' => $checkpoint->qr_token])
            ->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'Checkpoint tidak aktif.',
            ]);
    });

    it('rejects checkpoints of events that are not ongoing', function () {
        $participant = User::factory()->create(['role' => 'participant']);

        // Draft event
        $draftEvent = Event::factory()->create(['status' => 'draft']);
        $checkpointDraft = Checkpoint::factory()->create([
            'event_id' => $draftEvent->id,
            'status' => 'active',
            'qr_token' => (string) Str::uuid(),
        ]);

        $this->actingAs($participant)
            ->postJson(route('scanner.scan'), ['qr_token' => $checkpointDraft->qr_token])
            ->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'Event tidak sedang berlangsung.',
            ]);

        // Finished event
        $finishedEvent = Event::factory()->create(['status' => 'finished']);
        $checkpointFinished = Checkpoint::factory()->create([
            'event_id' => $finishedEvent->id,
            'status' => 'active',
            'qr_token' => (string) Str::uuid(),
        ]);

        $this->actingAs($participant)
            ->postJson(route('scanner.scan'), ['qr_token' => $checkpointFinished->qr_token])
            ->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'Event tidak sedang berlangsung.',
            ]);
    });

    it('rejects scans if participant is not registered to the event', function () {
        $participant = User::factory()->create(['role' => 'participant']);
        $event = Event::factory()->create(['status' => 'ongoing']);
        $checkpoint = Checkpoint::factory()->create([
            'event_id' => $event->id,
            'status' => 'active',
            'qr_token' => (string) Str::uuid(),
        ]);

        $this->actingAs($participant)
            ->postJson(route('scanner.scan'), ['qr_token' => $checkpoint->qr_token])
            ->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'Anda belum terdaftar pada event ini.',
            ]);
    });

    it('rejects duplicate checkpoint scans', function () {
        $participant = User::factory()->create(['role' => 'participant']);
        $event = Event::factory()->create(['status' => 'ongoing']);
        $checkpoint = Checkpoint::factory()->create([
            'event_id' => $event->id,
            'status' => 'active',
            'qr_token' => (string) Str::uuid(),
        ]);

        EventParticipant::factory()->create([
            'user_id' => $participant->id,
            'event_id' => $event->id,
            'completed_checkpoints' => 0,
            'current_event_points' => 0,
            'total_points' => 0,
        ]);

        // First scan
        CheckpointScan::create([
            'user_id' => $participant->id,
            'event_id' => $event->id,
            'checkpoint_id' => $checkpoint->id,
            'points_awarded' => $checkpoint->points,
            'scanned_at' => now(),
        ]);

        // Try scanning again
        $this->actingAs($participant)
            ->postJson(route('scanner.scan'), ['qr_token' => $checkpoint->qr_token])
            ->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'Checkpoint ini sudah pernah Anda scan.',
            ]);
    });

    it('rejects scans if participant points will exceed the event max points limit', function () {
        $participant = User::factory()->create(['role' => 'participant']);
        $event = Event::factory()->create([
            'status' => 'ongoing',
            'max_points' => 100,
        ]);
        $checkpoint = Checkpoint::factory()->create([
            'event_id' => $event->id,
            'status' => 'active',
            'points' => 50,
            'qr_token' => (string) Str::uuid(),
        ]);

        EventParticipant::factory()->create([
            'user_id' => $participant->id,
            'event_id' => $event->id,
            'completed_checkpoints' => 1,
            'current_event_points' => 60,
            'total_points' => 100,
        ]);

        $this->actingAs($participant)
            ->postJson(route('scanner.scan'), ['qr_token' => $checkpoint->qr_token])
            ->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'Batas maksimal poin per peserta untuk event ini adalah 100 poin.',
            ]);
    });

    it('successfully scans QR, awards points, updates participant stats, and logs activity', function () {
        $participant = User::factory()->create(['role' => 'participant']);
        $event = Event::factory()->create(['status' => 'ongoing']);
        $checkpoint = Checkpoint::factory()->create([
            'event_id' => $event->id,
            'status' => 'active',
            'points' => 50,
            'qr_token' => (string) Str::uuid(),
            'name' => 'Pos Eco 1',
        ]);

        $ep = EventParticipant::factory()->create([
            'user_id' => $participant->id,
            'event_id' => $event->id,
            'completed_checkpoints' => 1,
            'current_event_points' => 10,
            'total_points' => 100,
        ]);

        $this->actingAs($participant)
            ->postJson(route('scanner.scan'), ['qr_token' => $checkpoint->qr_token])
            ->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => 'Checkpoint berhasil discan!',
                'checkpoint_name' => 'Pos Eco 1',
                'points_awarded' => 50,
                'total_points' => 60,
            ]);

        // 1. Verify scan history is written
        $this->assertDatabaseHas('checkpoint_scans', [
            'user_id' => $participant->id,
            'event_id' => $event->id,
            'checkpoint_id' => $checkpoint->id,
            'points_awarded' => 50,
        ]);

        // 2. Verify participant points & counts incremented
        $this->assertDatabaseHas('event_participants', [
            'user_id' => $participant->id,
            'event_id' => $event->id,
            'completed_checkpoints' => 2,
            'current_event_points' => 60,
            'total_points' => 150,
        ]);

        // 3. Verify activity feed log is created
        $this->assertDatabaseHas('activities', [
            'user_id' => $participant->id,
            'event_id' => $event->id,
            'activity_type' => 'scan_checkpoint',
            'description' => 'berhasil scan Pos Eco 1',
            'points' => 50,
        ]);
    });
});
