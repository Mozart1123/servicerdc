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
            ['email' => 'superadmin@servicerdc.com'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@servicerdc.com',
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
            ['email' => 'admin@servicerdc.com'],
            [
                'name' => 'Admin ServiceRDC',
                'email' => 'admin@servicerdc.com',
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
            ['email' => 'user@servicerdc.com'],
            [
                'name' => 'Jean Kabongo',
                'email' => 'user@servicerdc.com',
                'phone' => '+243 999 000 003',
                'password' => Hash::make('User123!'),
                'role' => User::ROLE_USER,
                'user_type' => User::TYPE_CLIENT,
                'terms_accepted_at' => now(),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin users seeded successfully!');
        $this->command->table(
            ['Email', 'Password', 'Role'],
            [
                ['superadmin@servicerdc.com', 'SuperAdmin123!', 'Super Admin'],
                ['admin@servicerdc.com', 'Admin123!', 'Admin'],
                ['user@servicerdc.com', 'User123!', 'User'],
            ]
        );
    }
}
