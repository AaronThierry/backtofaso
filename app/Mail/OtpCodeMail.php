<?php

namespace App\Mail;

use App\Services\OtpService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $code, public int $minutes, public string $purpose = OtpService::REGISTER) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->purpose === OtpService::LOGIN
            ? $this->code.' : votre code de connexion Back to Burkina'
            : $this->code.' : confirmez votre adresse e-mail');
    }

    public function content(): Content
    {
        $login = $this->purpose === OtpService::LOGIN;

        return new Content(view: 'emails.otp', text: 'emails.otp-text', with: $login ? [
            'badge' => 'Connexion',
            'heading' => 'Votre code de connexion',
            'intro' => 'Une connexion à votre espace Back to Burkina vient d’être demandée. Pour la confirmer, utilisez le code ci-dessous.',
            'instructions' => 'Saisissez-le sur la page de connexion pour accéder à votre espace : dossier, messages et communauté.',
            'ignore' => 'Vous n’êtes pas à l’origine de cette connexion ? Quelqu’un connaît peut-être votre mot de passe : changez-le dès que possible et prévenez votre conseiller.',
        ] : [
            'badge' => 'Création de compte',
            'heading' => 'Bienvenue sur Back to Burkina',
            'intro' => 'Merci de nous rejoindre ! Il ne reste qu’une étape : confirmer votre adresse e-mail avec le code ci-dessous.',
            'instructions' => 'Saisissez-le sur la page d’inscription pour créer votre compte, puis complétez votre profil en quelques clics.',
            'ignore' => 'Vous n’avez pas demandé à créer de compte ? Ignorez simplement cet e-mail : aucun compte ne sera créé sans ce code.',
        ]);
    }
}
