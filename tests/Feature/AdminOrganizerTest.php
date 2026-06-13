<?php

use App\Models\OrganizerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('super admin authorization', function () {
    it('redirects unauthenticated users to login page', function () {
        $this->get(route('admin.organizers.index'))
            ->assertRedirect(route('login'));
    });

    it('returns 403 forbidden for participant role users', function () {
        $user = User::factory()->create(['role' => 'participant']);

        $this->actingAs($user)
            ->get(route('admin.organizers.index'))
            ->assertStatus(403);
    });

    it('returns 403 forbidden for organizer role users', function () {
        $user = User::factory()->create(['role' => 'organizer', 'status' => 'approved']);

        $this->actingAs($user)
            ->get(route('admin.organizers.index'))
            ->assertStatus(403);
    });

    it('allows super admin to access the dashboard', function () {
        $user = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($user)
            ->get(route('admin.organizers.index'))
            ->assertOk()
            ->assertViewIs('admin.organizers.index');
    });
});

describe('super admin organizer dashboard features', function () {
    beforeEach(function () {
        $this->admin = User::factory()->create(['role' => 'super_admin']);

        // Create 2 approved organizers
        $approved1 = User::factory()->create(['role' => 'organizer', 'status' => 'approved', 'approved_by' => $this->admin->id, 'approved_at' => now()]);
        OrganizerProfile::create(['user_id' => $approved1->id, 'organization_name' => 'Org Approved 1', 'contact_person' => 'Contact 1', 'phone' => '12345']);

        $approved2 = User::factory()->create(['role' => 'organizer', 'status' => 'approved', 'approved_by' => $this->admin->id, 'approved_at' => now()]);
        OrganizerProfile::create(['user_id' => $approved2->id, 'organization_name' => 'Org Approved 2', 'contact_person' => 'Contact 2', 'phone' => '12345']);

        // Create 1 pending organizer
        $this->pending = User::factory()->create(['role' => 'organizer', 'status' => 'pending']);
        OrganizerProfile::create(['user_id' => $this->pending->id, 'organization_name' => 'Org Pending 1', 'contact_person' => 'Contact P', 'phone' => '54321']);

        // Create 1 rejected organizer
        $rejected = User::factory()->create(['role' => 'organizer', 'status' => 'rejected', 'approved_by' => $this->admin->id, 'approved_at' => now()]);
        OrganizerProfile::create(['user_id' => $rejected->id, 'organization_name' => 'Org Rejected 1', 'contact_person' => 'Contact R', 'phone' => '99999']);
    });

    it('displays the correct statistics cards', function () {
        $response = $this->actingAs($this->admin)->get(route('admin.organizers.index'));

        $response->assertOk()
            ->assertSee('4 Organizer') // Total: 2 approved + 1 pending + 1 rejected = 4
            ->assertSee('1 Pending')
            ->assertSee('2 Approved')
            ->assertSee('1 Rejected');
    });

    it('lists the pending organizers in the pending table', function () {
        $response = $this->actingAs($this->admin)->get(route('admin.organizers.index'));

        $response->assertOk()
            ->assertSee('Org Pending 1')
            ->assertSee('Contact P')
            ->assertSee($this->pending->email)
            ->assertSee('54321');
    });

    it('lists the approved and rejected organizers in the history table', function () {
        $response = $this->actingAs($this->admin)->get(route('admin.organizers.index'));

        $response->assertOk()
            ->assertSee('Org Approved 1')
            ->assertSee('Org Approved 2')
            ->assertSee('Org Rejected 1');
    });

    it('allows super admin to view organizer detail page', function () {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.organizers.show', $this->pending->id));

        $response->assertOk()
            ->assertViewIs('admin.organizers.show')
            ->assertSee('Org Pending 1')
            ->assertSee('Contact P')
            ->assertSee('54321')
            ->assertSee($this->pending->username);
    });
});

describe('organizer verification actions', function () {
    beforeEach(function () {
        $this->admin = User::factory()->create(['role' => 'super_admin']);
        $this->pending = User::factory()->create(['role' => 'organizer', 'status' => 'pending']);
        OrganizerProfile::create(['user_id' => $this->pending->id, 'organization_name' => 'Lestari Eo Group', 'contact_person' => 'Ahmad', 'phone' => '0812']);
    });

    it('allows super admin to approve a pending organizer', function () {
        // Assert cannot access dashboard before approval
        $this->actingAs($this->pending)
            ->get(route('organizer.dashboard'))
            ->assertStatus(403);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.organizers.approve', $this->pending->id));

        $response->assertRedirect(route('admin.organizers.index'))
            ->assertSessionHas('success', 'Organizer Lestari Eo Group berhasil disetujui.');

        // Assert database state changes
        $this->assertDatabaseHas('users', [
            'id' => $this->pending->id,
            'status' => 'approved',
            'approved_by' => $this->admin->id,
        ]);

        $organizer = User::find($this->pending->id);
        expect($organizer->approved_at)->not->toBeNull();

        $this->pending->refresh();

        // Assert organizer can now access the dashboard
        $this->actingAs($this->pending)
            ->get(route('organizer.dashboard'))
            ->assertOk();
    });

    it('allows super admin to reject a pending organizer', function () {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.organizers.reject', $this->pending->id));

        $response->assertRedirect(route('admin.organizers.index'))
            ->assertSessionHas('success', 'Pendaftaran organizer Lestari Eo Group telah ditolak.');

        // Assert database state changes
        $this->assertDatabaseHas('users', [
            'id' => $this->pending->id,
            'status' => 'rejected',
            'approved_by' => $this->admin->id,
        ]);

        $organizer = User::find($this->pending->id);
        expect($organizer->approved_at)->not->toBeNull();

        $this->pending->refresh();

        // Assert organizer still cannot access dashboard
        $this->actingAs($this->pending)
            ->get(route('organizer.dashboard'))
            ->assertStatus(403);
    });
});
