@props(['title' => null, 'bodyClass' => '', 'description' => null])
@php
$description ??= 'Étudier à l’étranger, revenir bâtir le Faso. Travel Express accompagne les étudiants burkinabè de la candidature au retour à l’emploi, quelle que soit leur destination.';
$fullTitle = ($title ? $title.' · ' : '').'Back to Burkina';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Active les animations d'apparition ; si le script ne se charge pas, le contenu redevient visible --}}
    <script>document.documentElement.classList.add('js');setTimeout(function(){if(!document.documentElement.classList.contains('motion-ready'))document.documentElement.classList.remove('js')},2500);</script>
    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Aperçu lors d'un partage (WhatsApp, Facebook, LinkedIn…) --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Back to Burkina">
    <meta property="og:locale" content="fr_FR">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('icons/icon-512.png') }}">
    <meta name="twitter:card" content="summary">
    <meta property="og:image:alt" content="Logo Back to Faso">

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#0071E3">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Back to BF">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" type="image/svg+xml" href="/icons/icon.svg">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700;12..96,800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="{{ $bodyClass }}">
    {{ $slot }}
    @stack('scripts')
</body>
</html>
