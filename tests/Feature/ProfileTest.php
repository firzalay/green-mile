<?php

use App\Models\Activity;
use App\Models\Checkpoint;
use App\Models\CheckpointScan;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

describe('profile access controls', function () {
    it('redirects guest users to login', function () {
        $this->get(route('profile.show'))->assertRedirect(route('login'));
        $this->get(route('profile.edit'))->assertRedirect(route('login'));
        $this->put(route('profile.update'), [])->assertRedirect(route('login'));
        $this->put(route('profile.update-password'), [])->assertRedirect(route('login'));
    });

    it('denies access to non-participants', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);

        $this->actingAs($organizer)->get(route('profile.show'))->assertStatus(403);
        $this->actingAs($organizer)->get(route('profile.edit'))->assertStatus(403);
        $this->actingAs($organizer)->put(route('profile.update'), [])->assertStatus(403);
        $this->actingAs($organizer)->put(route('profile.update-password'), [])->assertStatus(403);
    });

    it('allows access to authenticated participants', function () {
        $participant = User::factory()->create(['role' => 'participant']);

        $this->actingAs($participant)->get(route('profile.show'))->assertOk();
        $this->actingAs($participant)->get(route('profile.edit'))->assertOk();
    });
});

describe('profile statistics, achievements, and activities', function () {
    it('displays user profile data, stats, achievements, and recent activities', function () {
        $user = User::factory()->create([
            'role' => 'participant',
            'name' => 'Fatahillah Firzalay',
            'username' => 'fatahillah',
            'email' => 'fatahillah@gmail.com',
            'created_at' => '2026-06-01 10:00:00',
        ]);

        $event1 = Event::factory()->create(['status' => 'ongoing']);
        $event2 = Event::factory()->create(['status' => 'finished']);

        // Stats: Joined and Completed events
        EventParticipant::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event1->id,
            'completed_checkpoints' => 2,
            'current_event_points' => 100,
            'status' => 'joined',
        ]);

        EventParticipant::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event2->id,
            'completed_checkpoints' => 4,
            'current_event_points' => 200,
            'status' => 'completed',
        ]);

        // Scans
        $checkpoint = Checkpoint::factory()->create(['event_id' => $event1->id]);
        CheckpointScan::create([
            'user_id' => $user->id,
            'event_id' => $event1->id,
            'checkpoint_id' => $checkpoint->id,
            'points_awarded' => 50,
            'scanned_at' => now(),
        ]);

        // Redemptions
        $reward = Reward::factory()->create(['event_id' => $event1->id]);
        RewardRedemption::create([
            'user_id' => $user->id,
            'reward_id' => $reward->id,
            'points_used' => 100,
            'status' => 'pending',
            'redeemed_at' => now(),
        ]);

        // Activities
        Activity::create([
            'user_id' => $user->id,
            'event_id' => $event1->id,
            'activity_type' => 'scan_checkpoint',
            'description' => 'berhasil scan Checkpoint 1',
            'points' => 50,
        ]);

        $response = $this->actingAs($user)->get(route('profile.show'));

        $response->assertOk()
            ->assertSee('Fatahillah Firzalay')
            ->assertSee('@fatahillah')
            ->assertSee('fatahillah@gmail.com')
            ->assertSee('Bergabung sejak '.$user->created_at->translatedFormat('F Y'))
            // Stats checks
            ->assertSee('Total Poin')
            ->assertSee('Event Diikuti')
            ->assertSee('Scan Checkpoint')
            // Logout check
            ->assertSee('Keluar dari Akun')
            ->assertSee('btn-logout-profile');
    });
});

describe('profile editing and avatar uploading', function () {
    it('validates name and username update criteria', function () {
        $user = User::factory()->create([
            'role' => 'participant',
            'name' => 'Fatahillah Firzalay',
            'username' => 'fatahillah',
        ]);

        // Validation: name required, min 3 chars
        $response = $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => 'Fa',
                'username' => 'fatahillah',
            ]);
        $response->assertSessionHasErrors(['name']);

        // Validation: username alphanumeric, min 4 chars
        $response = $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => 'Fatahillah New',
                'username' => 'fat',
            ]);
        $response->assertSessionHasErrors(['username']);

        // Validation: username alphanumeric only
        $response = $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => 'Fatahillah New',
                'username' => 'fata_hillah',
            ]);
        $response->assertSessionHasErrors(['username']);

        // Validation: unique username
        $otherUser = User::factory()->create([
            'username' => 'otheruser',
        ]);

        $response = $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => 'Fatahillah New',
                'username' => 'otheruser',
            ]);
        $response->assertSessionHasErrors(['username']);
    });

    it('updates profile information and processes avatar uploads', function () {
        Storage::fake('public');
        $user = User::factory()->create([
            'role' => 'participant',
            'name' => 'Old Name',
            'username' => 'olduser',
        ]);

        $file = UploadedFile::fake()->image('avatar.png')->size(500);

        $response = $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => 'New Name',
                'username' => 'newuser',
                'avatar' => $file,
            ]);

        $response->assertRedirect(route('profile.show'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'username' => 'newuser',
        ]);

        $user->refresh();
        expect($user->avatar)->toStartWith('/storage/avatars/');
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $user->avatar));
    });

    it('blocks invalid avatar uploads based on mime/size constraints', function () {
        Storage::fake('public');
        $user = User::factory()->create([
            'role' => 'participant',
        ]);

        // File too large (>2MB)
        $largeFile = UploadedFile::fake()->image('avatar.jpg')->size(3000);
        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => 'Valid Name',
                'username' => 'validuser',
                'avatar' => $largeFile,
            ])
            ->assertSessionHasErrors(['avatar']);

        // Wrong file extension
        $pdfFile = UploadedFile::fake()->create('avatar.pdf', 100);
        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => 'Valid Name',
                'username' => 'validuser',
                'avatar' => $pdfFile,
            ])
            ->assertSessionHasErrors(['avatar']);
    });
});

describe('profile password updates', function () {
    it('validates current password, confirm fields, and minimum length requirements', function () {
        $user = User::factory()->create([
            'role' => 'participant',
            'password' => Hash::make('secret123'),
        ]);

        // Wrong current password
        $response = $this->actingAs($user)
            ->put(route('profile.update-password'), [
                'current_password' => 'wrongpass',
                'password' => 'newsecret123',
                'password_confirmation' => 'newsecret123',
            ]);
        $response->assertSessionHasErrors(['current_password']);

        // Password too short (<8 chars)
        $response = $this->actingAs($user)
            ->put(route('profile.update-password'), [
                'current_password' => 'secret123',
                'password' => 'new123',
                'password_confirmation' => 'new123',
            ]);
        $response->assertSessionHasErrors(['password']);

        // Mismatched confirmation
        $response = $this->actingAs($user)
            ->put(route('profile.update-password'), [
                'current_password' => 'secret123',
                'password' => 'newsecret123',
                'password_confirmation' => 'mismatch123',
            ]);
        $response->assertSessionHasErrors(['password']);
    });

    it('successfully updates user password', function () {
        $user = User::factory()->create([
            'role' => 'participant',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->actingAs($user)
            ->put(route('profile.update-password'), [
                'current_password' => 'secret123',
                'password' => 'newsecret123',
                'password_confirmation' => 'newsecret123',
            ]);

        $response->assertRedirect(route('profile.show'));
        $user->refresh();
        expect(Hash::check('newsecret123', $user->password))->toBeTrue();
    });
});
