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
        Schema::table('job_offers', function (Blueprint $table) {
            if (!Schema::hasColumn('job_offers', 'employer_id')) {
                $table->foreignId('employer_id')->nullable()->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('job_offers', 'deadline')) {
                $table->date('deadline')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_offers', function (Blueprint $table) {
            if (Schema::hasColumn('job_offers', 'employer_id')) {
                $table->dropForeignIdFor('employer_id');
                $table->dropColumn('employer_id');
            }
            if (Schema::hasColumn('job_offers', 'deadline')) {
                $table->dropColumn('deadline');
            }
        });
    }
};
