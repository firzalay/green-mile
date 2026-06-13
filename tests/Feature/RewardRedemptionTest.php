<?php

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('reward page access control', function () {
    it('redirects unauthenticated users to login', function () {
        $this->get(route('rewards.index'))
            ->assertRedirect(route('login'));
    });

    it('denies access to non-participants for catalog and detail view', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $reward = Reward::factory()->create(['is_active' => true]);

        $this->actingAs($organizer)
            ->get(route('rewards.index'))
            ->assertStatus(403);

        $this->actingAs($organizer)
            ->get(route('rewards.show', $reward->id))
            ->assertStatus(403);
    });
});

describe('reward catalog and detail view', function () {
    it('displays the empty state when no rewards are available', function () {
        $user = User::factory()->create(['role' => 'participant']);

        $this->actingAs($user)
            ->get(route('rewards.index'))
            ->assertOk()
            ->assertSee('Belum Ada Reward');
    });

    it('displays available rewards and user point balance', function () {
        $user = User::factory()->create(['role' => 'participant']);
        $reward = Reward::factory()->create([
            'name' => 'Tumbler GreenRun',
            'required_points' => 500,
            'stock' => 10,
            'is_active' => true,
        ]);

        // Add points to user
        $event = Event::factory()->create();
        EventParticipant::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'current_event_points' => 1250,
        ]);

        $this->actingAs($user)
            ->get(route('rewards.index'))
            ->assertOk()
            ->assertSee('Tumbler GreenRun')
            ->assertSee('500 pts')
            ->assertSee('Stok: 10')
            ->assertSee('1,250');
    });

    it('renders single reward details correctly', function () {
        $user = User::factory()->create(['role' => 'participant']);
        $reward = Reward::factory()->create([
            'name' => 'Bibit Mangrove',
            'description' => 'Tanam mangrove peduli bumi',
            'required_points' => 300,
            'stock' => 15,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('rewards.show', $reward->id))
            ->assertOk()
            ->assertSee('Bibit Mangrove')
            ->assertSee('Tanam mangrove peduli bumi')
            ->assertSee('300 Poin')
            ->assertSee('15 Unit');
    });
});

describe('reward point redemption system', function () {
    it('successfully redeems reward when points and stock are available', function () {
        $user = User::factory()->create(['role' => 'participant']);
        $reward = Reward::factory()->create([
            'name' => 'Kaos GreenRun',
            'required_points' => 1000,
            'stock' => 5,
            'is_active' => true,
        ]);

        // Add 1500 points to user
        $event = Event::factory()->create();
        EventParticipant::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'current_event_points' => 1500,
        ]);

        $this->actingAs($user)
            ->post(route('rewards.redeem', $reward->id))
            ->assertRedirect(route('rewards.history'))
            ->assertSessionHas('success', 'Reward berhasil ditukarkan.');

        $reward->refresh();
        expect($reward->stock)->toBe(4);

        // Verify dynamically updated points balance: 1500 - 1000 = 500
        $user->refresh();
        expect($user->points)->toBe(500);

        $this->assertDatabaseHas('reward_redemptions', [
            'user_id' => $user->id,
            'reward_id' => $reward->id,
            'points_used' => 1000,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('activities', [
            'user_id' => $user->id,
            'activity_type' => 'redeem_reward',
            'points' => -1000,
        ]);
    });

    it('fails to redeem if user points are insufficient', function () {
        $user = User::factory()->create(['role' => 'participant']);
        $reward = Reward::factory()->create([
            'name' => 'Kaos GreenRun',
            'required_points' => 1000,
            'stock' => 5,
            'is_active' => true,
        ]);

        // Add only 200 points to user
        $event = Event::factory()->create();
        EventParticipant::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'current_event_points' => 200,
        ]);

        $this->actingAs($user)
            ->post(route('rewards.redeem', $reward->id))
            ->assertRedirect()
            ->assertSessionHas('error', 'Poin Anda tidak mencukupi untuk menukarkan reward ini.');

        $reward->refresh();
        expect($reward->stock)->toBe(5);

        $this->assertDatabaseMissing('reward_redemptions', [
            'user_id' => $user->id,
            'reward_id' => $reward->id,
        ]);
    });

    it('fails to redeem if reward is out of stock', function () {
        $user = User::factory()->create(['role' => 'participant']);
        $reward = Reward::factory()->create([
            'name' => 'Tote Bag GreenRun',
            'required_points' => 700,
            'stock' => 0,
            'is_active' => true,
        ]);

        // Add 1000 points to user
        $event = Event::factory()->create();
        EventParticipant::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'current_event_points' => 1000,
        ]);

        $this->actingAs($user)
            ->post(route('rewards.redeem', $reward->id))
            ->assertRedirect()
            ->assertSessionHas('error', 'Reward sedang tidak tersedia.');

        $this->assertDatabaseMissing('reward_redemptions', [
            'user_id' => $user->id,
            'reward_id' => $reward->id,
        ]);
    });
});

describe('redemption history log', function () {
    it('displays empty state if user has never redeemed', function () {
        $user = User::factory()->create(['role' => 'participant']);

        $this->actingAs($user)
            ->get(route('rewards.history'))
            ->assertOk()
            ->assertSee('Belum ada riwayat penukaran reward.');
    });

    it('displays list of previous redemptions and their status correctly', function () {
        $user = User::factory()->create(['role' => 'participant']);
        $reward = Reward::factory()->create([
            'name' => 'Bibit Mangrove',
            'is_active' => true,
        ]);

        RewardRedemption::create([
            'user_id' => $user->id,
            'reward_id' => $reward->id,
            'points_used' => 300,
            'status' => 'pending',
            'redeemed_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('rewards.history'))
            ->assertOk()
            ->assertSee('Bibit Mangrove')
            ->assertSee('-300 pts')
            ->assertSee('Pending');
    });
});
