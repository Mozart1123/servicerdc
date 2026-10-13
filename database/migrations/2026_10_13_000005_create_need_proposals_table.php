<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Réponse d'un artisan à une demande publiée (cahier des charges §2.4) :
 * - type "price" : prix, date d'intervention, message ;
 * - type "visit" : visite (métiers sur devis) avec date/heure et frais d'étude,
 *   puis devis envoyé après la visite (main d'œuvre, matériaux, délai, étapes de paiement).
 * Montants en dollars, comme les prix des services.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('need_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_request_id')->constrained('service_requests')->cascadeOnDelete();
            $table->foreignId('artisan_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('need_dispatch_id')->nullable()->constrained('need_dispatches')->nullOnDelete();
            $table->string('type', 10); // price | visit
            // pending : en attente du client | visit_accepted : visite acceptée, devis attendu
            // accepted : retenue (prix verrouillé) | rejected : non retenue
            $table->string('status', 20)->default('pending');

            $table->decimal('price', 10, 2)->nullable();
            $table->date('intervention_date')->nullable();
            $table->string('message', 500)->nullable();

            $table->dateTime('visit_at')->nullable();
            $table->decimal('study_fee', 10, 2)->nullable();

            $table->decimal('quote_labor', 10, 2)->nullable();
            $table->decimal('quote_materials', 10, 2)->nullable();
            $table->string('quote_duration', 100)->nullable();
            $table->json('quote_payment_steps')->nullable();
            $table->string('quote_message', 500)->nullable();
            $table->dateTime('quote_sent_at')->nullable();

            $table->timestamps();

            $table->unique(['service_request_id', 'artisan_id']);
            $table->index(['service_request_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('need_proposals');
    }
};
