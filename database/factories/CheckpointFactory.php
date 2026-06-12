<?php

namespace Database\Factories;

use App\Models\Checkpoint;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Checkpoint>
 */
class CheckpointFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'name' => 'Checkpoint '.fake()->numberBetween(1, 100),
            'location' => fake()->streetName().', '.fake()->city(),
            'description' => fake()->sentence(),
            'sequence' => fake()->numberBetween(1, 10),
            'points' => fake()->randomElement([50, 100, 150]),
            'status' => 'active',
        ];
    }
}
