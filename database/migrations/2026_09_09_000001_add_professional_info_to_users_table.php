<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute les champs "informations professionnelles" demandés par le client
 * pour le profil artisan : années d'expérience, langues parlées, zone
 * d'intervention, déplacement à domicile (oui/non), et une adresse
 * optionnelle. Tous nullable — retrocompatible avec les artisans existants
 * qui n'ont pas encore rempli ces champs (aucune valeur par défaut inventée).
 *
 * Le téléphone existe déjà (`users.phone`) et n'est PAS touché ici : il
 * reste privé, non affiché sur la fiche publique (décision produit —
 * voir la fiche publique de l'artisan, onglet "À propos").
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedSmallInteger('years_experience')->nullable()->after('bio');
            $table->json('languages')->nullable()->after('years_experience');
            $table->string('intervention_zone')->nullable()->after('languages');
            $table->boolean('home_service')->nullable()->after('intervention_zone');
            $table->string('address')->nullable()->after('home_service');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['years_experience', 'languages', 'intervention_zone', 'home_service', 'address']);
        });
    }
};
