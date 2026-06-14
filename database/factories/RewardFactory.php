<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Reward;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reward>
 */
class RewardFactory extends Factory
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
            'name' => $this->faker->word(),
            'description' => $this->faker->paragraph(),
            'image' => 'https://images.unsplash.com/photo-1545239351-ef35f43d514b?auto=format&fit=crop&q=80&w=600',
            'required_points' => $this->faker->numberBetween(100, 1000),
            'stock' => $this->faker->numberBetween(5, 50),
            'total_redeemed' => 0,
            'is_active' => true,
        ];
    }
}
