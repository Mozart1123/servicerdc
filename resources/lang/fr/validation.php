<?php

return [
    "accepted" => "Le champ :attribute doit être accepté.",
    "active_url" => "Le champ :attribute n'est pas une URL valide.",
    "after" => "Le champ :attribute doit être une date postérieure au :date.",
    "alpha" => "Le champ :attribute doit seulement contenir des lettres.",
    "array" => "Le champ :attribute doit être un tableau.",
    "before" => "Le champ :attribute doit être une date antérieure au :date.",

    "between" => [
        "numeric" => "Le champ :attribute doit être entre :min et :max.",
        "file" => "Le fichier :attribute doit peser entre :min et :max kilo-octets.",
        "string" => "Le champ :attribute doit contenir entre :min et :max caractères.",
        "array" => "Le champ :attribute doit avoir entre :min et :max éléments."
    ],

    "boolean" => "Le champ :attribute doit être vrai ou faux.",

    "confirmed" => "La confirmation de :attribute ne correspond pas.",

    "date" => "Le champ :attribute n'est pas une date valide.",

    "email" => "Le champ :attribute doit être une adresse email valide.",

    "exists" => "Le champ :attribute sélectionné est invalide.",

    "file" => "Le champ :attribute doit être un fichier.",

    "image" => "Le champ :attribute doit être une image.",

    "max" => [
        "numeric" => "Le champ :attribute ne doit pas être supérieur à :max.",
        "file" => "Le fichier :attribute ne doit pas dépasser :max kilo-octets.",
        "string" => "Le champ :attribute ne doit pas dépasser :max caractères.",
        "array" => "Le tableau :attribute ne doit pas contenir plus de :max éléments."
    ],

    "mimes" => "Le champ :attribute doit être un fichier de type : :values.",

    "required" => "Le champ :attribute est obligatoire.",

    "string" => "Le champ :attribute doit être une chaîne de caractères.",

    // ... add other rules as needed

    'attributes' => [
        'cover_image' => 'image de couverture',
        'company_logo' => "logo de l'entreprise",
        'profile_photo' => 'photo de profil',
        'image' => 'image',
        'attachment' => 'fichier joint',
        'resume_url' => 'CV',
    ],
];
