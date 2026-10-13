<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Envoi par vagues d'une demande publiée (cahier des charges §2.3).
 * Une ligne = une demande envoyée à un artisan, dans une vague donnée.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('need_dispatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_request_id')->constrained('service_requests')->cascadeOnDelete();
            $table->foreignId('artisan_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('wave');
            $table->decimal('score', 5, 1)->default(0);
            $table->string('proximity', 10);
            // sent : en attente de réponse | proposed : proposition envoyée
            // declined : refusée | expired : pas de réponse dans le délai
            // closed : demande annulée par le client avant la réponse
            $table->string('status', 20)->default('sent');
            $table->dateTime('sent_at');
            $table->dateTime('deadline_at');
            $table->timestamp('responded_at')->nullable();
            $table->string('decline_reason', 255)->nullable();
            $table->timestamps();

            $table->unique(['service_request_id', 'artisan_id']);
            $table->index(['artisan_id', 'status']);
        });

        Schema::table('service_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'current_wave')) {
                $table->unsignedTinyInteger('current_wave')->default(0)->after('expires_at');
            }
            if (!Schema::hasColumn('service_requests', 'wave_deadline_at')) {
                $table->timestamp('wave_deadline_at')->nullable()->after('current_wave');
            }
            if (!Schema::hasColumn('service_requests', 'matching_widened_at')) {
                $table->timestamp('matching_widened_at')->nullable()->after('wave_deadline_at');
            }
            if (!Schema::hasColumn('service_requests', 'matching_exhausted_at')) {
                $table->timestamp('matching_exhausted_at')->nullable()->after('matching_widened_at');
            }
            $table->index('wave_deadline_at');
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropIndex(['wave_deadline_at']);
            foreach (['current_wave', 'wave_deadline_at', 'matching_widened_at', 'matching_exhausted_at'] as $col) {
                if (Schema::hasColumn('service_requests', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
        Schema::dropIfExists('need_dispatches');
    }
};
