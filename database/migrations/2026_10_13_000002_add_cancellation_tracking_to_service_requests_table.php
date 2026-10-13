<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Qui a annulé et quand : le score de fiabilité du matching ne pénalise
 * que les annulations faites par l'artisan, pas celles du client.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'cancelled_by')) {
                $table->foreignId('cancelled_by')->nullable()->after('completed_at')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('service_requests', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (Schema::hasColumn('service_requests', 'cancelled_by')) {
                $table->dropConstrainedForeignId('cancelled_by');
            }
            if (Schema::hasColumn('service_requests', 'cancelled_at')) {
                $table->dropColumn('cancelled_at');
            }
        });
    }
};
