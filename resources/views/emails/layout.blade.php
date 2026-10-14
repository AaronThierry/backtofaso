{{--
    Gabarit commun des e-mails Back to Burkina.
    Contraintes des messageries : mise en page en tableaux, styles en ligne, pas de flex ni de grille.
    Sections : preheader (aperçu dans la boîte de réception), content.
--}}
<!DOCTYPE html>
<html lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>@yield('title', 'Back to Burkina')</title>
    <!--[if mso]><style>table,td,div,p,a,span{font-family:Arial,sans-serif !important;}</style><![endif]-->
    <style>
        @media only screen and (max-width: 520px) {
            .bb-pad { padding-left: 24px !important; padding-right: 24px !important; }
            .bb-code { font-size: 30px !important; letter-spacing: 8px !important; }
            .bb-title { font-size: 22px !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#F3F5F9;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">
    {{-- Texte d'aperçu dans la boîte de réception --}}
    <div style="display:none;max-height:0;overflow:hidden;mso-hide:all;font-size:1px;line-height:1px;color:#F3F5F9;opacity:0;">
        @yield('preheader')&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#F3F5F9" style="background-color:#F3F5F9;">
        <tr>
            <td align="center" style="padding:32px 12px 40px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:560px;">

                    {{-- En-tête bleu nuit --}}
                    <tr>
                        <td bgcolor="#0A1C42" style="background-color:#0A1C42;background-image:linear-gradient(135deg,#163571 0%,#0A1C42 55%,#06122C 100%);border-radius:20px 20px 0 0;padding:28px 36px 26px;" class="bb-pad">
                            {{-- Logo Back to Faso, version pour fond sombre (PNG : les messageries n'affichent pas le SVG) --}}
                            <a href="{{ config('app.url') }}" style="text-decoration:none;">
                                <img src="{{ asset('images/brand/email-logo.png') }}" width="200" height="41" alt="Back to Faso"
                                     style="display:block;width:200px;height:41px;border:0;outline:none;color:#FFFFFF;font-family:'Segoe UI',Helvetica,Arial,sans-serif;font-size:20px;font-weight:800;">
                            </a>
                            <div style="padding-top:8px;font-family:'Segoe UI',Helvetica,Arial,sans-serif;font-size:12.5px;color:#AEB8CC;line-height:1.4;">par Travel Express</div>
                        </td>
                    </tr>
                    {{-- Liseré aux couleurs du drapeau --}}
                    <tr>
                        <td height="4" bgcolor="#009E49" style="height:4px;line-height:4px;font-size:4px;background-color:#009E49;background-image:linear-gradient(90deg,#EF2B2D 0%,#EF2B2D 45%,#FCD116 45%,#FCD116 55%,#009E49 55%,#009E49 100%);">&nbsp;</td>
                    </tr>

                    {{-- Contenu --}}
                    <tr>
                        <td bgcolor="#FFFFFF" style="background-color:#FFFFFF;border-radius:0 0 20px 20px;padding:36px 36px 32px;font-family:'Segoe UI',Helvetica,Arial,sans-serif;color:#2D3037;box-shadow:0 12px 32px -18px rgba(10,28,66,0.25);" class="bb-pad">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Pied de page --}}
                    <tr>
                        <td align="center" style="padding:26px 24px 0;font-family:'Segoe UI',Helvetica,Arial,sans-serif;">
                            <div style="font-size:13px;font-weight:700;color:#40444A;">Back to Burkina</div>
                            <div style="font-size:12px;line-height:1.7;color:#82868E;padding-top:4px;">
                                Étudier à l’étranger, revenir bâtir le Faso.<br>
                                Un service Travel Express · Bobo-Dioulasso, Burkina Faso
                            </div>
                            <div style="font-size:12px;line-height:1.7;padding-top:10px;">
                                <a href="{{ config('app.url') }}" style="color:#0071E3;text-decoration:none;font-weight:600;">{{ preg_replace('#^https?://#', '', rtrim(config('app.url'), '/')) }}</a>
                            </div>
                            <div style="font-size:11px;line-height:1.6;color:#9DA2A9;padding-top:14px;">
                                Cet e-mail vous a été envoyé automatiquement, merci de ne pas y répondre.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
