<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artisan_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('level', ['nouveau', 'actif', 'verifie', 'elite'])->default('nouveau');
            $table->unsignedInteger('total_missions')->default(0);
            $table->unsignedInteger('missions_via_platform')->default(0);
            $table->decimal('average_rating', 2, 1)->default(0.0);
            $table->unsignedInteger('warning_count')->default(0);
            $table->timestamp('visibility_penalty_until')->nullable();
            $table->timestamp('level_updated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artisan_levels');
    }
};
