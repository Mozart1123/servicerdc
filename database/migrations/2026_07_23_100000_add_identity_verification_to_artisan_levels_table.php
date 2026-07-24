<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('artisan_levels', function (Blueprint $table) {
            $table->string('identity_document_path')->nullable()->after('level_updated_at');
            $table->enum('identity_document_type', ['national_id', 'passport', 'driving_license'])->nullable()->after('identity_document_path');
            $table->enum('verification_status', ['not_submitted', 'pending', 'approved', 'rejected'])->default('not_submitted')->after('identity_document_type');
            $table->text('verification_rejection_reason')->nullable()->after('verification_status');
            $table->timestamp('verified_at')->nullable()->after('verification_rejection_reason');
            $table->foreignId('verified_by')->nullable()->after('verified_at')->constrained('users')->nullOnDelete();
            $table->timestamp('grace_period_ends_at')->nullable()->after('verified_by');
        });

        // Set a 30-day grace period for artisans who are already 'verifie' or 'elite' at the time of feature launch
        DB::table('artisan_levels')
            ->whereIn('level', ['verifie', 'elite'])
            ->where('verification_status', '!=', 'approved')
            ->update([
                'grace_period_ends_at' => now()->addDays(30),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artisan_levels', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn([
                'identity_document_path',
                'identity_document_type',
                'verification_status',
                'verification_rejection_reason',
                'verified_at',
                'verified_by',
                'grace_period_ends_at',
            ]);
        });
    }
};
