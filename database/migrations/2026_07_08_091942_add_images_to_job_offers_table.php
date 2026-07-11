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
        Schema::table('job_offers', function (Blueprint $table) {
            if (!Schema::hasColumn('job_offers', 'company_logo')) {
                $table->string('company_logo')->nullable()->after('company_name');
            }
            if (!Schema::hasColumn('job_offers', 'cover_image')) {
                $table->string('cover_image')->nullable()->after('company_logo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('job_offers', function (Blueprint $table) {
            $table->dropColumn(['company_logo', 'cover_image']);
        });
    }
};
