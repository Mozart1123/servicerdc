<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Admin Users Seeder
 * 
 * Creates default admin and super admin users for the system.
 */
class AdminUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@proconnect.com'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@proconnect.com',
                'phone' => '+243 999 000 001',
                'password' => Hash::make('SuperAdmin123!'),
                'role' => User::ROLE_SUPER_ADMIN,
                'user_type' => User::TYPE_CLIENT,
                'terms_accepted_at' => now(),
                'email_verified_at' => now(),
            ]
        );

        // Create Admin
        User::updateOrCreate(
            ['email' => 'admin@proconnect.com'],
            [
                'name' => 'Admin ProConnect',
                'email' => 'admin@proconnect.com',
                'phone' => '+243 999 000 002',
                'password' => Hash::make('Admin123!'),
                'role' => User::ROLE_ADMIN,
                'user_type' => User::TYPE_CLIENT,
                'terms_accepted_at' => now(),
                'email_verified_at' => now(),
            ]
        );

        // Create Test User
        User::updateOrCreate(
            ['email' => 'user@proconnect.com'],
            [
                'name' => 'Jean Kabongo',
                'email' => 'user@proconnect.com',
                'phone' => '+243 999 000 003',
                'password' => Hash::make('User123!'),
                'role' => User::ROLE_USER,
                'user_type' => User::TYPE_CLIENT,
                'terms_accepted_at' => now(),
                'email_verified_at' => now(),
            ]
        );

        // Create Artisan Test User
        User::updateOrCreate(
            ['email' => 'artisan@proconnect.com'],
            [
                'name' => 'Artisan Test',
                'email' => 'artisan@proconnect.com',
                'phone' => '+243 999 111 222',
                'password' => Hash::make('Artisan123!'),
                'role' => User::ROLE_USER,
                'user_type' => User::TYPE_ARTISAN,
                'terms_accepted_at' => now(),
                'email_verified_at' => now(),
            ]
        );

        // Create Job Seeker Test User
        User::updateOrCreate(
            ['email' => 'chercheur@proconnect.com'],
            [
                'name' => 'Candidat Test',
                'email' => 'chercheur@proconnect.com',
                'phone' => '+243 999 333 444',
                'password' => Hash::make('Candidat123!'),
                'role' => User::ROLE_USER,
                'user_type' => User::TYPE_JOB_SEEKER,
                'terms_accepted_at' => now(),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin and test users seeded successfully!');
        $this->command->table(
            ['Email', 'Password', 'Type / Role'],
            [
                ['superadmin@proconnect.com', 'SuperAdmin123!', 'Super Admin'],
                ['admin@proconnect.com', 'Admin123!', 'Admin'],
                ['user@proconnect.com', 'User123!', 'Client'],
                ['artisan@proconnect.com', 'Artisan123!', 'Artisan'],
                ['chercheur@proconnect.com', 'Candidat123!', 'Chercheur d\'emploi'],
            ]
        );
    }
}
