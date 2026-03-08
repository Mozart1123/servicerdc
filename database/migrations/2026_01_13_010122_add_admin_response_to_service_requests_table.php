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
        Schema::table('service_requests', function (Blueprint $table) {
            // Add admin_response column if it doesn't exist
            if (!Schema::hasColumn('service_requests', 'admin_response')) {
                $table->text('admin_response')->nullable()->after('response');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (Schema::hasColumn('service_requests', 'admin_response')) {
                $table->dropColumn('admin_response');
            }
        });
    }
};
