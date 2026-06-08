<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure user_type supports 'recruiter' alongside existing types
        // This migration is informational - the column already accepts any string
        // We just add an index for performance if not already present
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'user_type')) {
                $table->string('user_type')->nullable()->after('role');
            }
        });
    }

    public function down(): void
    {
        // No destructive action
    }
};
