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
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('organization_id')->nullable()->change();
            
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('mission_id')->nullable()->after('organization_id')->constrained()->nullOnDelete();
            
            $table->string('type')->nullable()->after('currency'); // deposit, payout, refund, subscription
            $table->string('kpay_reference')->nullable()->after('reference_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['mission_id']);
            $table->dropColumn(['user_id', 'mission_id', 'type', 'kpay_reference']);
            
            $table->unsignedBigInteger('organization_id')->nullable(false)->change();
        });
    }
};
