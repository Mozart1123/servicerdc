<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cas limites d'une demande publiée (cahier des charges §2.6) :
 * - stale_notified_at : client prévenu une fois qu'aucune proposition n'est arrivée après 24 h ;
 * - relaunched_at : dernière relance par le client (nouvelle vague + 7 jours de plus).
 * L'expiration à 7 jours utilise le statut "expired" (colonne status en varchar).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'stale_notified_at')) {
                $table->timestamp('stale_notified_at')->nullable()->after('matching_exhausted_at');
            }
            if (!Schema::hasColumn('service_requests', 'relaunched_at')) {
                $table->timestamp('relaunched_at')->nullable()->after('stale_notified_at');
            }
            $table->index(['mode', 'status', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropIndex(['mode', 'status', 'expires_at']);
            foreach (['stale_notified_at', 'relaunched_at'] as $col) {
                if (Schema::hasColumn('service_requests', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
