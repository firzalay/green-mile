<?php

use App\Models\Event;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

describe('authorization rules', function () {
    it('redirects guests to login', function () {
        $event = Event::factory()->create();
        $reward = Reward::factory()->create(['event_id' => $event->id]);

        $this->get(route('organizer.events.rewards.index', $event->id))->assertRedirect(route('login'));
        $this->get(route('organizer.events.rewards.create', $event->id))->assertRedirect(route('login'));
        $this->post(route('organizer.events.rewards.store', $event->id), [])->assertRedirect(route('login'));
        $this->get(route('organizer.rewards.show', $reward->id))->assertRedirect(route('login'));
        $this->get(route('organizer.rewards.edit', $reward->id))->assertRedirect(route('login'));
        $this->put(route('organizer.rewards.update', $reward->id), [])->assertRedirect(route('login'));
        $this->delete(route('organizer.rewards.destroy', $reward->id))->assertRedirect(route('login'));
    });

    it('returns 403 forbidden for participants', function () {
        $user = User::factory()->create(['role' => 'participant']);
        $event = Event::factory()->create();
        $reward = Reward::factory()->create(['event_id' => $event->id]);

        $this->actingAs($user)->get(route('organizer.events.rewards.index', $event->id))->assertStatus(403);
        $this->actingAs($user)->get(route('organizer.events.rewards.create', $event->id))->assertStatus(403);
        $this->actingAs($user)->post(route('organizer.events.rewards.store', $event->id), [])->assertStatus(403);
        $this->actingAs($user)->get(route('organizer.rewards.show', $reward->id))->assertStatus(403);
        $this->actingAs($user)->get(route('organizer.rewards.edit', $reward->id))->assertStatus(403);
        $this->actingAs($user)->put(route('organizer.rewards.update', $reward->id), [])->assertStatus(403);
        $this->actingAs($user)->delete(route('organizer.rewards.destroy', $reward->id))->assertStatus(403);
    });

    it('prevents organizers from accessing other organizers rewards', function () {
        $organizer1 = User::factory()->create(['role' => 'organizer']);
        $organizer2 = User::factory()->create(['role' => 'organizer']);

        $event = Event::factory()->create(['organizer_id' => $organizer1->id]);
        $reward = Reward::factory()->create(['event_id' => $event->id]);

        $this->actingAs($organizer2)->get(route('organizer.events.rewards.index', $event->id))->assertStatus(403);
        $this->actingAs($organizer2)->get(route('organizer.events.rewards.create', $event->id))->assertStatus(403);
        $this->actingAs($organizer2)->post(route('organizer.events.rewards.store', $event->id), [])->assertStatus(403);

        $this->actingAs($organizer2)->get(route('organizer.rewards.show', $reward->id))->assertStatus(403);
        $this->actingAs($organizer2)->get(route('organizer.rewards.edit', $reward->id))->assertStatus(403);
        $this->actingAs($organizer2)->put(route('organizer.rewards.update', $reward->id), [])->assertStatus(403);
        $this->actingAs($organizer2)->delete(route('organizer.rewards.destroy', $reward->id))->assertStatus(403);
    });
});

