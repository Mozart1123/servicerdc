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
        Schema::table('service_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'service_id')) {
                $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('cascade');
            }
            if (!Schema::hasColumn('service_requests', 'location')) {
                $table->string('location')->nullable();
            }
            if (!Schema::hasColumn('service_requests', 'notes')) {
                $table->text('notes')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (Schema::hasColumn('service_requests', 'service_id')) {
                $table->dropForeignIdFor('service_id');
                $table->dropColumn('service_id');
            }
            if (Schema::hasColumn('service_requests', 'location')) {
                $table->dropColumn('location');
            }
            if (Schema::hasColumn('service_requests', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
