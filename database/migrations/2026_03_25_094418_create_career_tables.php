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
        // Add skills and interests to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'skills')) {
                $table->json('skills')->nullable()->after('status');
            }
            if (!Schema::hasColumn('users', 'interests')) {
                $table->json('interests')->nullable()->after('skills');
            }
        });

        // Create career_paths table
        Schema::create('career_paths', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('required_skills'); // array of skills
            $table->json('interests_match'); // array of interests
            $table->string('salary_range')->nullable();
            $table->string('industry')->nullable();
            $table->timestamps();
        });

        // Create career_recommendations table
        Schema::create('career_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('career_path_id')->constrained('career_paths')->onDelete('cascade');
            $table->decimal('match_score', 5, 2); // 0-100 percentage
            $table->text('analysis')->nullable(); // reasoning for recommendation
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('career_recommendations');
        Schema::dropIfExists('career_paths');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['skills', 'interests']);
        });
    }
};
