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
        Schema::table('job_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('job_applications', 'cover_letter')) {
                $table->text('cover_letter')->nullable();
            }
            if (!Schema::hasColumn('job_applications', 'applied_at')) {
                $table->timestamp('applied_at')->nullable();
            }
            if (!Schema::hasColumn('job_applications', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable();
            }
            if (!Schema::hasColumn('job_applications', 'admin_notes')) {
                $table->text('admin_notes')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            if (Schema::hasColumn('job_applications', 'cover_letter')) {
                $table->dropColumn('cover_letter');
            }
            if (Schema::hasColumn('job_applications', 'applied_at')) {
                $table->dropColumn('applied_at');
            }
            if (Schema::hasColumn('job_applications', 'reviewed_at')) {
                $table->dropColumn('reviewed_at');
            }
            if (Schema::hasColumn('job_applications', 'admin_notes')) {
                $table->dropColumn('admin_notes');
            }
        });
    }
};
