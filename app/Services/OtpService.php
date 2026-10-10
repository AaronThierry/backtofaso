<?php

namespace App\Services;

use App\Models\PhoneVerification;
use Illuminate\Support\Facades\Hash;

class OtpService
{
    public function __construct(private SmsSender $sms) {}

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

    public static function format(string $phone): string
    {
        if (preg_match('/^\+226(\d{2})(\d{2})(\d{2})(\d{2})$/', $phone, $m)) {
            return "+226 {$m[1]} {$m[2]} {$m[3]} {$m[4]}";
        }

        return $phone;
    }

    /** Génère et envoie un code. Retourne le code (affiché seulement en local). */
    public function send(string $phone): string
    {
        $length = config('backtoburkina.otp.length');
        $code = str_pad((string) random_int(0, 10 ** $length - 1), $length, '0', STR_PAD_LEFT);

        PhoneVerification::where('phone', $phone)->whereNull('verified_at')->delete();
        PhoneVerification::create([
            'phone' => $phone,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(config('backtoburkina.otp.ttl_minutes')),
        ]);

        $this->sms->send($phone, "Back to Burkina : votre code est {$code}. Il expire dans ".config('backtoburkina.otp.ttl_minutes').' minutes.');

        return $code;
    }

    /** @return true|string  true si valide, sinon le message d'erreur */
    public function verify(string $phone, string $code): true|string
    {
        $record = PhoneVerification::where('phone', $phone)->whereNull('verified_at')->latest()->first();

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

    public function secondsBeforeResend(string $phone): int
    {
        $last = PhoneVerification::where('phone', $phone)->latest()->first();
        if (! $last) {
            return 0;
        }
        $wait = config('backtoburkina.otp.resend_after_seconds') - (int) $last->created_at->diffInSeconds(now());

        return max(0, $wait);
    }
}
