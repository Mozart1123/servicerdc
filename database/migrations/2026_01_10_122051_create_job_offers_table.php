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
        Schema::create('job_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link to Admin/User
            $table->string('title');
            $table->string('company_name');
            $table->string('logo_url')->nullable();
            $table->string('location');
            $table->string('category');
            $table->string('contract_type'); // Full-time, Part-time, Freelance
            $table->string('salary_range')->nullable();
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->enum('status', ['active', 'expired'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_offers');
    }
};
