<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Photos de réalisations qu'un artisan publie sur son profil public
     * (onglet "Réalisations") — une sorte de vitrine de ses travaux passés.
     */
    public function up(): void
    {
        Schema::create('artisan_realisations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artisan_id')->constrained('users')->onDelete('cascade');
            $table->string('image_path');
            $table->string('caption')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artisan_realisations');
    }
};
