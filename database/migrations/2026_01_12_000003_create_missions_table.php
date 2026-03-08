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
        if (!Schema::hasTable('missions')) {
            Schema::create('missions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('cascade');
                $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('artisan_id')->constrained('users')->onDelete('cascade');
                $table->string('title');
                $table->text('description')->nullable();
                $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->decimal('amount', 10, 2)->nullable();
                $table->integer('rating')->nullable();
                $table->text('feedback')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missions');
    }
};
