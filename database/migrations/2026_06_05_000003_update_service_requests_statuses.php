<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add artisan_id to service_requests for direct linked artisan
        Schema::table('service_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'artisan_id')) {
                $table->foreignId('artisan_id')->nullable()->constrained('users')->nullOnDelete()->after('service_id');
            }
        });

        // Add extra statuses to job_applications
        Schema::table('job_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('job_applications', 'cv_id')) {
                $table->foreignId('cv_id')->nullable()->constrained('cvs')->nullOnDelete()->after('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropForeign(['artisan_id']);
            $table->dropColumn('artisan_id');
        });
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropForeign(['cv_id']);
            $table->dropColumn('cv_id');
        });
    }
};
