<?php

namespace App\Mail;

use App\Models\GroupInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Invitation à rejoindre un groupe, avec un lien personnel */
class GroupInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public GroupInvitation $invitation, public string $url, public bool $hasAccount) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->invitation->inviter->first_name.' vous invite dans le groupe « '.$this->invitation->conversation->name.' »');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.group-invitation', text: 'emails.group-invitation-text');
    }
}
