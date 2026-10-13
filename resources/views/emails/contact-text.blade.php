Nouveau message du formulaire de contact
{!! $contact->subjectLabel() !!} · reçu le {{ $contact->created_at->translatedFormat('j F Y à H:i') }}

Nom : {!! $contact->name !!}
E-mail : {!! $contact->email !!}
@if ($contact->phone)
Téléphone : {!! $contact->phone !!}
@endif

{!! $contact->message !!}

--
Répondez directement à cet e-mail : votre réponse partira à {!! $contact->email !!}.
