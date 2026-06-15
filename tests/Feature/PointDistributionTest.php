<?php

use App\Actions\Checkpoint\ProcessCheckpointScanAction;
use App\Models\Checkpoint;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('calculates automatic point distribution correctly into checkpoint pools', function () {
    $organizer = User::factory()->create(['role' => 'organizer']);
    $event = Event::factory()->create([
        'organizer_id' => $organizer->id,
        'total_point_pool' => 100,
    ]);

    // Create 3 checkpoints
    $cp1 = Checkpoint::factory()->create(['event_id' => $event->id, 'sequence' => 1]);
    $cp2 = Checkpoint::factory()->create(['event_id' => $event->id, 'sequence' => 2]);
    $cp3 = Checkpoint::factory()->create(['event_id' => $event->id, 'sequence' => 3]);

    expect($cp1->fresh()->point)->toBe(33);
    expect($cp2->fresh()->point)->toBe(33);
    expect($cp3->fresh()->point)->toBe(34);

    // Delete checkpoint 3
    $cp3->delete();

    expect($cp1->fresh()->point)->toBe(50);
    expect($cp2->fresh()->point)->toBe(50);
});

it('enforces event status lock for point configurations and checkpoint actions', function () {
    $organizer = User::factory()->create(['role' => 'organizer']);
    $event = Event::factory()->create([
        'organizer_id' => $organizer->id,
        'total_point_pool' => 100,
        'status' => 'ongoing',
    ]);

    // Try to update event point config
    $payload = [
        'name' => 'New Name',
        'location' => $event->location,
        'start_date' => $event->start_date->format('Y-m-d'),
        'end_date' => $event->end_date->format('Y-m-d'),
        'max_points' => $event->max_points,
        'point_pool' => $event->point_pool,
        'status' => 'ongoing',
        'total_event_point' => 200,
    ];

    $this->actingAs($organizer)
        ->put(route('organizer.events.update', $event->id), $payload)
        ->assertSessionHasErrors(['total_point_pool']);

    // Try to add checkpoint to ongoing event
    $this->actingAs($organizer)
        ->post(route('organizer.events.checkpoints.store', $event->id), [
            'name' => 'CP Fail',
            'sequence' => 1,
            'points' => 50,
            'status' => 'active',
        ])->assertStatus(403);
});

it('calculates tier-based scan points correctly', function () {
    $organizer = User::factory()->create(['role' => 'organizer']);

    // Create ongoing event with 50,000 point pool
    $event = Event::factory()->create([
        'organizer_id' => $organizer->id,
        'total_point_pool' => 50000,
        'status' => 'draft',
    ]);

    // Create 2 checkpoints to divide total points pool: 25,000 pts per checkpoint
    $checkpoint = Checkpoint::factory()->create([
        'event_id' => $event->id,
        'sequence' => 1,
        'qr_token' => 'token_tier_test',
    ]);

    // Create second checkpoint to divide pool
    Checkpoint::factory()->create([
        'event_id' => $event->id,
        'sequence' => 2,
    ]);

    // Add bonus tiers to Checkpoint 1
    $checkpoint->bonusTiers()->create([
        'rank_start' => 1,
        'rank_end' => 2,
        'bonus_percentage' => 10.0, // 10% of 25,000 = 2,500 pts bonus
    ]);

    $checkpoint->bonusTiers()->create([
        'rank_start' => 3,
        'rank_end' => null,
        'bonus_percentage' => 5.0, // 5% of 25,000 = 1,250 pts bonus
    ]);

    $event->update(['status' => 'ongoing']);

    // Setup 3 participants
    $p1 = User::factory()->create(['role' => 'participant']);
    $p2 = User::factory()->create(['role' => 'participant']);
    $p3 = User::factory()->create(['role' => 'participant']);

    EventParticipant::create([
        'event_id' => $event->id,
        'user_id' => $p1->id,
        'completed_checkpoints' => 0,
        'current_event_points' => 0,
        'total_points' => 0,
        'status' => 'joined',
    ]);
    EventParticipant::create([
        'event_id' => $event->id,
        'user_id' => $p2->id,
        'completed_checkpoints' => 0,
        'current_event_points' => 0,
        'total_points' => 0,
        'status' => 'joined',
    ]);
    EventParticipant::create([
        'event_id' => $event->id,
        'user_id' => $p3->id,
        'completed_checkpoints' => 0,
        'current_event_points' => 0,
        'total_points' => 0,
        'status' => 'joined',
    ]);

    $action = app(ProcessCheckpointScanAction::class);

    // Scan 1: Rank 1 -> tier 10% of 25,000 = 2,500 pts (directly, not base+bonus)
    $res1 = $action->execute($p1, 'token_tier_test');
    expect($res1['points_awarded'])->toBe(2500);
    $this->assertDatabaseHas('checkpoint_scans', [
        'user_id' => $p1->id,
        'checkpoint_id' => $checkpoint->id,
        'total_point' => 2500,
    ]);

    // Scan 2: Rank 2 -> tier 10% of 25,000 = 2,500 pts
    $res2 = $action->execute($p2, 'token_tier_test');
    expect($res2['points_awarded'])->toBe(2500);
    $this->assertDatabaseHas('checkpoint_scans', [
        'user_id' => $p2->id,
        'checkpoint_id' => $checkpoint->id,
        'total_point' => 2500,
    ]);

    // Scan 3: Rank 3 -> tier 5% of 25,000 = 1,250 pts
    $res3 = $action->execute($p3, 'token_tier_test');
    expect($res3['points_awarded'])->toBe(1250);
    $this->assertDatabaseHas('checkpoint_scans', [
        'user_id' => $p3->id,
        'checkpoint_id' => $checkpoint->id,
        'total_point' => 1250,
    ]);
});
