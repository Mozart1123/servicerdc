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
            if (!Schema::hasColumn('job_offers', 'start_date')) {
                $table->date('start_date')->nullable()->after('category');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_offers', function (Blueprint $table) {
            if (Schema::hasColumn('job_offers', 'start_date')) {
                $table->dropColumn('start_date');
            }
        });
    }
};
