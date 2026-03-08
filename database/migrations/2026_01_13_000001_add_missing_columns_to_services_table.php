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
        // Check if services table exists and update it
        if (Schema::hasTable('services')) {
            Schema::table('services', function (Blueprint $table) {
                // Ajouter les colonnes manquantes
                if (!Schema::hasColumn('services', 'artisan_id')) {
                    $table->foreignId('artisan_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
                }
                if (!Schema::hasColumn('services', 'category_id')) {
                    $table->foreignId('category_id')->nullable()->after('artisan_id')->constrained('categories')->onDelete('set null');
                }
                if (!Schema::hasColumn('services', 'title')) {
                    $table->string('title')->nullable()->after('category_id');
                }
                if (!Schema::hasColumn('services', 'description')) {
                    $table->text('description')->nullable()->after('title');
                }
                if (!Schema::hasColumn('services', 'price')) {
                    $table->decimal('price', 10, 2)->nullable()->after('description');
                }
                if (!Schema::hasColumn('services', 'location')) {
                    $table->string('location')->nullable()->after('price');
                }
                if (!Schema::hasColumn('services', 'images')) {
                    $table->json('images')->nullable()->after('location');
                }
                if (!Schema::hasColumn('services', 'status')) {
                    $table->enum('status', ['active', 'inactive'])->default('active')->after('images');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('services')) {
            Schema::table('services', function (Blueprint $table) {
                if (Schema::hasColumn('services', 'artisan_id')) {
                    $table->dropForeignIdFor('artisan_id');
                    $table->dropColumn('artisan_id');
                }
                if (Schema::hasColumn('services', 'category_id')) {
                    $table->dropForeignIdFor('category_id');
                    $table->dropColumn('category_id');
                }
                if (Schema::hasColumn('services', 'title')) {
                    $table->dropColumn('title');
                }
                if (Schema::hasColumn('services', 'description')) {
                    $table->dropColumn('description');
                }
                if (Schema::hasColumn('services', 'price')) {
                    $table->dropColumn('price');
                }
                if (Schema::hasColumn('services', 'location')) {
                    $table->dropColumn('location');
                }
                if (Schema::hasColumn('services', 'images')) {
                    $table->dropColumn('images');
                }
                if (Schema::hasColumn('services', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }
    }
};
