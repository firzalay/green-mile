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
            'description' => 'Ini adalah event GreenRun untuk mendukung kelestarian alam dan lingkungan sekitar. Mari berlari dan kurangi emisi karbon!',
            'banner_image' => 'https://images.unsplash.com/photo-1502224562085-639556652f33?auto=format&fit=crop&q=80&w=800',
            'total_rewards' => 'Rp 10.000.000',
            'max_points' => 500,
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

    /**
     * State for an upcoming event.
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'event_date' => fake()->dateTimeBetween('+1 week', '+2 months')->format('Y-m-d'),
        ]);
    }

    /**
     * State for an ongoing event.
     */
    public function ongoing(): static
    {
        return $this->state(fn (array $attributes) => [
            'event_date' => now()->format('Y-m-d'),
        ]);
    }

    /**
     * State for a finished event.
     */
    public function finished(): static
    {
        return $this->state(fn (array $attributes) => [
            'event_date' => fake()->dateTimeBetween('-2 months', '-1 week')->format('Y-m-d'),
        ]);
    }
}
