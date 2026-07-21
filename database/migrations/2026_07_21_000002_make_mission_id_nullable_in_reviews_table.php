<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Drop constraints that prevent changing mission_id to nullable
            $table->dropForeign(['mission_id']);
            $table->dropUnique(['mission_id', 'client_id']);
            
            // Make mission_id nullable
            $table->unsignedBigInteger('mission_id')->nullable()->change();
            
            // Add service_request_id to link reviews directly to service requests
            $table->foreignId('service_request_id')->nullable()->after('mission_id')->constrained('service_requests')->onDelete('cascade');
            
            // Re-add foreign key for mission_id
            $table->foreign('mission_id')->references('id')->on('missions')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['service_request_id']);
            $table->dropColumn('service_request_id');
            
            // Note: reverting mission_id to non-nullable might fail if there are nulls.
            // We just leave it nullable in down or force it if needed.
            // To be safe, we just drop the added column in down().
        });
    }
};
