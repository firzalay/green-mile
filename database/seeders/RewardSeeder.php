<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Reward;
use Illuminate\Database\Seeder;

class RewardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = Event::all();

        $templates = [
            [
                'name' => 'Tumbler GreenRun',
                'description' => 'Tumbler stainless steel eksklusif GreenRun untuk mengurangi penggunaan botol plastik sekali pakai.',
                'image' => 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?auto=format&fit=crop&q=80&w=600',
                'required_points' => 500,
                'stock' => 20,
            ],
            [
                'name' => 'Kaos GreenRun',
                'description' => 'Kaos sport dry-fit ramah lingkungan terbuat dari bahan serat bambu daur ulang.',
                'image' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&q=80&w=600',
                'required_points' => 1000,
                'stock' => 10,
            ],
            [
                'name' => 'Tote Bag GreenRun',
                'description' => 'Tote bag kanvas organik serbaguna dengan desain eco-friendly untuk belanja bebas plastik.',
                'image' => 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&q=80&w=600',
                'required_points' => 700,
                'stock' => 15,
            ],
            [
                'name' => 'Bibit Mangrove',
                'description' => 'Dukung penanaman bibit mangrove di kawasan pesisir untuk mencegah abrasi dan merehabilitasi ekosistem laut.',
                'image' => 'https://images.unsplash.com/photo-1545239351-ef35f43d514b?auto=format&fit=crop&q=80&w=600',
                'required_points' => 300,
                'stock' => 50,
            ],
            [
                'name' => 'Voucher Sponsor',
                'description' => 'Voucher belanja ramah lingkungan senilai Rp 50.000 di merchant sponsor GreenRun.',
                'image' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&q=80&w=600',
                'required_points' => 400,
                'stock' => 30,
            ],
        ];

        // Seed 2 rewards for each event
        foreach ($events as $index => $event) {
            $firstTemplate = $templates[$index % count($templates)];
            $secondTemplate = $templates[($index + 1) % count($templates)];

            Reward::create(array_merge($firstTemplate, [
                'event_id' => $event->id,
                'total_redeemed' => 0,
                'is_active' => true,
            ]));

            Reward::create(array_merge($secondTemplate, [
                'event_id' => $event->id,
                'total_redeemed' => 0,
                'is_active' => true,
            ]));
        }
    }
}
