<?php

namespace Database\Seeders;

use App\Models\Checkpoint;
use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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
                $points = 50;
                $name = 'Checkpoint '.$i;

                if ($event->name === 'GreenRun Surabaya') {
                    if ($i === 1) {
                        $name = 'CP-01';
                        $points = 100;
                    } elseif ($i === 2) {
                        $name = 'CP-02';
                        $points = 150;
                    } elseif ($i === 3) {
                        $name = 'CP-03';
                        $points = 250;
                    }
                }

                Checkpoint::create([
                    'event_id' => $event->id,
                    'name' => $name,
                    'location' => 'Titik '.$i.' - '.$event->location,
                    'description' => 'Ini adalah deskripsi untuk checkpoint '.$i.' pada event '.$event->name.'. Silakan kunjungi titik ini untuk melakukan pemindaian QR Code.',
                    'sequence' => $i,
                    'points' => $points,
                    'status' => $i === 4 ? 'inactive' : 'active',
                    'qr_token' => $i <= 3 ? Str::uuid()->toString() : null,
                ]);
            }
        }
    }
}
