<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Document;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Seed 1-3 documents per user primarily for testing
            $docCount = rand(1, 3);
            
            for ($i = 0; $i < $docCount; $i++) {
                Document::create([
                    'user_id' => $user->id,
                    'type' => ['identity', 'diploma', 'business_reg'][rand(0, 2)],
                    'file_path' => 'https://placehold.co/800x600/e2e8f0/64748b?text=DOCUMENT+SAMPLE',
                    'status' => ['pending', 'verified', 'rejected'][rand(0, 2)],
                    'ai_status' => ['Authentic', 'Blurry', 'Tampered'][rand(0, 2)],
                    'ai_confidence' => ['high', 'medium', 'low'][rand(0, 2)],
                    'created_at' => now()->subDays(rand(0, 30)),
                ]);
            }
        }
    }
}
