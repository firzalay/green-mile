<?php

namespace Database\Seeders;

use App\Models\OrganizerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OrganizerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Super Admin
        $admin = User::factory()->create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        // 2. Approved Organizers (3 Total)
        $approved1 = User::factory()->create([
            'name' => 'GreenRun Organizer',
            'username' => 'organizer',
            'email' => 'organizer@example.com',
            'password' => Hash::make('password'),
            'role' => 'organizer',
            'status' => 'approved',
            'approved_by' => $admin->id,
            'approved_at' => now()->subDays(5),
        ]);
        OrganizerProfile::create([
            'user_id' => $approved1->id,
            'organization_name' => 'GreenRun Community',
            'contact_person' => 'GreenRun Organizer',
            'phone' => '081234567890',
            'description' => 'Komunitas GreenRun Eco Lestari cabang utama.',
            'website' => 'https://greenrun.example.com',
        ]);

        $approved2 = User::factory()->create([
            'name' => 'Eco Runner Org',
            'username' => 'ecorunner',
            'email' => 'organizer2@example.com',
            'password' => Hash::make('password'),
            'role' => 'organizer',
            'status' => 'approved',
            'approved_by' => $admin->id,
            'approved_at' => now()->subDays(3),
        ]);
        OrganizerProfile::create([
            'user_id' => $approved2->id,
            'organization_name' => 'Eco Runners Association',
            'contact_person' => 'Eco Runner Org',
            'phone' => '081234567891',
            'description' => 'Asosiasi pelari ramah lingkungan tingkat nasional.',
            'website' => 'https://ecorunners.example.com',
        ]);

        $approved3 = User::factory()->create([
            'name' => 'Lestari Event Organizer',
            'username' => 'lestari_eo',
            'email' => 'organizer3@example.com',
            'password' => Hash::make('password'),
            'role' => 'organizer',
            'status' => 'approved',
            'approved_by' => $admin->id,
            'approved_at' => now()->subDays(1),
        ]);
        OrganizerProfile::create([
            'user_id' => $approved3->id,
            'organization_name' => 'PT Lestari Event Organindo',
            'contact_person' => 'Lestari Event Organizer',
            'phone' => '081234567892',
            'description' => 'Event organizer profesional fokus pada gerakan zero-waste.',
        ]);

        // 3. Pending Organizers (5 Total)
        for ($i = 1; $i <= 5; $i++) {
            $pending = User::factory()->create([
                'name' => "Pending Organizer {$i}",
                'username' => "pending{$i}",
                'email' => "pending{$i}@example.com",
                'password' => Hash::make('password'),
                'role' => 'organizer',
                'status' => 'pending',
            ]);
            OrganizerProfile::create([
                'user_id' => $pending->id,
                'organization_name' => "Pending Org Group {$i}",
                'contact_person' => "Pending Organizer {$i}",
                'phone' => '08123456789'.($i + 2),
                'description' => "Profil deskripsi untuk pending organizer nomor {$i}.",
            ]);
        }

        // 4. Rejected Organizers (2 Total)
        $rejected1 = User::factory()->create([
            'name' => 'Rejected Organizer One',
            'username' => 'rejected1',
            'email' => 'rejected1@example.com',
            'password' => Hash::make('password'),
            'role' => 'organizer',
            'status' => 'rejected',
            'approved_by' => $admin->id,
            'approved_at' => now()->subDays(2),
        ]);
        OrganizerProfile::create([
            'user_id' => $rejected1->id,
            'organization_name' => 'Rejected Org Group A',
            'contact_person' => 'Rejected Organizer One',
            'phone' => '081234567898',
            'description' => 'Registrasi ditolak karena dokumen kelayakan tidak lengkap.',
        ]);

        $rejected2 = User::factory()->create([
            'name' => 'Rejected Organizer Two',
            'username' => 'rejected2',
            'email' => 'rejected2@example.com',
            'password' => Hash::make('password'),
            'role' => 'organizer',
            'status' => 'rejected',
            'approved_by' => $admin->id,
            'approved_at' => now()->subDays(1),
        ]);
        OrganizerProfile::create([
            'user_id' => $rejected2->id,
            'organization_name' => 'Rejected Org Group B',
            'contact_person' => 'Rejected Organizer Two',
            'phone' => '081234567899',
            'description' => 'Registrasi ditolak karena terindikasi organisasi fiktif.',
        ]);
    }
}
