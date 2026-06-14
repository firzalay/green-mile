<?php

use App\Models\OrganizerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

describe('admin profile access controls', function () {
    it('redirects guest users to login', function () {
        $this->get(route('admin.profile.show'))->assertRedirect(route('login'));
        $this->get(route('admin.profile.edit'))->assertRedirect(route('login'));
        $this->put(route('admin.profile.update'), [])->assertRedirect(route('login'));
        $this->put(route('admin.profile.update-password'), [])->assertRedirect(route('login'));
    });

    it('denies access to participant role', function () {
        $participant = User::factory()->create(['role' => 'participant']);

        $this->actingAs($participant)->get(route('admin.profile.show'))->assertStatus(403);
        $this->actingAs($participant)->get(route('admin.profile.edit'))->assertStatus(403);
        $this->actingAs($participant)->put(route('admin.profile.update'), [])->assertStatus(403);
        $this->actingAs($participant)->put(route('admin.profile.update-password'), [])->assertStatus(403);
    });

    it('denies access to organizer role', function () {
        $organizer = User::factory()->create(['role' => 'organizer', 'status' => 'approved']);

        $this->actingAs($organizer)->get(route('admin.profile.show'))->assertStatus(403);
        $this->actingAs($organizer)->get(route('admin.profile.edit'))->assertStatus(403);
        $this->actingAs($organizer)->put(route('admin.profile.update'), [])->assertStatus(403);
        $this->actingAs($organizer)->put(route('admin.profile.update-password'), [])->assertStatus(403);
    });

    it('allows access to super admin', function () {
        $admin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($admin)->get(route('admin.profile.show'))->assertOk();
        $this->actingAs($admin)->get(route('admin.profile.edit'))->assertOk();
    });
});

describe('admin profile display and statistics', function () {
    it('displays admin profile details and correct organizer review stats', function () {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'name' => 'Main Super Admin',
            'username' => 'mainadmin',
            'email' => 'mainadmin@example.com',
        ]);

        // Create approved and rejected organizers by this admin
        $approved1 = User::factory()->create(['role' => 'organizer', 'status' => 'approved', 'approved_by' => $admin->id]);
        OrganizerProfile::create(['user_id' => $approved1->id, 'organization_name' => 'Org A', 'contact_person' => 'A', 'phone' => '1']);

        $approved2 = User::factory()->create(['role' => 'organizer', 'status' => 'approved', 'approved_by' => $admin->id]);
        OrganizerProfile::create(['user_id' => $approved2->id, 'organization_name' => 'Org B', 'contact_person' => 'B', 'phone' => '2']);

        $rejected = User::factory()->create(['role' => 'organizer', 'status' => 'rejected', 'approved_by' => $admin->id]);
        OrganizerProfile::create(['user_id' => $rejected->id, 'organization_name' => 'Org C', 'contact_person' => 'C', 'phone' => '3']);

        // Organizer approved by another admin (should not be counted in this admin's stats)
        $otherAdmin = User::factory()->create(['role' => 'super_admin']);
        $approvedOther = User::factory()->create(['role' => 'organizer', 'status' => 'approved', 'approved_by' => $otherAdmin->id]);
        OrganizerProfile::create(['user_id' => $approvedOther->id, 'organization_name' => 'Org D', 'contact_person' => 'D', 'phone' => '4']);

        $response = $this->actingAs($admin)->get(route('admin.profile.show'));

        $response->assertOk()
            ->assertSee('Main Super Admin')
            ->assertSee('@mainadmin')
            ->assertSee('mainadmin@example.com')
            // Statistics checks
            ->assertSee('Organizer Disetujui')
            ->assertSee('2')
            ->assertSee('Organizer Ditolak')
            ->assertSee('1')
            ->assertSee('Total Tinjauan')
            ->assertSee('3');
    });
});

describe('admin profile editing and validation', function () {
    it('updates admin profile information and processes avatar uploads', function () {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'super_admin',
            'name' => 'Old Admin Name',
            'username' => 'oldadmin',
        ]);

        $file = UploadedFile::fake()->image('avatar.jpg')->size(500);

        $response = $this->actingAs($admin)
            ->put(route('admin.profile.update'), [
                'name' => 'New Admin Name',
                'username' => 'newadmin',
                'avatar' => $file,
            ]);

        $response->assertRedirect(route('admin.profile.show'));

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'New Admin Name',
            'username' => 'newadmin',
        ]);

        $admin->refresh();
        expect($admin->avatar)->toStartWith('/storage/avatars/');
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $admin->avatar));
    });

    it('validates input criteria on update', function () {
        $admin = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $response = $this->actingAs($admin)
            ->put(route('admin.profile.update'), [
                'name' => 'Ad',
                'username' => 'invalid name with spaces',
            ]);

        $response->assertSessionHasErrors(['name', 'username']);
    });
});

describe('admin password updates', function () {
    it('successfully updates admin password', function () {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->actingAs($admin)
            ->put(route('admin.profile.update-password'), [
                'current_password' => 'secret123',
                'password' => 'newsecret123',
                'password_confirmation' => 'newsecret123',
            ]);

        $response->assertRedirect(route('admin.profile.show'));
        $admin->refresh();
        expect(Hash::check('newsecret123', $admin->password))->toBeTrue();
    });

    it('rejects password updates with invalid current password or mismatch', function () {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->actingAs($admin)
            ->put(route('admin.profile.update-password'), [
                'current_password' => 'wrongpass',
                'password' => 'newsecret123',
                'password_confirmation' => 'newsecret123',
            ]);

        $response->assertSessionHasErrors(['current_password']);
    });
});
