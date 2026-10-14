BACK TO BURKINA · par Travel Express
====================================

{!! $invitation->inviter->name !!} vous invite à rejoindre le groupe « {!! $invitation->conversation->name !!} » sur Back to Burkina.

Rejoindre le groupe :
{!! $url !!}

@if ($hasAccount)
Connectez-vous avec votre compte, puis confirmez pour entrer dans le groupe.
@else
Vous n'avez pas encore de compte : créez-le avec cette adresse e-mail, vous rejoindrez le groupe automatiquement.
@endif

Invitation personnelle : ce lien ne fonctionne qu'avec le compte {!! $invitation->email !!}, une seule fois, jusqu'au {{ $invitation->expires_at->translatedFormat('j F Y') }}.

Vous ne connaissez pas cette personne ? Ignorez simplement cet e-mail.

--
Back to Burkina · Étudier à l’étranger, revenir bâtir le Faso.
{{ config('app.url') }}
