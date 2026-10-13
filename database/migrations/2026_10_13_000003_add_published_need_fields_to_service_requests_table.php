<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Demandes publiées par le client ("Décrire mon besoin") : sans artisan désigné,
 * elles seront envoyées par le matching aux artisans pertinents.
 * mode = 'direct' (demande à un artisan depuis le catalogue) | 'open' (besoin publié).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'mode')) {
                $table->string('mode', 20)->default('direct')->after('artisan_id');
            }
            if (!Schema::hasColumn('service_requests', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('mode')->constrained('categories')->nullOnDelete();
            }
            if (!Schema::hasColumn('service_requests', 'service_type_id')) {
                $table->foreignId('service_type_id')->nullable()->after('category_id')->constrained('service_types')->nullOnDelete();
            }
            if (!Schema::hasColumn('service_requests', 'commune_id')) {
                $table->foreignId('commune_id')->nullable()->after('city')->constrained('communes')->nullOnDelete();
            }
            if (!Schema::hasColumn('service_requests', 'quartier')) {
                $table->string('quartier', 120)->nullable()->after('commune_id');
            }
            if (!Schema::hasColumn('service_requests', 'timing')) {
                $table->string('timing', 20)->nullable()->after('urgency');
            }
            if (!Schema::hasColumn('service_requests', 'preferred_date')) {
                $table->date('preferred_date')->nullable()->after('timing');
            }
            if (!Schema::hasColumn('service_requests', 'budget_type')) {
                $table->string('budget_type', 10)->nullable()->after('budget_max');
            }
            if (!Schema::hasColumn('service_requests', 'photos')) {
                $table->json('photos')->nullable()->after('description');
            }
            if (!Schema::hasColumn('service_requests', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('cancelled_at');
            }
            $table->index(['mode', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropIndex(['mode', 'status']);
            foreach (['category_id', 'service_type_id', 'commune_id'] as $fk) {
                if (Schema::hasColumn('service_requests', $fk)) {
                    $table->dropConstrainedForeignId($fk);
                }
            }
            foreach (['mode', 'quartier', 'timing', 'preferred_date', 'budget_type', 'photos', 'expires_at'] as $col) {
                if (Schema::hasColumn('service_requests', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
