<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('job_applications', 'applicant_name')) {
                $table->string('applicant_name')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('job_applications', 'applicant_email')) {
                $table->string('applicant_email')->nullable()->after('applicant_name');
            }
            if (!Schema::hasColumn('job_applications', 'applicant_phone')) {
                $table->string('applicant_phone')->nullable()->after('applicant_email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            if (Schema::hasColumn('job_applications', 'applicant_phone')) {
                $table->dropColumn('applicant_phone');
            }
            if (Schema::hasColumn('job_applications', 'applicant_email')) {
                $table->dropColumn('applicant_email');
            }
            if (Schema::hasColumn('job_applications', 'applicant_name')) {
                $table->dropColumn('applicant_name');
            }
        });
    }
};
