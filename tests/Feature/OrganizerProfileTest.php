<?php

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\OrganizerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

describe('organizer profile access controls', function () {
    it('redirects guest users to login', function () {
        $this->get(route('organizer.profile.show'))->assertRedirect(route('login'));
        $this->get(route('organizer.profile.edit'))->assertRedirect(route('login'));
        $this->put(route('organizer.profile.update'), [])->assertRedirect(route('login'));
        $this->put(route('organizer.profile.update-password'), [])->assertRedirect(route('login'));
    });

    it('denies access to participant role', function () {
        $participant = User::factory()->create(['role' => 'participant']);

        $this->actingAs($participant)->get(route('organizer.profile.show'))->assertStatus(403);
        $this->actingAs($participant)->get(route('organizer.profile.edit'))->assertStatus(403);
        $this->actingAs($participant)->put(route('organizer.profile.update'), [])->assertStatus(403);
        $this->actingAs($participant)->put(route('organizer.profile.update-password'), [])->assertStatus(403);
    });

    it('denies access to unapproved organizers', function () {
        $pendingOrganizer = User::factory()->create([
            'role' => 'organizer',
            'status' => 'pending',
        ]);
        OrganizerProfile::create([
            'user_id' => $pendingOrganizer->id,
            'organization_name' => 'Pending Org',
            'contact_person' => 'Pending Person',
            'phone' => '12345',
        ]);

        $this->actingAs($pendingOrganizer)->get(route('organizer.profile.show'))->assertStatus(403);
        $this->actingAs($pendingOrganizer)->get(route('organizer.profile.edit'))->assertStatus(403);
        $this->actingAs($pendingOrganizer)->put(route('organizer.profile.update'), [])->assertStatus(403);
        $this->actingAs($pendingOrganizer)->put(route('organizer.profile.update-password'), [])->assertStatus(403);
    });

    it('allows access to approved organizers', function () {
        $organizer = User::factory()->create([
            'role' => 'organizer',
            'status' => 'approved',
        ]);
        OrganizerProfile::create([
            'user_id' => $organizer->id,
            'organization_name' => 'Approved Org',
            'contact_person' => 'Approved Person',
            'phone' => '12345',
        ]);

        $this->actingAs($organizer)->get(route('organizer.profile.show'))->assertOk();
        $this->actingAs($organizer)->get(route('organizer.profile.edit'))->assertOk();
    });
});

describe('organizer profile display and statistics', function () {
    it('displays organizer profile details and calculated statistics', function () {
        $organizer = User::factory()->create([
            'role' => 'organizer',
            'name' => 'GreenRun Organizer',
            'username' => 'greenorg',
            'email' => 'greenorg@example.com',
        ]);

        OrganizerProfile::create([
            'user_id' => $organizer->id,
            'organization_name' => 'Eco Lestari Foundation',
            'contact_person' => 'Eco Lestari',
            'phone' => '08987654321',
            'description' => 'We organize green runs.',
            'website' => 'https://ecolestari.example.com',
        ]);

        // Create events
        $event1 = Event::factory()->create([
            'user_id' => $organizer->id,
            'is_active' => true,
        ]);
        $event2 = Event::factory()->create([
            'user_id' => $organizer->id,
            'is_active' => false,
        ]);

        // Setup participants
        $p1 = User::factory()->create(['role' => 'participant']);
        $p2 = User::factory()->create(['role' => 'participant']);

        EventParticipant::create([
            'event_id' => $event1->id,
            'user_id' => $p1->id,
            'completed_checkpoints' => 2,
            'current_event_points' => 100,
        ]);
        EventParticipant::create([
            'event_id' => $event2->id,
            'user_id' => $p2->id,
            'completed_checkpoints' => 1,
            'current_event_points' => 50,
        ]);

        $response = $this->actingAs($organizer)->get(route('organizer.profile.show'));

        $response->assertOk()
            ->assertSee('GreenRun Organizer')
            ->assertSee('@greenorg')
            ->assertSee('greenorg@example.com')
            ->assertSee('Eco Lestari Foundation')
            ->assertSee('Eco Lestari')
            ->assertSee('08987654321')
            ->assertSee('We organize green runs.')
            ->assertSee('https://ecolestari.example.com')
            // Statistics assertions
            ->assertSee('Total Event')
            ->assertSee('2')
            ->assertSee('Event Aktif')
            ->assertSee('1')
            ->assertSee('Total Peserta')
            ->assertSee('2');
    });
});

