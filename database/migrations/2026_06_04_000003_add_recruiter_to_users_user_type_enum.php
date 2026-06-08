<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Add 'recruiter' to the user_type enum and migrate legacy 'job_seeker' records.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Alter the enum to add 'recruiter'
        DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('client','artisan','job_seeker','recruiter') NOT NULL DEFAULT 'client'");

        // 2. Migrate existing job_seeker accounts to recruiter
        //    (They were intended to be recruiters all along)
        DB::table('users')
            ->where('user_type', 'job_seeker')
            ->update(['user_type' => 'recruiter']);
    }

    public function down(): void
    {
        // Revert: change recruiter back to job_seeker first, then remove from enum
        DB::table('users')
            ->where('user_type', 'recruiter')
            ->update(['user_type' => 'job_seeker']);

        DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('client','artisan','job_seeker') NOT NULL DEFAULT 'client'");
    }
};
