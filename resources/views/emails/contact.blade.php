@extends('emails.layout')

@section('title', 'Nouveau message')
@section('preheader', $contact->name.' : '.\Illuminate\Support\Str::limit($contact->message, 80))

@section('content')
    <table role="presentation" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td bgcolor="#F0F7FF" style="background-color:#F0F7FF;border:1px solid #DCEBFF;border-radius:999px;padding:6px 14px;font-size:11.5px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#005BBF;">
                Formulaire de contact
            </td>
        </tr>
    </table>

    <h1 class="bb-title" style="margin:18px 0 0;font-size:24px;line-height:1.25;font-weight:800;color:#0A1C42;">{{ $contact->subjectLabel() }}</h1>
    <p style="margin:8px 0 0;font-size:14px;color:#82868E;">Reçu le {{ $contact->created_at->translatedFormat('j F Y à H:i') }}</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:22px;border:1px solid #E9EBEF;border-radius:14px;">
        @foreach (array_filter(['Nom' => $contact->name, 'E-mail' => $contact->email, 'Téléphone' => $contact->phone]) as $label => $value)
            <tr>
                <td style="padding:12px 16px;font-size:13px;color:#82868E;width:110px;{{ $loop->last ? '' : 'border-bottom:1px solid #E9EBEF;' }}">{{ $label }}</td>
                <td style="padding:12px 16px;font-size:14.5px;font-weight:600;color:#2D3037;{{ $loop->last ? '' : 'border-bottom:1px solid #E9EBEF;' }}">
                    @if ($label === 'E-mail')
                        <a href="mailto:{{ $value }}" style="color:#0071E3;text-decoration:none;">{{ $value }}</a>
                    @else
                        {{ $value }}
                    @endif
                </td>
            </tr>
        @endforeach
    </table>

    <div style="margin-top:22px;font-size:11.5px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#82868E;">Message</div>
    <div style="margin-top:8px;background-color:#F4F7FB;border-radius:14px;padding:18px 20px;font-size:15px;line-height:1.65;color:#2D3037;white-space:pre-line;">{{ $contact->message }}</div>

    <p style="margin:24px 0 0;font-size:13.5px;line-height:1.6;color:#686C73;">Répondez directement à cet e-mail : votre réponse partira à {{ $contact->email }}.</p>
@endsection
