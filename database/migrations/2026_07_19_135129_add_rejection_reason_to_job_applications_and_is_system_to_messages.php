<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add rejection_reason to job_applications
        Schema::table('job_applications', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('status');
        });

        // Add is_system to messages
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('is_system')->default(false)->after('is_read');
        });
    }

    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('is_system');
        });
    }
};
