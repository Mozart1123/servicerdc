<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Photo de couverture affichée en bannière sur le profil public de
     * l'artisan (remplace le dégradé par défaut quand elle est définie).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'cover_photo')) {
                $table->string('cover_photo')->nullable()->after('profile_photo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'cover_photo')) {
                $table->dropColumn('cover_photo');
            }
        });
    }
};
