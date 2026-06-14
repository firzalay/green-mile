<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
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
        $startDate = fake()->dateTimeBetween('now', '+3 months');
        $endDate = (clone $startDate)->modify('+'.fake()->numberBetween(0, 2).' days');

        return [
            'organizer_id' => User::factory()->state(['role' => 'organizer']),
            'name' => $this->faker->words(3, true).' Run '.fake()->year(),
            'location' => fake()->city().', '.fake()->country(),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'total_checkpoints' => fake()->numberBetween(5, 10),
            'is_active' => true,
            'description' => 'Ini adalah event GreenRun untuk mendukung kelestarian alam dan lingkungan sekitar. Mari berlari dan kurangi emisi karbon!',
            'banner' => 'https://images.unsplash.com/photo-1502224562085-639556652f33?auto=format&fit=crop&q=80&w=800',
            'total_rewards' => 'Rp 10.000.000',
            'max_points' => 500,
            'point_pool' => 50000,
            'remaining_point_pool' => 50000,
            'max_participants' => fake()->numberBetween(50, 200),
            'status' => 'published',
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
        return $this->state(function (array $attributes) {
            $startDate = fake()->dateTimeBetween('+1 week', '+2 months');
            $endDate = (clone $startDate)->modify('+'.fake()->numberBetween(0, 2).' days');

            return [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'status' => 'published',
            ];
        });
    }

    /**
     * State for an ongoing event.
     */
    public function ongoing(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = now();
            $endDate = (clone $startDate)->modify('+'.fake()->numberBetween(0, 2).' days');

            return [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'status' => 'ongoing',
            ];
        });
    }

    /**
     * State for a finished event.
     */
    public function finished(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = fake()->dateTimeBetween('-2 months', '-1 week');
            $endDate = (clone $startDate)->modify('+'.fake()->numberBetween(0, 2).' days');

            return [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'status' => 'finished',
            ];
        });
    }
}
