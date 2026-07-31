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
        if (!Schema::hasTable('wallets')) {
            Schema::create('wallets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
                $table->decimal('balance', 12, 2)->default(0.00);
                $table->decimal('pending_balance', 12, 2)->default(0.00);
                $table->string('currency', 10)->default('USD');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('wallet_transactions')) {
            Schema::create('wallet_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('from_user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('mission_id')->nullable()->constrained('missions')->onDelete('set null');
                $table->enum('type', ['credit', 'debit']);
                $table->decimal('amount', 12, 2);
                $table->decimal('commission_amount', 12, 2)->default(0.00);
                $table->decimal('net_amount', 12, 2);
                $table->string('status', 30)->default('completed');
                $table->string('description')->nullable();
                $table->string('reference_id')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');
    }
};
