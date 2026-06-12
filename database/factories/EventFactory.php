<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true).' Run '.fake()->year(),
            'location' => fake()->city().', '.fake()->country(),
            'event_date' => fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'total_checkpoints' => fake()->numberBetween(5, 10),
            'is_active' => true,
        ];
    }

    /**
     * State for an inactive event.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
