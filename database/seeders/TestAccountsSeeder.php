<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestAccountsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Compte Client
        User::updateOrCreate(
            ['email' => 'client@test.com'],
            [
                'name' => 'Marie Mbayo',
                'email' => 'client@test.com',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_USER,
                'user_type' => User::TYPE_CLIENT,
                'terms_accepted_at' => now(),
                'email_verified_at' => now(),
            ]
        );

        // 2. Compte Artisan
        User::updateOrCreate(
            ['email' => 'artisan@test.com'],
            [
                'name' => 'Jean Artisan',
                'email' => 'artisan@test.com',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_USER,
                'user_type' => User::TYPE_ARTISAN,
                'terms_accepted_at' => now(),
                'email_verified_at' => now(),
            ]
        );

        // 3. Compte Emploie (Job Seeker)
        User::updateOrCreate(
            ['email' => 'emploie@test.com'],
            [
                'name' => 'Alain Emploie',
                'email' => 'emploie@test.com',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_USER,
                'user_type' => User::TYPE_JOB_SEEKER,
                'terms_accepted_at' => now(),
                'email_verified_at' => now(),
            ]
        );
    }
}
