<?php

namespace App\Support;

/** Numéros de téléphone (coordonnées de contact, pas d'authentification) */
class Phone
{
    /** +226 + « 70 00 00 00 » → +22670000000 */
    public static function normalize(string $phone, ?string $countryCode = null): string
    {
        $phone = preg_replace('/[^\d+]/', '', $phone);
        if (str_starts_with($phone, '00')) {
            $phone = '+'.substr($phone, 2);
        }
        if (! str_starts_with($phone, '+')) {
            $phone = ($countryCode ?? config('backtoburkina.default_country_code')).ltrim($phone, '0');
        }

        return $phone;
    }

    public static function format(?string $phone): string
    {
        if ($phone && preg_match('/^\+226(\d{2})(\d{2})(\d{2})(\d{2})$/', $phone, $m)) {
            return "+226 {$m[1]} {$m[2]} {$m[3]} {$m[4]}";
        }

        return (string) $phone;
    }
}
