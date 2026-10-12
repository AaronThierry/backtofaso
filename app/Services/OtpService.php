<?php

namespace App\Services;

use App\Mail\OtpCodeMail;
use App\Models\EmailVerification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

/**
 * Codes de vérification envoyés par e-mail, obligatoires à l'inscription et à chaque connexion.
 * Un code est lié à une adresse et à un motif : un code d'inscription ne permet pas de se connecter.
 */
class OtpService
{
    public const REGISTER = 'register';
    public const LOGIN = 'login';

    public static function normalizeEmail(string $email): string
    {
        return mb_strtolower(trim($email));
    }

    /** « a••••@gmail.com » */
    public static function maskEmail(string $email): string
    {
        [$name, $domain] = explode('@', $email) + [1 => ''];

        return mb_substr($name, 0, 1).str_repeat('•', max(2, mb_strlen($name) - 1)).'@'.$domain;
    }

    /** Génère et envoie un code. Retourne le code (affiché seulement en local). */
    public function send(string $email, string $purpose): string
    {
        $length = config('backtoburkina.otp.length');
        $code = str_pad((string) random_int(0, 10 ** $length - 1), $length, '0', STR_PAD_LEFT);

        EmailVerification::where('email', $email)->where('purpose', $purpose)->whereNull('verified_at')->delete();
        EmailVerification::create([
            'email' => $email,
            'purpose' => $purpose,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(config('backtoburkina.otp.ttl_minutes')),
        ]);

        Mail::to($email)->send(new OtpCodeMail($code, config('backtoburkina.otp.ttl_minutes'), $purpose));

        return $code;
    }

    /** @return true|string  true si valide, sinon le message d'erreur */
    public function verify(string $email, string $purpose, string $code): true|string
    {
        $record = EmailVerification::where('email', $email)->where('purpose', $purpose)
            ->whereNull('verified_at')->latest('id')->first();

        if (! $record) {
            return 'Aucun code en attente. Demandez un nouveau code.';
        }
        if ($record->isExpired()) {
            return 'Ce code a expiré. Demandez un nouveau code.';
        }
        if ($record->attempts >= config('backtoburkina.otp.max_attempts')) {
            return 'Trop de tentatives. Demandez un nouveau code.';
        }
        if (! Hash::check($code, $record->code_hash)) {
            $record->increment('attempts');

            return 'Code incorrect.';
        }

        $record->update(['verified_at' => now()]);

        return true;
    }

    public function secondsBeforeResend(string $email, string $purpose): int
    {
        $last = EmailVerification::where('email', $email)->where('purpose', $purpose)->latest('id')->first();
        if (! $last) {
            return 0;
        }
        $wait = config('backtoburkina.otp.resend_after_seconds') - (int) $last->created_at->diffInSeconds(now());

        return max(0, $wait);
    }
}
