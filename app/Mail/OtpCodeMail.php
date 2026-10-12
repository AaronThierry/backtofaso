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
        return new Content(view: 'emails.otp', text: 'emails.otp-text', with: [
            'intro' => $this->purpose === OtpService::LOGIN
                ? 'Voici votre code pour vous connecter à votre espace Back to Burkina :'
                : 'Bienvenue ! Voici votre code pour confirmer votre adresse e-mail et créer votre compte :',
        ]);
    }
}
