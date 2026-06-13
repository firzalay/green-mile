<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Login Tests
|--------------------------------------------------------------------------
*/
describe('login page', function () {
    it('renders the login page', function () {
        $this->get(route('login'))
            ->assertOk()
            ->assertViewIs('auth.login');
    });

    it('redirects authenticated users away from login page', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect(route('dashboard'));
    });
});

describe('login form submission', function () {
    it('allows a user to log in with valid credentials', function () {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    });

    it('fails login with invalid password', function () {
        $user = User::factory()->create([
            'password' => bcrypt('correctpassword'),
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ])->assertRedirect()
            ->assertSessionHas('error');

        $this->assertGuest();
    });

    it('validates email is required', function () {
        $this->post(route('login'), [
            'email' => '',
            'password' => 'password123',
        ])->assertSessionHasErrors('email');
    });

    it('validates email format', function () {
        $this->post(route('login'), [
            'email' => 'not-an-email',
            'password' => 'password123',
        ])->assertSessionHasErrors('email');
    });

    it('validates password is required', function () {
        $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => '',
        ])->assertSessionHasErrors('password');
    });
});

/*
|--------------------------------------------------------------------------
| Register Tests
|--------------------------------------------------------------------------
*/
describe('register page', function () {
    it('redirects register root to select role page', function () {
        $this->get(route('register'))
            ->assertRedirect(route('register.select-role'));
    });

    it('redirects authenticated users away from participant register page', function () {
        $user = User::factory()->create(['role' => 'participant', 'status' => 'active']);

        $this->actingAs($user)
            ->get(route('register.participant'))
            ->assertRedirect(route('dashboard'));
    });
});

describe('register form submission', function () {
    it('allows a new participant to register', function () {
        $this->post(route('register.participant'), [
            'name' => 'Budi Santoso',
            'username' => 'budisantoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'Budi Santoso',
            'username' => 'budisantoso',
            'email' => 'budi@example.com',
        ]);
    });

    it('validates name is required', function () {
        $this->post(route('register.participant'), [
            'name' => '',
            'username' => 'budisantoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('name');
    });

    it('validates email must be unique', function () {
        User::factory()->create(['email' => 'existing@example.com']);

        $this->post(route('register.participant'), [
            'name' => 'Budi',
            'username' => 'budisantoso',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('email');
    });

    it('validates password minimum length of 8', function () {
        $this->post(route('register.participant'), [
            'name' => 'Budi',
            'username' => 'budisantoso',
            'email' => 'budi@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');
    });

    it('validates password confirmation matches', function () {
        $this->post(route('register.participant'), [
            'name' => 'Budi',
            'username' => 'budisantoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
        ])->assertSessionHasErrors('password');
    });
});

/*
|--------------------------------------------------------------------------
| Logout Tests
|--------------------------------------------------------------------------
*/
describe('logout', function () {
    it('logs out an authenticated user', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    });
});
