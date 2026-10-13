<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Proposition retenue par le client : son montant est verrouillé dans la mission
 * créée à l'acceptation (cahier des charges §2.5).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'accepted_proposal_id')) {
                $table->foreignId('accepted_proposal_id')->nullable()->after('artisan_id')->constrained('need_proposals')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (Schema::hasColumn('service_requests', 'accepted_proposal_id')) {
                $table->dropConstrainedForeignId('accepted_proposal_id');
            }
        });
    }
};
