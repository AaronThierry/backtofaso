{{-- Gabarit des pages d'erreur : autonome (pas de session, pas d'utilisateur), pour s'afficher dans tous les cas --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex">
    <title>@yield('title') · Back to Burkina</title>
    <link rel="icon" type="image/png" href="/icons/icon-192.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,700;12..96,800&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-hero relative min-h-dvh overflow-hidden text-white">
    <div class="bg-weave pointer-events-none absolute inset-y-0 right-0 w-1/2 opacity-50 [mask-image:linear-gradient(to_left,black,transparent)]"></div>
    <div class="pointer-events-none absolute -left-40 top-1/4 size-[480px] animate-drift rounded-full bg-brand-500/25 blur-3xl"></div>

    <main class="relative mx-auto flex min-h-dvh max-w-[1100px] flex-col px-5 py-8 sm:px-8">
        <a href="/" class="flex items-center gap-3 self-start">
            <x-logo :size="42" />
            <span class="flex flex-col leading-tight"><span class="font-display text-lg font-bold">Back to Burkina</span><span class="text-xs text-white/55">par Travel Express</span></span>
        </a>

        <div class="my-auto grid items-center gap-10 py-12 lg:grid-cols-[1fr_auto]">
            <div class="flex animate-rise flex-col gap-5">
                <span class="eyebrow text-gold-300">Erreur @yield('code')</span>
                <h1 class="max-w-2xl text-[clamp(2.2rem,7vw,4.2rem)] font-extrabold leading-[1.02]">@yield('heading')</h1>
                <p class="max-w-xl text-[16.5px] leading-relaxed text-white/70 sm:text-lg">@yield('message')</p>
                <div class="flex flex-col gap-3 pt-2 min-[460px]:flex-row">
                    @section('actions')
                        <a href="/" class="btn-gold">Retour à l’accueil <x-icon name="arrow" :size="18" /></a>
                        <a href="javascript:history.back()" class="btn-outline-light">Page précédente</a>
                    @show
                </div>
            </div>
            <span class="pointer-events-none hidden select-none bg-linear-to-b from-white/25 to-white/0 bg-clip-text font-display text-[220px] font-extrabold leading-none text-transparent lg:block" aria-hidden="true">@yield('code')</span>
        </div>

        <p class="text-[13px] text-white/45">Besoin d’aide ? <a href="/contact" class="font-semibold text-white/70 underline-offset-2 hover:underline">Contactez-nous</a></p>
    </main>
</body>
</html>
