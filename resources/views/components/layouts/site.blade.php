@props(['title' => null])
@php
$links = ['#parcours' => 'Étudier en Chine', '#communaute' => 'Communauté', '#talents' => 'Talents', '#entreprises' => 'Entreprises', '#faq' => 'FAQ'];
@endphp
<x-layouts.base :title="$title">
    <div class="bg-navy-950 text-[13px] text-white/80">
        <div class="mx-auto flex max-w-[1200px] flex-wrap items-center justify-between gap-2 px-4 py-2.5 sm:px-6">
            <span class="flex items-center gap-2">
                <span class="relative flex size-2"><span class="absolute inset-0 animate-ping rounded-full bg-gold-400 opacity-60"></span><span class="relative size-2 rounded-full bg-gold-400"></span></span>
                Inscriptions ouvertes pour la prochaine rentrée ·
                <a href="{{ route('welcome') }}" class="font-bold text-white underline decoration-gold-400 decoration-2 underline-offset-4 hover:text-gold-200">Déposer mon dossier</a>
            </span>
            <span class="hidden items-center gap-1.5 sm:flex"><x-icon name="map" :size="14" class="text-gold-300" /> Agence Travel Express · Bobo-Dioulasso</span>
        </div>
    </div>

    <header class="sticky top-0 z-40 transition-shadow duration-300" x-data="{ open: false, scrolled: false }"
            @scroll.window="scrolled = window.scrollY > 10" :class="scrolled || open ? 'bg-white/90 shadow-[0_1px_0_rgb(16_24_40/0.06),0_10px_30px_-18px_rgb(16_24_40/0.25)] backdrop-blur-xl' : 'bg-white'">
        <div class="mx-auto flex max-w-[1200px] items-center justify-between gap-4 px-4 py-3.5 sm:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <x-logo :size="42" />
                <span class="flex flex-col leading-tight">
                    <span class="font-display text-[19px] font-bold text-navy-950">Back to Burkina</span>
                    <span class="text-xs text-ink-500">par Travel Express</span>
                </span>
            </a>
            <nav class="hidden items-center gap-1 text-[15px] font-semibold lg:flex" aria-label="Navigation du site">
                @foreach ($links as $href => $label)
                    <a href="{{ $href }}" class="rounded-full px-3.5 py-2 text-ink-700 transition hover:bg-ink-50 hover:text-navy-950">{{ $label }}</a>
                @endforeach
            </nav>
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary min-h-11 px-4 text-sm">Mon espace <x-icon name="arrow" :size="16" /></a>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost hidden min-h-11 px-4 text-sm text-ink-800 sm:inline-flex">Se connecter</a>
                    <a href="{{ route('welcome') }}" class="btn-primary min-h-11 px-4 text-sm">Créer mon compte</a>
                @endauth
                <button type="button" @click="open = !open" :aria-expanded="open" aria-controls="menu-mobile" aria-label="Menu" class="icon-btn lg:hidden">
                    <x-icon name="menu" x-show="!open" />
                    <x-icon name="close" x-show="open" x-cloak />
                </button>
            </div>
        </div>

        {{-- Menu mobile --}}
        <nav id="menu-mobile" x-show="open" x-cloak x-transition.origin.top class="border-t border-ink-100 bg-white lg:hidden" aria-label="Navigation du site">
            <div class="mx-auto flex max-w-[1200px] flex-col gap-1 px-4 py-3 sm:px-6">
                @foreach ($links as $href => $label)
                    <a href="{{ $href }}" @click="open = false" class="flex min-h-12 items-center justify-between rounded-xl px-3 text-[15px] font-semibold text-ink-800 hover:bg-ink-50">{{ $label }} <x-icon name="chevron" :size="16" class="text-ink-300" /></a>
                @endforeach
                @guest
                    <a href="{{ route('login') }}" class="btn-secondary mt-2 sm:hidden">Se connecter</a>
                @endguest
            </div>
        </nav>
    </header>

    <main>{{ $slot }}</main>

    <footer class="bg-hero relative overflow-hidden text-white">
        <div class="bg-weave pointer-events-none absolute inset-x-0 top-0 h-full opacity-40 [mask-image:linear-gradient(to_bottom,black,transparent_70%)]"></div>
        <div class="relative mx-auto grid max-w-[1200px] gap-10 px-4 py-16 text-sm sm:grid-cols-2 sm:px-6 lg:grid-cols-[1.4fr_1fr_1fr_1.2fr]">
            <div class="flex flex-col gap-4">
                <span class="flex items-center gap-3"><x-logo :size="44" /><span class="font-display text-xl font-bold">Back to Burkina</span></span>
                <span class="max-w-xs leading-relaxed text-white/65">Un projet de Travel Express pour le retour des compétences au Burkina Faso.</span>
            </div>
            <div class="flex flex-col gap-3">
                <span class="text-xs font-bold uppercase tracking-[0.14em] text-gold-300">Étudiants</span>
                <a href="{{ route('welcome') }}" class="text-white/75 transition hover:text-white">Déposer un dossier</a>
                <a href="#parcours" class="text-white/75 transition hover:text-white">Étudier en Chine</a>
                <a href="#communaute" class="text-white/75 transition hover:text-white">Communauté</a>
            </div>
            <div class="flex flex-col gap-3">
                <span class="text-xs font-bold uppercase tracking-[0.14em] text-gold-300">Entreprises</span>
                <a href="#talents" class="text-white/75 transition hover:text-white">Vivier de talents</a>
                <a href="{{ route('register', ['profil' => 'recruiter']) }}" class="text-white/75 transition hover:text-white">Compte recruteur</a>
            </div>
            <div class="flex flex-col gap-3">
                <span class="text-xs font-bold uppercase tracking-[0.14em] text-gold-300">Contact</span>
                <span class="flex items-center gap-2 text-white/75"><x-icon name="map" :size="16" /> Bobo-Dioulasso, Burkina Faso</span>
                <span class="flex items-center gap-2 text-white/75"><x-icon name="phone" :size="16" /> [Téléphone]</span>
                <span class="flex items-center gap-2 text-white/75"><x-icon name="mail" :size="16" /> contact@backtoburkina.bf</span>
                <a href="https://travels.express" class="inline-flex items-center gap-1.5 font-bold text-white hover:text-gold-200">travels.express <x-icon name="external" :size="14" /></a>
            </div>
        </div>
        <div class="relative border-t border-white/10">
            <div class="mx-auto flex max-w-[1200px] flex-wrap justify-between gap-3 px-4 py-5 text-[13px] text-white/55 sm:px-6">
                <span>© {{ date('Y') }} Travel Express. Tous droits réservés.</span>
                <span>Mentions légales · Confidentialité</span>
            </div>
        </div>
    </footer>
</x-layouts.base>
