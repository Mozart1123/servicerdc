<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_available')) {
                $table->boolean('is_available')->default(true)->after('availability_note');
            }
            if (!Schema::hasColumn('users', 'available_until')) {
                $table->date('available_until')->nullable()->after('is_available');
            }
            if (!Schema::hasColumn('users', 'working_hours_start')) {
                $table->string('working_hours_start', 10)->nullable()->default('08:00')->after('available_until');
            }
            if (!Schema::hasColumn('users', 'working_hours_end')) {
                $table->string('working_hours_end', 10)->nullable()->default('18:00')->after('working_hours_start');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumnIfExists('is_available');
            $table->dropColumnIfExists('available_until');
            $table->dropColumnIfExists('working_hours_start');
            $table->dropColumnIfExists('working_hours_end');
        });
    }
};