describe('organizer profile editing and validation', function () {
    it('updates profile and organization info and processes avatar uploads', function () {
        Storage::fake('public');

        $organizer = User::factory()->create([
            'role' => 'organizer',
            'name' => 'Old Name',
            'username' => 'oldorg',
        ]);

        OrganizerProfile::create([
            'user_id' => $organizer->id,
            'organization_name' => 'Old Org',
            'contact_person' => 'Old Contact',
            'phone' => '00000',
        ]);

        $file = UploadedFile::fake()->image('avatar.png')->size(500);

        $response = $this->actingAs($organizer)
            ->put(route('organizer.profile.update'), [
                'name' => 'New Name',
                'username' => 'neworg',
                'avatar' => $file,
                'organization_name' => 'New Org LLC',
                'contact_person' => 'New Contact Person',
                'phone' => '08111222333',
                'description' => 'Updated description.',
                'website' => 'https://neworg.example.com',
            ]);

        $response->assertRedirect(route('organizer.profile.show'));

        $this->assertDatabaseHas('users', [
            'id' => $organizer->id,
            'name' => 'New Name',
            'username' => 'neworg',
        ]);

        $this->assertDatabaseHas('organizer_profiles', [
            'user_id' => $organizer->id,
            'organization_name' => 'New Org LLC',
            'contact_person' => 'New Contact Person',
            'phone' => '08111222333',
            'description' => 'Updated description.',
            'website' => 'https://neworg.example.com',
        ]);

        $organizer->refresh();
        expect($organizer->avatar)->toStartWith('/storage/avatars/');
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $organizer->avatar));
    });

    it('validates input criteria on update', function () {
        $organizer = User::factory()->create([
            'role' => 'organizer',
        ]);
        OrganizerProfile::create([
            'user_id' => $organizer->id,
            'organization_name' => 'Test Org',
            'contact_person' => 'Test Person',
            'phone' => '12345',
        ]);

        // Name too short, invalid username with spaces
        $response = $this->actingAs($organizer)
            ->put(route('organizer.profile.update'), [
                'name' => 'Ab',
                'username' => 'invalid user name',
                'organization_name' => '',
                'contact_person' => '',
                'phone' => '',
            ]);

        $response->assertSessionHasErrors([
            'name',
            'username',
            'organization_name',
            'contact_person',
            'phone',
        ]);
    });
});

describe('organizer password updates', function () {
    it('successfully updates user password', function () {
        $organizer = User::factory()->create([
            'role' => 'organizer',
            'password' => Hash::make('secret123'),
        ]);
        OrganizerProfile::create([
            'user_id' => $organizer->id,
            'organization_name' => 'Test Org',
            'contact_person' => 'Test Person',
            'phone' => '12345',
        ]);

        $response = $this->actingAs($organizer)
            ->put(route('organizer.profile.update-password'), [
                'current_password' => 'secret123',
                'password' => 'newsecret123',
                'password_confirmation' => 'newsecret123',
            ]);

        $response->assertRedirect(route('organizer.profile.show'));
        $organizer->refresh();
        expect(Hash::check('newsecret123', $organizer->password))->toBeTrue();
    });

    it('rejects password updates with invalid current password or confirmation mismatch', function () {
        $organizer = User::factory()->create([
            'role' => 'organizer',
            'password' => Hash::make('secret123'),
        ]);
        OrganizerProfile::create([
            'user_id' => $organizer->id,
            'organization_name' => 'Test Org',
            'contact_person' => 'Test Person',
            'phone' => '12345',
        ]);

        $response = $this->actingAs($organizer)
            ->put(route('organizer.profile.update-password'), [
                'current_password' => 'wrongpass',
                'password' => 'newsecret123',
                'password_confirmation' => 'newsecret123',
            ]);

        $response->assertSessionHasErrors(['current_password']);

        $response = $this->actingAs($organizer)
            ->put(route('organizer.profile.update-password'), [
                'current_password' => 'secret123',
                'password' => 'newsecret123',
                'password_confirmation' => 'mismatch123',
            ]);

        $response->assertSessionHasErrors(['password']);
    });
});
