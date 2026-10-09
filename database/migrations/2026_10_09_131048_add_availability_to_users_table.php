<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'availability_days')) {
                // JSON array of day slugs: ["lun","mar","mer","jeu","ven","sam","dim"]
                $table->json('availability_days')->nullable()->after('home_service');
            }
            if (!Schema::hasColumn('users', 'availability_note')) {
                $table->string('availability_note', 255)->nullable()->after('availability_days');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumnIfExists('availability_days');
            $table->dropColumnIfExists('availability_note');
        });
    }
};
