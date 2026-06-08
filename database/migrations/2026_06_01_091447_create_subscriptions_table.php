<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                if (!Schema::hasColumn('subscriptions', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                }
                if (!Schema::hasColumn('subscriptions', 'subscription_plan_id')) {
                    $table->foreignId('subscription_plan_id')->nullable()->after('user_id')->constrained()->onDelete('cascade');
                }
                if (!Schema::hasColumn('subscriptions', 'billing_cycle')) {
                    $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly')->after('status');
                }
                if (!Schema::hasColumn('subscriptions', 'payment_method')) {
                    $table->enum('payment_method', ['mobile_money', 'visa_mastercard', 'cash'])->nullable()->after('billing_cycle');
                }
                if (!Schema::hasColumn('subscriptions', 'payment_phone')) {
                    $table->string('payment_phone', 20)->nullable()->after('payment_method');
                }
                if (!Schema::hasColumn('subscriptions', 'transaction_ref')) {
                    $table->string('transaction_ref', 100)->nullable()->after('payment_phone');
                }
                if (!Schema::hasColumn('subscriptions', 'amount_paid')) {
                    $table->decimal('amount_paid', 10, 2)->nullable()->after('transaction_ref');
                }
                if (!Schema::hasColumn('subscriptions', 'paid_at')) {
                    $table->timestamp('paid_at')->nullable()->after('ends_at');
                }
                if (!Schema::hasColumn('subscriptions', 'meta')) {
                    $table->json('meta')->nullable()->after('paid_at');
                }

                // Change status enum to be compatible or make B2B fields nullable
                $table->string('status')->default('pending')->change();
                $table->foreignId('organization_id')->nullable()->change();
                $table->foreignId('plan_id')->nullable()->change();
            });
        } else {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('subscription_plan_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('organization_id')->nullable()->constrained()->cascadeOnDelete();
                $table->foreignId('plan_id')->nullable()->constrained()->cascadeOnDelete();
                $table->string('status')->default('pending');
                $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
                $table->enum('payment_method', ['mobile_money', 'visa_mastercard', 'cash'])->nullable();
                $table->string('payment_phone', 20)->nullable();
                $table->string('transaction_ref', 100)->nullable();
                $table->decimal('amount_paid', 10, 2)->nullable();
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->timestamp('canceled_at')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
