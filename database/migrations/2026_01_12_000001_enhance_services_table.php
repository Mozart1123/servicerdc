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
        Schema::table('services', function (Blueprint $table) {
            // Add artisan_id and category_id if they don't exist
            if (!Schema::hasColumn('services', 'artisan_id')) {
                $table->foreignId('artisan_id')->nullable()->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('services', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            }
            if (!Schema::hasColumn('services', 'title')) {
                $table->string('title')->nullable();
            }
            if (!Schema::hasColumn('services', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('services', 'price')) {
                $table->decimal('price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('services', 'location')) {
                $table->string('location')->nullable();
            }
            if (!Schema::hasColumn('services', 'images')) {
                $table->json('images')->nullable();
            }
            if (!Schema::hasColumn('services', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Drop foreign keys and columns
            if (Schema::hasColumn('services', 'artisan_id')) {
                $table->dropForeignIdFor('artisan_id');
                $table->dropColumn('artisan_id');
            }
            if (Schema::hasColumn('services', 'category_id')) {
                $table->dropForeignIdFor('category_id');
                $table->dropColumn('category_id');
            }
        });
    }
};
