<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Admin ────────────────────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'admin@proconnect.cd'],
            [
                'name'              => 'Admin ProConnect',
                'email'             => 'admin@proconnect.cd',
                'phone'             => '+243000000001',
                'password'          => Hash::make('Admin@1234'),
                'role'              => User::ROLE_ADMIN,
                'user_type'         => User::TYPE_CLIENT, // Required by NOT NULL enum constraint; role=admin is the actual privilege
                'status'            => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
                'terms_accepted_at' => now(),
            ]
        );

        // ─── Artisan ─────────────────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'artisan@proconnect.cd'],
            [
                'name'              => 'Jean Artisan',
                'email'             => 'artisan@proconnect.cd',
                'phone'             => '+243000000002',
                'password'          => Hash::make('Artisan@1234'),
                'role'              => User::ROLE_USER,
                'user_type'         => User::TYPE_ARTISAN,
                'status'            => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
                'terms_accepted_at' => now(),
                'bio'               => 'Artisan qualifié spécialisé en plomberie et électricité.',
            ]
        );

        // ─── Client ──────────────────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'client@proconnect.cd'],
            [
                'name'              => 'Marie Client',
                'email'             => 'client@proconnect.cd',
                'phone'             => '+243000000003',
                'password'          => Hash::make('Client@1234'),
                'role'              => User::ROLE_USER,
                'user_type'         => User::TYPE_CLIENT,
                'status'            => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
                'terms_accepted_at' => now(),
            ]
        );

        $this->command->info('✅ Accounts created:');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin',   'admin@proconnect.cd',   'Admin@1234'],
                ['Artisan', 'artisan@proconnect.cd', 'Artisan@1234'],
                ['Client',  'client@proconnect.cd',  'Client@1234'],
            ]
        );
    }
}
