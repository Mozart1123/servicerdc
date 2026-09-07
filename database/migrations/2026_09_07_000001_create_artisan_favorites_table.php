<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Un client peut ajouter un artisan à ses favoris (bouton cœur sur le
     * profil public de l'artisan). Une paire (user_id, artisan_id) ne peut
     * exister qu'une seule fois.
     */
    public function up(): void
    {
        Schema::create('artisan_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('artisan_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'artisan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artisan_favorites');
    }
};
