<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('missions', function (Blueprint $table) {
            $table->enum('payment_channel', ['platform', 'cash'])->nullable()->after('amount');
            $table->decimal('commission_amount', 10, 2)->nullable()->after('payment_channel');
            $table->enum('commission_status', ['pending', 'paid'])->nullable()->after('commission_amount');
            $table->timestamp('contact_unlocked_at')->nullable()->after('commission_status');
        });
    }

    public function down(): void
    {
        Schema::table('missions', function (Blueprint $table) {
            $table->dropColumn(['payment_channel', 'commission_amount', 'commission_status', 'contact_unlocked_at']);
        });
    }
};
