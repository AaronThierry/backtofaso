<?php

return [
    // Codes de vérification envoyés par e-mail, à l'inscription et à chaque connexion
    'otp' => [
        'length' => 6,
        'ttl_minutes' => 10,
        'max_attempts' => 5,
        'resend_after_seconds' => 60,
        // Affiche le code à l'écran en local pour tester sans boîte mail
        'show_in_dev' => env('OTP_SHOW_IN_DEV', false),
    ],

    // Coordonnées de l'agence (site public, pied de page, page Contact)
    'contact' => [
        // Adresse qui reçoit les messages du formulaire de contact
        'email' => env('CONTACT_EMAIL', 'contact@backtoburkina.bf'),
        'phone' => env('CONTACT_PHONE'),          // ex. +226 70 00 00 00
        'whatsapp' => env('CONTACT_WHATSAPP'),    // numéro au format international, sans espaces : 22670000000
        'address' => env('CONTACT_ADDRESS', 'Bobo-Dioulasso, Burkina Faso'),
        'hours' => env('CONTACT_HOURS'),           // ex. Du lundi au vendredi, 8 h – 17 h
    ],

    'default_country_code' => '+226',

    'cities' => ['Bobo-Dioulasso', 'Ouagadougou', 'Koudougou', 'Banfora', 'Ouahigouya', 'Kaya', 'Fada N’Gourma', 'Dédougou', 'Autre'],

    'uploads' => [
        'max_kb' => 8192,
        'document_mimes' => 'pdf,jpg,jpeg,png',
    ],
];
