<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'          => 'Starter',
                'slug'          => 'starter',
                'description'   => 'Parfait pour débuter et tester la plateforme.',
                'price_monthly' => 0.00,
                'price_yearly'  => 0.00,
                'currency'      => 'USD',
                'color'         => 'slate',
                'icon'          => 'fa-seedling',
                'is_popular'    => false,
                'is_active'     => true,
                'max_services'  => 1,
                'sort_order'    => 1,
                'features'      => [
                    '1 service publié',
                    'Profil basique',
                    'Accès aux offres d\'emploi',
                    'Support par email',
                ],
            ],
            [
                'name'          => 'Pro',
                'slug'          => 'pro',
                'description'   => 'Idéal pour les artisans actifs qui veulent se développer.',
                'price_monthly' => 9.99,
                'price_yearly'  => 89.99,
                'currency'      => 'USD',
                'color'         => 'blue',
                'icon'          => 'fa-bolt',
                'is_popular'    => true,
                'is_active'     => true,
                'max_services'  => 10,
                'sort_order'    => 2,
                'features'      => [
                    '10 services publiés',
                    'Badge "Artisan Vérifié"',
                    'Profil mis en avant',
                    'Statistiques avancées',
                    'Priorité dans les recherches',
                    'Support prioritaire',
                ],
            ],
            [
                'name'          => 'Elite',
                'slug'          => 'elite',
                'description'   => 'Pour les professionnels qui veulent dominer leur marché.',
                'price_monthly' => 24.99,
                'price_yearly'  => 219.99,
                'currency'      => 'USD',
                'color'         => 'amber',
                'icon'          => 'fa-crown',
                'is_popular'    => false,
                'is_active'     => true,
                'max_services'  => 999,
                'sort_order'    => 3,
                'features'      => [
                    'Services illimités',
                    'Badge "Elite Pro"',
                    'Placement #1 garanti',
                    'Tableau de bord analytique complet',
                    'Accès API partenaires',
                    'Manager dédié',
                    'Support 24/7 WhatsApp',
                ],
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }

        $this->command->info('Subscription plans seeded!');
        $this->command->table(
            ['Plan', 'Mensuel', 'Annuel', 'Max Services'],
            collect($plans)->map(fn ($p) => [$p['name'], '$'.$p['price_monthly'], '$'.$p['price_yearly'], $p['max_services']])->toArray()
        );
    }
}
