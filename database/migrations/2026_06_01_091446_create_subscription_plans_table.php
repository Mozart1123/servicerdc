<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');                         // Starter, Pro, Elite
            $table->string('slug')->unique();               // starter, pro, elite
            $table->text('description')->nullable();
            $table->decimal('price_monthly', 10, 2); 
            $table->decimal('price_yearly', 10, 2);
            $table->string('currency', 10)->default('USD');
            $table->json('features');                       // list of features
            $table->string('color')->default('blue');       // UI accent color
            $table->string('icon')->default('fa-star');
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('max_services')->default(1);    // max services allowed
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
