@extends('emails.layout')

@section('title', $heading)
@section('preheader', 'Votre code : '.$code.' · valable '.$minutes.' minutes')

@section('content')
    {{-- Pastille du motif --}}
    <table role="presentation" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td bgcolor="#F0F7FF" style="background-color:#F0F7FF;border:1px solid #DCEBFF;border-radius:999px;padding:6px 14px;font-size:11.5px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#005BBF;">
                {{ $badge }}
            </td>
        </tr>
    </table>

    <h1 class="bb-title" style="margin:18px 0 0;font-size:25px;line-height:1.25;font-weight:800;color:#0A1C42;letter-spacing:-0.3px;">{{ $heading }}</h1>
    <p style="margin:12px 0 0;font-size:15.5px;line-height:1.65;color:#53575E;">{{ $intro }}</p>

    {{-- Code --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:26px;">
        <tr>
            <td align="center" bgcolor="#F4F7FB" style="background-color:#F4F7FB;border:1px solid #E9EBEF;border-radius:16px;padding:26px 16px 22px;">
                <div style="font-size:11.5px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#82868E;">Votre code</div>
                <div class="bb-code" style="padding-top:10px;font-family:'SFMono-Regular',Consolas,'Liberation Mono',Menlo,monospace;font-size:38px;line-height:1.1;font-weight:700;letter-spacing:12px;color:#0A1C42;mso-line-height-rule:exactly;">{{ $code }}</div>
                <div style="padding-top:14px;font-size:13px;color:#686C73;">
                    Valable <strong style="color:#2D3037;">{{ $minutes }} minutes</strong>
                </div>
            </td>
        </tr>
    </table>

    <p style="margin:24px 0 0;font-size:14.5px;line-height:1.65;color:#53575E;">{{ $instructions }}</p>

    {{-- Conseil de sécurité --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:26px;">
        <tr>
            <td bgcolor="#FFF8EC" style="background-color:#FFF8EC;border-left:4px solid #C8922B;border-radius:4px 12px 12px 4px;padding:14px 18px;font-size:13.5px;line-height:1.6;color:#724F06;">
                <strong>Ne communiquez jamais ce code.</strong> L’équipe Travel Express ne vous le demandera jamais, ni par téléphone, ni par message.
            </td>
        </tr>
    </table>

    <p style="margin:26px 0 0;padding-top:22px;border-top:1px solid #E9EBEF;font-size:13px;line-height:1.65;color:#82868E;">
        {{ $ignore }}
    </p>
@endsection
