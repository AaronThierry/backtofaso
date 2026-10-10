<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Envoi des SMS. Le pilote « log » écrit le message dans storage/logs/laravel.log.
 * Pour la production, ajoutez un pilote ici (API Orange SMS, Twilio, etc.).
 */
class SmsSender
{
    public function send(string $phone, string $message): void
    {
        match (config('backtoburkina.sms.driver')) {
            'log' => Log::info("[SMS → {$phone}] {$message}"),
            default => throw new \RuntimeException('Pilote SMS non configuré : '.config('backtoburkina.sms.driver')),
        };
    }
}
