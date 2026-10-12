<?php

return [
    'sms' => [
        // log | (à brancher : orange, twilio…)
        'driver' => env('SMS_DRIVER', 'log'),
        'sender' => env('SMS_SENDER_NAME', 'BackToBF'),
    ],

    'otp' => [
        // false : aucune vérification par code (inscription directe, connexion par mot de passe uniquement)
        'enabled' => (bool) env('OTP_ENABLED', false),
        // mail : code envoyé par e-mail (adresse obligatoire à l'inscription) | sms : code envoyé par SMS
        'channel' => env('OTP_CHANNEL', 'mail'),
        'length' => 6,
        'ttl_minutes' => 10,
        'max_attempts' => 5,
        'resend_after_seconds' => 60,
        // Affiche le code à l'écran en local pour tester sans téléphone
        'show_in_dev' => env('OTP_SHOW_IN_DEV', false),
    ],

    'default_country_code' => '+226',

    'cities' => ['Bobo-Dioulasso', 'Ouagadougou', 'Koudougou', 'Banfora', 'Ouahigouya', 'Kaya', 'Fada N’Gourma', 'Dédougou', 'Autre'],

    'uploads' => [
        'max_kb' => 8192,
        'document_mimes' => 'pdf,jpg,jpeg,png',
    ],
];
