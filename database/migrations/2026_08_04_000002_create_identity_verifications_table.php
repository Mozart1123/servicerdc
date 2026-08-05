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
        Schema::create('identity_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('identity_document_type', ['voter_card', 'passport', 'national_id']);
            $table->string('identity_document_path');
            $table->string('selfie_path');
            $table->enum('verification_status', ['not_submitted', 'pending', 'approved', 'rejected'])->default('not_submitted');
            $table->string('verification_rejection_reason')->nullable();
            $table->text('verification_rejection_comment')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('identity_verifications');
    }
};
