<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Provinces de la RDC (liste canonique, 26 provinces post-2015)
    |--------------------------------------------------------------------------
    |
    | Source unique de vérité pour toute fonctionnalité liée à la province
    | déclarée par un utilisateur (formulaire CV, agrégats admin, carte
    | géographique, filtres). Ne pas dupliquer cette liste ailleurs — les
    | anciens noms de provinces (avant le redécoupage de 2015, ex. "Bas-Congo",
    | "Bandundu", "Katanga", "Kasaï-Occidental/Oriental" au sens ancien) ne
    | doivent pas y figurer : ils ne correspondent à aucune des 26 provinces
    | actuelles et cassent les agrégats par province (carte, statistiques).
    |
    */

    'provinces' => [
        'Bas-Uele', 'Équateur', 'Haut-Katanga', 'Haut-Lomami', 'Haut-Uele',
        'Ituri', 'Kasaï', 'Kasaï Central', 'Kasaï Oriental', 'Kinshasa',
        'Kongo Central', 'Kwango', 'Kwilu', 'Lomami', 'Lualaba', 'Mai-Ndombe',
        'Maniema', 'Mongala', 'Nord-Kivu', 'Nord-Ubangi', 'Sankuru',
        'Sud-Kivu', 'Sud-Ubangi', 'Tanganyika', 'Tshopo', 'Tshuapa',
    ],

];
