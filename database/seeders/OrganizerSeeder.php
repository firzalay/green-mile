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
        User::factory()->create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        // 2. Organizer Approved (First one: organizer@example.com for backward compatibility)
        $approved1 = User::factory()->create([
            'name' => 'GreenRun Organizer',
            'username' => 'organizer',
            'email' => 'organizer@example.com',
            'password' => Hash::make('password'),
            'role' => 'organizer',
            'status' => 'approved',
        ]);
        OrganizerProfile::create([
            'user_id' => $approved1->id,
            'organization_name' => 'GreenRun Community',
            'contact_person' => 'GreenRun Organizer',
            'phone' => '081234567890',
        ]);

        $approved2 = User::factory()->create([
            'name' => 'Eco Runner Org',
            'username' => 'ecorunner',
            'email' => 'organizer2@example.com',
            'password' => Hash::make('password'),
            'role' => 'organizer',
            'status' => 'approved',
        ]);
        OrganizerProfile::create([
            'user_id' => $approved2->id,
            'organization_name' => 'Eco Runners Association',
            'contact_person' => 'Eco Runner Org',
            'phone' => '081234567891',
        ]);

        // 3. Organizer Pending
        $pending1 = User::factory()->create([
            'name' => 'Pending Organizer One',
            'username' => 'pending1',
            'email' => 'pending1@example.com',
            'password' => Hash::make('password'),
            'role' => 'organizer',
            'status' => 'pending',
        ]);
        OrganizerProfile::create([
            'user_id' => $pending1->id,
            'organization_name' => 'Pending Org Group 1',
            'contact_person' => 'Pending Organizer One',
            'phone' => '081234567892',
        ]);

        $pending2 = User::factory()->create([
            'name' => 'Pending Organizer Two',
            'username' => 'pending2',
            'email' => 'pending2@example.com',
            'password' => Hash::make('password'),
            'role' => 'organizer',
            'status' => 'pending',
        ]);
        OrganizerProfile::create([
            'user_id' => $pending2->id,
            'organization_name' => 'Pending Org Group 2',
            'contact_person' => 'Pending Organizer Two',
            'phone' => '081234567893',
        ]);

        // 4. Organizer Rejected
        $rejected = User::factory()->create([
            'name' => 'Rejected Organizer',
            'username' => 'rejected',
            'email' => 'rejected@example.com',
            'password' => Hash::make('password'),
            'role' => 'organizer',
            'status' => 'rejected',
        ]);
        OrganizerProfile::create([
            'user_id' => $rejected->id,
            'organization_name' => 'Rejected Org Group',
            'contact_person' => 'Rejected Organizer',
            'phone' => '081234567894',
        ]);
    }
}
