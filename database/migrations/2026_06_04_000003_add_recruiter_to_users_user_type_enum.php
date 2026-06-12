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
        // SQLite doesn't support MODIFY COLUMN - enum values are stored as strings
        // Just migrate existing job_seeker accounts to recruiter
        DB::table('users')
            ->where('user_type', 'job_seeker')
            ->update(['user_type' => 'recruiter']);
    }

    public function down(): void
    {
        // Revert: change recruiter back to job_seeker
        DB::table('users')
            ->where('user_type', 'recruiter')
            ->update(['user_type' => 'job_seeker']);
    }
};
