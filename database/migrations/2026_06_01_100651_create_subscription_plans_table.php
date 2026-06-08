<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * This file is a duplicate stub — the real subscription_plans migration is
 * 2026_06_01_091446_create_subscription_plans_table.php
 * This one is kept as a no-op to avoid breaking the migrations table.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Skip: table already created by the 091446 migration
        if (Schema::hasTable('subscription_plans')) {
            return;
        }

        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // No-op: managed by the 091446 migration
    }
};
