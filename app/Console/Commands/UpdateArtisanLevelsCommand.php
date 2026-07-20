<?php

namespace App\Console\Commands;

use App\Models\ArtisanLevel;
use App\Models\Review;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UpdateArtisanLevelsCommand extends Command
{
    protected $signature   = 'proconnect:update-artisan-levels';
    protected $description = 'Recalcule les niveaux de réputation pour tous les artisans (à planifier quotidiennement).';

    public function handle(): int
    {
        $this->info('🔄 Début du recalcul des niveaux artisans...');

        $artisans = User::where('user_type', User::TYPE_ARTISAN)->get();
        $bar      = $this->output->createProgressBar($artisans->count());
        $bar->start();

        $updated = 0;

        foreach ($artisans as $artisan) {
            // ── 1. Calcul des missions ────────────────────────────────────────────
            $completedMissions       = $artisan->missionsAsArtisan()->where('status', 'completed');
            $totalMissions           = $completedMissions->count();
            $missionsViaPlatform     = (clone $completedMissions)->where('payment_channel', 'platform')->count();
            $platformRatio           = $totalMissions > 0 ? ($missionsViaPlatform / $totalMissions) : 0;

            // ── 2. Note moyenne (Review approuvés uniquement) ─────────────────────
            $avgRating = Review::where('artisan_id', $artisan->id)
                ->where('status', 'approved')
                ->avg('rating') ?? 0.0;

            // ── 3. Abonnement Premium actif ───────────────────────────────────────
            $hasPremium = Subscription::where('user_id', $artisan->id)
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
                })
                ->whereHas('subscriptionPlan', fn($q) => $q->where('slug', 'premium'))
                ->exists();

            // ── 4. Litiges ouverts non résolus ───────────────────────────────────
            $openDisputes = \App\Models\SupportTicket::where('user_id', $artisan->id)
                ->where('ticket_type', 'dispute')
                ->where('status', 'open')
                ->count();

            // ── 5. Ancienneté (30+ jours pour Élite) ─────────────────────────────
            $accountAgeDays = $artisan->created_at->diffInDays(now());

            // ── 6. Calcul du niveau selon les seuils ─────────────────────────────
            $level = $this->computeLevel(
                $totalMissions,
                $platformRatio,
                $avgRating,
                $hasPremium,
                $openDisputes,
                $accountAgeDays
            );

            // ── 7. Upsert dans artisan_levels ─────────────────────────────────────
            $existing = ArtisanLevel::firstOrNew(['user_id' => $artisan->id]);
            $levelChanged = $existing->level !== $level;

            $existing->fill([
                'level'                  => $level,
                'total_missions'         => $totalMissions,
                'missions_via_platform'  => $missionsViaPlatform,
                'average_rating'         => round($avgRating, 1),
                'level_updated_at'       => $levelChanged ? now() : $existing->level_updated_at,
            ])->save();

            $updated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Recalcul terminé : {$updated} artisans mis à jour.");

        return self::SUCCESS;
    }

    /**
     * Determine artisan level based on thresholds:
     *
     * Nouveau  → par défaut
     * Actif    → 5+ missions dont ≥50% via plateforme
     * Vérifié  → 10+ missions, ≥60% platform, note ≥4.0, Premium actif
     * Élite    → 30+ missions, ≥85% platform, note ≥4.5, aucun litige ouvert,
     *            Premium actif, compte ≥180 jours
     */
    private function computeLevel(
        int $totalMissions,
        float $platformRatio,
        float $avgRating,
        bool $hasPremium,
        int $openDisputes,
        int $accountAgeDays
    ): string {
        // Élite
        if (
            $totalMissions >= 30 &&
            $platformRatio >= 0.85 &&
            $avgRating >= 4.5 &&
            $openDisputes === 0 &&
            $hasPremium &&
            $accountAgeDays >= 180
        ) {
            return ArtisanLevel::LEVEL_ELITE;
        }

        // Vérifié
        if (
            $totalMissions >= 10 &&
            $platformRatio >= 0.60 &&
            $avgRating >= 4.0 &&
            $hasPremium
        ) {
            return ArtisanLevel::LEVEL_VERIFIE;
        }

        // Actif
        if (
            $totalMissions >= 5 &&
            $platformRatio >= 0.50
        ) {
            return ArtisanLevel::LEVEL_ACTIF;
        }

        // Par défaut
        return ArtisanLevel::LEVEL_NOUVEAU;
    }
}
