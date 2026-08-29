<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // role/status are not mass-assignable, so each seeded user below has them
        // applied afterwards via forceFill (bypassing $fillable is fine here:
        // these are trusted, hardcoded values, not user input).

        // ─── Admin ────────────────────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'admin@proconnect.cd'],
            [
                'name'              => 'Admin ProConnect',
                'email'             => 'admin@proconnect.cd',
                'phone'             => '+243000000001',
                'password'          => Hash::make('Admin@1234'),
                'user_type'         => User::TYPE_CLIENT, // Required by NOT NULL enum constraint; role=admin is the actual privilege
                'email_verified_at' => now(),
                'terms_accepted_at' => now(),
            ]
        )->forceFill(['role' => User::ROLE_ADMIN, 'status' => User::STATUS_ACTIVE])->save();

        // ─── Artisan ─────────────────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'artisan@proconnect.cd'],
            [
                'name'              => 'Jean Artisan',
                'email'             => 'artisan@proconnect.cd',
                'phone'             => '+243000000002',
                'password'          => Hash::make('Artisan@1234'),
                'user_type'         => User::TYPE_ARTISAN,
                'email_verified_at' => now(),
                'terms_accepted_at' => now(),
                'bio'               => 'Artisan qualifié spécialisé en plomberie et électricité.',
            ]
        )->forceFill(['role' => User::ROLE_USER, 'status' => User::STATUS_ACTIVE])->save();

        // ─── Client ──────────────────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'client@proconnect.cd'],
            [
                'name'              => 'Marie Client',
                'email'             => 'client@proconnect.cd',
                'phone'             => '+243000000003',
                'password'          => Hash::make('Client@1234'),
                'user_type'         => User::TYPE_CLIENT,
                'email_verified_at' => now(),
                'terms_accepted_at' => now(),
            ]
        )->forceFill(['role' => User::ROLE_USER, 'status' => User::STATUS_ACTIVE])->save();

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
