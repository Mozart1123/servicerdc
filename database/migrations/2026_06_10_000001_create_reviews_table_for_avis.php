<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTableForAvis extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('mission_id')->constrained('missions')->onDelete('cascade');
                $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('artisan_id')->constrained('users')->onDelete('cascade');
                $table->integer('rating')->default(0); // 1-5
                $table->text('feedback')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->string('rejection_reason')->nullable();
                $table->timestamps();
                
                $table->index('mission_id');
                $table->index('client_id');
                $table->index('status');
                $table->unique(['mission_id', 'client_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
}
