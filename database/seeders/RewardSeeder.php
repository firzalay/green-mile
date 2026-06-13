<?php

namespace Database\Seeders;

use App\Models\Reward;
use Illuminate\Database\Seeder;

class RewardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Reward::create([
            'name' => 'Bibit Mangrove',
            'description' => 'Dukung penanaman bibit mangrove di kawasan pesisir untuk mencegah abrasi dan merehabilitasi ekosistem laut.',
            'image' => 'https://images.unsplash.com/photo-1545239351-ef35f43d514b?auto=format&fit=crop&q=80&w=600',
            'required_points' => 300,
            'stock' => 50,
            'is_active' => true,
        ]);

        Reward::create([
            'name' => 'Tumbler GreenRun',
            'description' => 'Tumbler stainless steel eksklusif GreenRun untuk mengurangi penggunaan botol plastik sekali pakai.',
            'image' => 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?auto=format&fit=crop&q=80&w=600',
            'required_points' => 500,
            'stock' => 20,
            'is_active' => true,
        ]);

        Reward::create([
            'name' => 'Kaos GreenRun',
            'description' => 'Kaos sport dry-fit ramah lingkungan terbuat dari bahan serat bambu daur ulang.',
            'image' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&q=80&w=600',
            'required_points' => 1000,
            'stock' => 10,
            'is_active' => true,
        ]);

        Reward::create([
            'name' => 'Tote Bag GreenRun',
            'description' => 'Tote bag kanvas organik serbaguna dengan desain eco-friendly untuk belanja bebas plastik.',
            'image' => 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&q=80&w=600',
            'required_points' => 700,
            'stock' => 0,
            'is_active' => true,
        ]);
    }
}
