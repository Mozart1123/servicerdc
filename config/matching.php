<?php

/*
|--------------------------------------------------------------------------
| Matching des demandes publiées ("Décrire mon besoin")
|--------------------------------------------------------------------------
| Valeurs par défaut. Les pondérations du score sont modifiables dans l'admin
| (table settings, clés "matching.weight.*") ; ces valeurs servent de repli.
*/

return [

    // Score de pertinence sur 100 (cahier des charges §2.2).
    'weights' => [
        'rating'         => 30, // note ajustée (moyenne bayésienne)
        'proximity'      => 20, // même commune / voisine / zone élargie
        'budget'         => 15, // prix habituel vs budget du client
        'responsiveness' => 15, // taux et délai de réponse sur 30 jours
        'reliability'    => 10, // missions terminées / acceptées
        'newcomer'       => 10, // bonus nouvel artisan
    ],

    // Moyenne bayésienne : poids de la moyenne plateforme, et repli sans aucun avis.
    'bayes_prior_weight'   => 5,
    'default_platform_avg' => 4.0,

    // Filtres éliminatoires.
    'max_active_jobs' => 3, // un artisan avec 3 demandes/missions en cours ne reçoit plus rien

    // Statuts qui comptent comme "en cours" pour l'artisan.
    'active_statuses' => ['accepted', 'in_progress', 'awaiting_validation'],

    // Fenêtre de calcul de la réactivité (jours).
    'responsiveness_window_days' => 30,

    // Heure locale pour "disponible tout de suite" (horaires de travail).
    'timezone' => 'Africa/Kinshasa',

    // Interrupteur général de l'envoi aux artisans. Désactivé tant que les artisans
    // ne peuvent pas encore répondre (proposition / refus) : sinon ils seraient
    // pénalisés en réactivité sans pouvoir agir. Activer via MATCHING_DISPATCH_ENABLED=true.
    'dispatch_enabled' => (bool) env('MATCHING_DISPATCH_ENABLED', false),

    // Envoi par vagues (§2.3).
    'waves' => [
        'size'               => 5,   // artisans contactés par vague
        'deadline_minutes'   => 120, // délai de réponse en temps normal
        'urgent_minutes'     => 30,  // délai de réponse pour une demande urgente
        'min_proposals'      => 3,   // en dessous, vague suivante à la fin du délai
        'max_proposals'      => 5,   // au-delà, on arrête les envois
    ],

];
