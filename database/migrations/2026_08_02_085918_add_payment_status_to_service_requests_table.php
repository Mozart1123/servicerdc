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
            if (!Schema::hasColumn('service_requests', 'payment_status')) {
                $table->string('payment_status')->default('unpaid')->after('status');
            }
            if (!Schema::hasColumn('service_requests', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (Schema::hasColumn('service_requests', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
            if (Schema::hasColumn('service_requests', 'paid_at')) {
                $table->dropColumn('paid_at');
            }
        });
    }
};
