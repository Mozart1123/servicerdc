<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_request_work_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_request_id')->constrained()->onDelete('cascade');
            // dateTime(), not timestamp(): a MySQL TIMESTAMP column with no
            // explicit DEFAULT implicitly gets DEFAULT CURRENT_TIMESTAMP ON
            // UPDATE CURRENT_TIMESTAMP when it's the first such column in the
            // table (explicit_defaults_for_timestamp off) — every UPDATE to
            // the row (e.g. setting ended_at on pause) would then silently
            // overwrite started_at with the DB server's current time. DATETIME
            // has no such auto-update behavior.
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable(); // null = session still active (not paused yet)
            $table->timestamps();

            $table->index(['service_request_id', 'started_at'], 'sr_work_sessions_sr_id_started_at_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_request_work_sessions');
    }
};
