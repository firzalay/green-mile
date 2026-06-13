<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ParticipantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(30)->create([
            'role' => 'participant',
            'status' => 'active',
        ]);
    }
}
