<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('missions', function (Blueprint $table) {
            $table->foreignId('service_request_id')->nullable()->after('id')->constrained('service_requests')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('missions', function (Blueprint $table) {
            $table->dropForeign(['service_request_id']);
            $table->dropColumn('service_request_id');
        });
    }
};
