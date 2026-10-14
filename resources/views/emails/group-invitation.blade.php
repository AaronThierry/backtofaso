@php
    $group = $invitation->conversation;
    $inviter = $invitation->inviter;
@endphp
@extends('emails.layout')

@section('title', 'Invitation au groupe '.$group->name)
@section('preheader', $inviter->name.' vous invite à rejoindre « '.$group->name.' » sur Back to Burkina')

@section('content')
    <table role="presentation" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td bgcolor="#F0F7FF" style="background-color:#F0F7FF;border:1px solid #DCEBFF;border-radius:999px;padding:6px 14px;font-size:11.5px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#005BBF;">
                Invitation
            </td>
        </tr>
    </table>

    <h1 class="bb-title" style="margin:18px 0 0;font-size:25px;line-height:1.25;font-weight:800;color:#0A1C42;">Rejoignez le groupe « {{ $group->name }} »</h1>
    <p style="margin:12px 0 0;font-size:15.5px;line-height:1.65;color:#53575E;">
        <strong style="color:#2D3037;">{{ $inviter->name }}</strong> vous invite à rejoindre ce groupe {{ $group->is_public ? 'public' : 'privé' }} de la communauté Back to Burkina.
        @if ($group->description)<br><span style="color:#686C73;">{{ $group->description }}</span>@endif
    </p>

    {{-- Bouton --}}
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin-top:28px;">
        <tr>
            <td align="center" bgcolor="#0071E3" style="background-color:#0071E3;background-image:linear-gradient(180deg,#0A84FF,#0071E3);border-radius:12px;">
                <a href="{{ $url }}" target="_blank" style="display:inline-block;padding:15px 30px;font-size:15.5px;font-weight:700;color:#FFFFFF;text-decoration:none;border-radius:12px;">Rejoindre le groupe</a>
            </td>
        </tr>
    </table>

    <p style="margin:22px 0 0;font-size:14.5px;line-height:1.65;color:#53575E;">
        @if ($hasAccount)
            Connectez-vous avec votre compte Back to Burkina, puis confirmez pour entrer dans le groupe.
        @else
            Vous n’avez pas encore de compte : créez-le avec cette adresse e-mail, vous rejoindrez le groupe automatiquement.
        @endif
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:24px;">
        <tr>
            <td bgcolor="#FFF8EC" style="background-color:#FFF8EC;border-left:4px solid #C8922B;border-radius:4px 12px 12px 4px;padding:14px 18px;font-size:13.5px;line-height:1.6;color:#724F06;">
                <strong>Invitation personnelle.</strong> Ce lien ne fonctionne qu’avec le compte {{ $invitation->email }}, une seule fois, jusqu’au {{ $invitation->expires_at->translatedFormat('j F Y') }}.
            </td>
        </tr>
    </table>

    <p style="margin:24px 0 0;font-size:12.5px;line-height:1.6;color:#82868E;">Le bouton ne fonctionne pas ? Copiez ce lien dans votre navigateur :<br><a href="{{ $url }}" style="color:#0071E3;word-break:break-all;">{{ $url }}</a></p>

    <p style="margin:22px 0 0;padding-top:20px;border-top:1px solid #E9EBEF;font-size:13px;line-height:1.65;color:#82868E;">
        Vous ne connaissez pas {{ $inviter->first_name }} ou ne souhaitez pas rejoindre ce groupe ? Ignorez simplement cet e-mail.
    </p>
@endsection
