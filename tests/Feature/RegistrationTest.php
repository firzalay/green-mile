<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

describe('registration select role page', function () {
    it('allows guests to view the role selection page', function () {
        $this->get(route('register.select-role'))
            ->assertOk()
            ->assertSee('Pilih Jenis Akun Anda')
            ->assertSee('Daftar sebagai Participant')
            ->assertSee('Daftar sebagai Event Organizer');
    });

    it('redirects authenticated users to their dashboards', function () {
        $participant = User::factory()->create(['role' => 'participant', 'status' => 'active']);
        $organizer = User::factory()->create(['role' => 'organizer', 'status' => 'approved']);

        $this->actingAs($participant)
            ->get(route('register.select-role'))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($organizer)
            ->get(route('register.select-role'))
            ->assertRedirect(route('organizer.dashboard'));
    });
});

describe('participant registration form', function () {
    it('allows guests to view the participant registration form', function () {
        $this->get(route('register.participant'))
            ->assertOk()
            ->assertSee('Registrasi Participant')
            ->assertSee('Nama Lengkap');
    });

    it('validates participant input rules', function () {
        // Required fields
        $this->post(route('register.participant'), [])
            ->assertSessionHasErrors(['name', 'username', 'email', 'password']);

        // Username and Email uniqueness
        User::factory()->create([
            'username' => 'existing_user',
            'email' => 'existing@example.com',
        ]);

        $this->post(route('register.participant'), [
            'name' => 'John Doe',
            'username' => 'existing_user',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors(['username', 'email']);

        // Password min length
        $this->post(route('register.participant'), [
            'name' => 'John Doe',
            'username' => 'newuser',
            'email' => 'new@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');

        // Password confirmation matches
        $this->post(route('register.participant'), [
            'name' => 'John Doe',
            'username' => 'newuser',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different',
        ])->assertSessionHasErrors('password');
    });

    it('creates active participant and automatically logs them in', function () {
        $response = $this->post(route('register.participant'), [
            'name' => 'Budi Santoso',
            'username' => 'budisantoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'name' => 'Budi Santoso',
            'username' => 'budisantoso',
            'email' => 'budi@example.com',
            'role' => 'participant',
            'status' => 'active',
        ]);
    });
});

describe('organizer registration form', function () {
    it('allows guests to view the organizer registration form', function () {
        $this->get(route('register.organizer'))
            ->assertOk()
            ->assertSee('Registrasi Organizer')
            ->assertSee('Nama Organisasi');
    });

    it('validates organizer input rules', function () {
        $this->post(route('register.organizer'), [])
            ->assertSessionHasErrors(['organization_name', 'contact_person', 'username', 'email', 'phone', 'password']);
    });

    it('creates pending organizer user and profile but does not auto login', function () {
        $response = $this->post(route('register.organizer'), [
            'organization_name' => 'Jejak Lestari Org',
            'contact_person' => 'Joko Widodo',
            'username' => 'jokowi',
            'email' => 'jokowi@example.com',
            'phone' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertOk()
            ->assertViewIs('auth.register-organizer-success')
            ->assertSee('Pendaftaran Berhasil')
            ->assertSee('Akun organizer Anda sedang menunggu persetujuan');

        $this->assertGuest();

        $this->assertDatabaseHas('users', [
            'name' => 'Joko Widodo',
            'username' => 'jokowi',
            'email' => 'jokowi@example.com',
            'role' => 'organizer',
            'status' => 'pending',
        ]);

        $user = User::where('username', 'jokowi')->first();
        $this->assertDatabaseHas('organizer_profiles', [
            'user_id' => $user->id,
            'organization_name' => 'Jejak Lestari Org',
            'contact_person' => 'Joko Widodo',
            'phone' => '081234567890',
        ]);
    });
});

describe('organizer status login restrictions', function () {
    it('blocks pending organizers from logging in', function () {
        $pendingOrganizer = User::factory()->create([
            'role' => 'organizer',
            'status' => 'pending',
            'password' => Hash::make('password123'),
        ]);

        $this->post(route('login'), [
            'email' => $pendingOrganizer->email,
            'password' => 'password123',
        ])->assertRedirect()
            ->assertSessionHas('error', 'Akun Anda masih menunggu persetujuan dari Super Admin.');

        $this->assertGuest();
    });

    it('blocks rejected organizers from logging in', function () {
        $rejectedOrganizer = User::factory()->create([
            'role' => 'organizer',
            'status' => 'rejected',
            'password' => Hash::make('password123'),
        ]);

        $this->post(route('login'), [
            'email' => $rejectedOrganizer->email,
            'password' => 'password123',
        ])->assertRedirect()
            ->assertSessionHas('error', 'Pendaftaran organizer Anda belum dapat disetujui. Silakan hubungi tim Jejak Hijau untuk informasi lebih lanjut.');

        $this->assertGuest();
    });

    it('allows approved organizers to log in and redirects them to organizer dashboard', function () {
        $approvedOrganizer = User::factory()->create([
            'role' => 'organizer',
            'status' => 'approved',
            'password' => Hash::make('password123'),
        ]);

        $this->post(route('login'), [
            'email' => $approvedOrganizer->email,
            'password' => 'password123',
        ])->assertRedirect(route('organizer.dashboard'));

        $this->assertAuthenticatedAs($approvedOrganizer);
    });
});

describe('organizer dashboard middleware protection', function () {
    it('denies access to organizer dashboard routes for pending organizers', function () {
        $pendingOrganizer = User::factory()->create([
            'role' => 'organizer',
            'status' => 'pending',
        ]);

        $this->actingAs($pendingOrganizer)
            ->get(route('organizer.dashboard'))
            ->assertStatus(403);
    });

    it('denies access to organizer dashboard routes for rejected organizers', function () {
        $rejectedOrganizer = User::factory()->create([
            'role' => 'organizer',
            'status' => 'rejected',
        ]);

        $this->actingAs($rejectedOrganizer)
            ->get(route('organizer.dashboard'))
            ->assertStatus(403);
    });

    it('allows access to organizer dashboard routes for approved organizers', function () {
        $approvedOrganizer = User::factory()->create([
            'role' => 'organizer',
            'status' => 'approved',
        ]);

        $this->actingAs($approvedOrganizer)
            ->get(route('organizer.dashboard'))
            ->assertOk();
    });
});
