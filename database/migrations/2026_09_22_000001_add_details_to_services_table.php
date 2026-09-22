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
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'pricing_type')) {
                $table->string('pricing_type', 30)->default('fixed')->after('price');
            }
            if (!Schema::hasColumn('services', 'duration')) {
                $table->string('duration', 50)->nullable()->after('pricing_type');
            }
            if (!Schema::hasColumn('services', 'service_location')) {
                $table->string('service_location', 50)->nullable()->after('duration');
            }
            if (!Schema::hasColumn('services', 'availability')) {
                $table->string('availability', 50)->nullable()->after('service_location');
            }
            if (!Schema::hasColumn('services', 'min_notice')) {
                $table->string('min_notice', 50)->nullable()->after('availability');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'pricing_type',
                'duration',
                'service_location',
                'availability',
                'min_notice',
            ]);
        });
    }
};
