<?php

namespace Database\Seeders;

use App\Models\Checkpoint;
use App\Models\Event;
use Illuminate\Database\Seeder;

class CheckpointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = Event::all();

        foreach ($events as $event) {
            $numCheckpoints = $event->total_checkpoints ?: 8;
            for ($i = 1; $i <= $numCheckpoints; $i++) {
                Checkpoint::create([
                    'event_id' => $event->id,
                    'name' => 'Checkpoint '.$i,
                    'location' => 'Titik '.$i.' - '.$event->location,
                    'description' => 'Ini adalah deskripsi untuk checkpoint '.$i.' pada event '.$event->name.'. Silakan kunjungi titik ini untuk melakukan pemindaian QR Code.',
                    'sequence' => $i,
                    'points' => 50,
                    'status' => $i === 4 ? 'inactive' : 'active',
                ]);
            }
        }
    }
}
