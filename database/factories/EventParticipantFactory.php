<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventParticipant>
 */
class EventParticipantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $completedCheckpoints = fake()->numberBetween(0, 7);
        $points = $completedCheckpoints * fake()->numberBetween(10, 50);

        return [
            'event_id' => Event::factory(),
            'user_id' => User::factory(),
            'completed_checkpoints' => $completedCheckpoints,
            'current_event_points' => $points,
            'total_points' => $points + fake()->numberBetween(0, 500),
            'rank' => fake()->numberBetween(1, 50),
            'joined_at' => now()->subDays(fake()->numberBetween(0, 10)),
            'status' => 'joined',
        ];
    }

    /**
     * Associate with a specific event.
     */
    public function forEvent(Event $event): static
    {
        return $this->state(fn (array $attributes) => [
            'event_id' => $event->id,
            'completed_checkpoints' => fake()->numberBetween(0, $event->total_checkpoints),
        ]);
    }
}