describe('organizer reward management crud operations', function () {
    it('allows organizer to list rewards of their event', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $reward1 = Reward::factory()->create([
            'event_id' => $event->id,
            'name' => 'First Reward',
        ]);
        $reward2 = Reward::factory()->create([
            'event_id' => $event->id,
            'name' => 'Second Reward',
        ]);

        $response = $this->actingAs($organizer)->get(route('organizer.events.rewards.index', $event->id));

        $response->assertOk()
            ->assertSee('First Reward')
            ->assertSee('Second Reward');
    });

    it('allows organizer to render create reward page', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $response = $this->actingAs($organizer)->get(route('organizer.events.rewards.create', $event->id));

        $response->assertOk();
    });

    it('validates attributes and creates reward successfully', function () {
        Storage::fake('public');
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $image = UploadedFile::fake()->image('reward_mug.jpg')->size(500);

        $payload = [
            'name' => 'Tumbler Premium',
            'description' => 'Tumbler stainless steel ramah lingkungan.',
            'image' => $image,
            'required_points' => 300,
            'stock' => 25,
        ];

        $response = $this->actingAs($organizer)->post(route('organizer.events.rewards.store', $event->id), $payload);

        $response->assertRedirect(route('organizer.events.rewards.index', $event->id));

        $reward = Reward::where('name', 'Tumbler Premium')->first();
        expect($reward)->not->toBeNull();
        dump($reward->image);
        expect($reward->image)->toStartWith('/storage/rewards/');

        $this->assertDatabaseHas('rewards', [
            'event_id' => $event->id,
            'name' => 'Tumbler Premium',
            'required_points' => 300,
            'stock' => 25,
            'total_redeemed' => 0,
            'is_active' => 1,
        ]);
    });

    it('validates reward image upload constraints', function () {
        Storage::fake('public');
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $largeImage = UploadedFile::fake()->image('large.jpg')->size(3000);
        $payload = [
            'name' => 'Tumbler Premium',
            'description' => 'Tumbler stainless steel ramah lingkungan.',
            'image' => $largeImage,
            'required_points' => 300,
            'stock' => 25,
        ];

        $this->actingAs($organizer)
            ->post(route('organizer.events.rewards.store', $event->id), $payload)
            ->assertSessionHasErrors(['image']);

        $pdfFile = UploadedFile::fake()->create('document.pdf', 100);
        $payload['image'] = $pdfFile;

        $this->actingAs($organizer)
            ->post(route('organizer.events.rewards.store', $event->id), $payload)
            ->assertSessionHasErrors(['image']);
    });

    it('allows organizer to view reward details', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $reward = Reward::factory()->create([
            'event_id' => $event->id,
            'name' => 'Bibit Bakau',
            'description' => 'Penanaman bibit di pesisir.',
        ]);

        $response = $this->actingAs($organizer)->get(route('organizer.rewards.show', $reward->id));

        $response->assertOk()
            ->assertSee('Bibit Bakau')
            ->assertSee('Penanaman bibit di pesisir.');
    });

    it('allows organizer to render edit reward page', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $reward = Reward::factory()->create(['event_id' => $event->id]);

        $response = $this->actingAs($organizer)->get(route('organizer.rewards.edit', $reward->id));

        $response->assertOk();
    });

    it('validates attributes and updates reward successfully', function () {
        Storage::fake('public');
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $reward = Reward::factory()->create([
            'event_id' => $event->id,
            'name' => 'Old Reward Name',
            'is_active' => true,
        ]);

        $newImage = UploadedFile::fake()->image('updated.png')->size(200);

        $payload = [
            'name' => 'Updated Reward Name',
            'description' => 'Updated Description text.',
            'image' => $newImage,
            'required_points' => 450,
            'stock' => 15,
            'is_active' => 0,
        ];

        $response = $this->actingAs($organizer)->put(route('organizer.rewards.update', $reward->id), $payload);

        $response->assertRedirect(route('organizer.rewards.show', $reward->id));

        $this->assertDatabaseHas('rewards', [
            'id' => $reward->id,
            'name' => 'Updated Reward Name',
            'description' => 'Updated Description text.',
            'required_points' => 450,
            'stock' => 15,
            'is_active' => 0,
        ]);

        $reward->refresh();
        expect($reward->image)->toStartWith('/storage/rewards/');
    });

    it('allows organizer to delete a reward with zero redemptions', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $reward = Reward::factory()->create([
            'event_id' => $event->id,
            'total_redeemed' => 0,
        ]);

        $response = $this->actingAs($organizer)->delete(route('organizer.rewards.destroy', $reward->id));

        $response->assertRedirect(route('organizer.events.rewards.index', $event->id));

        $this->assertDatabaseMissing('rewards', [
            'id' => $reward->id,
        ]);
    });

    it('prevents deletion of a reward that has been redeemed', function () {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $reward = Reward::factory()->create([
            'event_id' => $event->id,
            'total_redeemed' => 1,
        ]);

        $response = $this->actingAs($organizer)->delete(route('organizer.rewards.destroy', $reward->id));

        $response->assertRedirect();
        $response->assertSessionHasErrors(['total_redeemed']);

        $this->assertDatabaseHas('rewards', [
            'id' => $reward->id,
        ]);
    });
});
