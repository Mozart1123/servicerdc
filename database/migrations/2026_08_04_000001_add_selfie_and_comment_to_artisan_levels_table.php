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
        Schema::table('artisan_levels', function (Blueprint $table) {
            $table->string('selfie_path')->nullable()->after('identity_document_type');
            $table->text('verification_rejection_comment')->nullable()->after('verification_rejection_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artisan_levels', function (Blueprint $table) {
            $table->dropColumn(['selfie_path', 'verification_rejection_comment']);
        });
    }
};
