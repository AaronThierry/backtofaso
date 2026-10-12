<?php

namespace App\Services;

use App\Models\PhoneVerification;
use App\Mail\OtpCodeMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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

    /** Canal utilisé pour une personne : e-mail si configuré et si elle a une adresse, sinon SMS */
    public static function channelFor(?string $email): string
    {
        return config('backtoburkina.otp.channel') === 'mail' && $email ? 'mail' : 'sms';
    }

    public static function enabled(): bool
    {
        return config('backtoburkina.otp.enabled');
    }

    public static function usesMail(): bool
    {
        return self::enabled() && config('backtoburkina.otp.channel') === 'mail';
    }

    /** « a****@gmail.com » */
    public static function maskEmail(string $email): string
    {
        [$name, $domain] = explode('@', $email) + [1 => ''];

        return mb_substr($name, 0, 1).str_repeat('•', max(2, mb_strlen($name) - 1)).'@'.$domain;
    }

    /**
     * Génère et envoie un code (par e-mail ou SMS). Le code est rattaché au numéro de téléphone.
     * Retourne le code (affiché seulement en local).
     */
    public function send(string $phone, ?string $email = null): string
    {
        $length = config('backtoburkina.otp.length');
        $code = str_pad((string) random_int(0, 10 ** $length - 1), $length, '0', STR_PAD_LEFT);

        PhoneVerification::where('phone', $phone)->whereNull('verified_at')->delete();
        PhoneVerification::create([
            'phone' => $phone,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(config('backtoburkina.otp.ttl_minutes')),
        ]);

        if (self::channelFor($email) === 'mail') {
            Mail::to($email)->send(new OtpCodeMail($code, config('backtoburkina.otp.ttl_minutes')));
        } else {
            $this->sms->send($phone, "Back to Burkina : votre code est {$code}. Il expire dans ".config('backtoburkina.otp.ttl_minutes').' minutes.');
        }

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
