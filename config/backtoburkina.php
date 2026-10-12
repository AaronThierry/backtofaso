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

    'default_country_code' => '+226',

    'cities' => ['Bobo-Dioulasso', 'Ouagadougou', 'Koudougou', 'Banfora', 'Ouahigouya', 'Kaya', 'Fada N’Gourma', 'Dédougou', 'Autre'],

    'uploads' => [
        'max_kb' => 8192,
        'document_mimes' => 'pdf,jpg,jpeg,png',
    ],
];
