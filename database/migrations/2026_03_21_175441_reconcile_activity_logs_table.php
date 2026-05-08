<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            // Check and add missing columns if they don't exist
            if (!Schema::hasColumn('activity_logs', 'model_type')) {
                $table->string('model_type')->nullable()->after('action');
            }
            if (!Schema::hasColumn('activity_logs', 'model_id')) {
                $table->unsignedBigInteger('model_id')->nullable()->after('model_type');
            }
            if (!Schema::hasColumn('activity_logs', 'payload')) {
                $table->json('payload')->nullable()->after('model_id');
            }
            if (!Schema::hasColumn('activity_logs', 'severity')) {
                $table->enum('severity', ['info', 'warning', 'danger'])->default('info')->after('payload');
            }

            // Drop redundant columns if they exist
            if (Schema::hasColumn('activity_logs', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('activity_logs', 'subject_type')) {
                $table->dropColumn('subject_type');
            }
            if (Schema::hasColumn('activity_logs', 'subject_id')) {
                $table->dropColumn('subject_id');
            }
            if (Schema::hasColumn('activity_logs', 'properties')) {
                $table->dropColumn('properties');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to revert specifically for this reconciliation
    }
};
