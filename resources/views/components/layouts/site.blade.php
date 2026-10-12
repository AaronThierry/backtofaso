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

    {{-- Barre de progression de lecture --}}
    <div class="pointer-events-none fixed inset-x-0 top-0 z-[60] h-[3px]">
        <div data-scroll-progress style="transform: scaleX(0)" class="h-full origin-left bg-linear-to-r from-gold-300 via-gold-400 to-brand-500"></div>
    </div>

    <header class="sticky top-0 z-40 transition-[transform,box-shadow,background-color] duration-300 ease-out"
            x-data="{ open: false, scrolled: false, hidden: false, last: 0 }"
            x-effect="document.documentElement.classList.toggle('overflow-hidden', open)"
            @scroll.window.passive="const y = window.scrollY; scrolled = y > 10; hidden = !open && y > 220 && y > last; last = y"
            @keydown.escape.window="open = false"
            :class="[hidden ? '-translate-y-full' : 'translate-y-0', scrolled || open ? 'bg-white/90 shadow-[0_1px_0_rgb(16_24_40/0.06),0_10px_30px_-18px_rgb(16_24_40/0.25)] backdrop-blur-xl' : 'bg-white']">
        <div class="mx-auto flex max-w-[1200px] items-center justify-between gap-3 px-4 py-3 sm:px-6 sm:py-3.5">
            <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-2.5 sm:gap-3">
                <x-logo :size="40" />
                <span class="flex min-w-0 flex-col leading-tight">
                    <span class="truncate font-display text-[17px] font-bold text-navy-950 sm:text-[19px]">Back to Burkina</span>
                    <span class="hidden text-xs text-ink-500 min-[380px]:block">par Travel Express</span>
                </span>
            </a>
            <nav class="hidden items-center gap-1 text-[15px] font-semibold lg:flex" aria-label="Navigation du site">
                @foreach ($links as $href => $label)
                    <a href="{{ $href }}" class="group relative rounded-full px-3.5 py-2 text-ink-700 transition hover:text-navy-950">
                        {{ $label }}
                        <span class="absolute inset-x-3.5 bottom-1 h-0.5 origin-left scale-x-0 rounded-full bg-gold-400 transition-transform duration-300 group-hover:scale-x-100"></span>
                    </a>
                @endforeach
            </nav>
            <div class="flex shrink-0 items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary min-h-11 px-4 text-sm">Mon espace <x-icon name="arrow" :size="16" /></a>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost hidden min-h-11 px-4 text-sm text-ink-800 sm:inline-flex">Se connecter</a>
                    <a href="{{ route('welcome') }}" class="btn-primary hidden min-h-11 px-4 text-sm min-[420px]:inline-flex">Créer mon compte</a>
                @endauth
                <button type="button" @click="open = !open" :aria-expanded="open" aria-controls="menu-mobile" aria-label="Menu" class="icon-btn lg:hidden">
                    <x-icon name="menu" x-show="!open" />
                    <x-icon name="close" x-show="open" x-cloak />
                </button>
            </div>
        </div>

        {{-- Menu mobile plein écran --}}
        <nav id="menu-mobile" x-show="open" x-cloak
             x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="absolute inset-x-0 top-full h-[calc(100dvh-64px)] overflow-y-auto border-t border-ink-100 bg-white lg:hidden" aria-label="Navigation du site">
            <div class="mx-auto flex max-w-[1200px] flex-col gap-1 px-4 pb-10 pt-4 sm:px-6">
                @foreach ($links as $href => $label)
                    <a href="{{ $href }}" @click="open = false" class="flex min-h-14 animate-rise items-center justify-between border-b border-ink-100 px-1 font-display text-[22px] font-bold text-navy-950" style="animation-delay: {{ $loop->index * 55 }}ms">
                        {{ $label }} <x-icon name="arrow" :size="20" class="text-gold-500" />
                    </a>
                @endforeach
                <div class="mt-6 flex animate-rise flex-col gap-2.5" style="animation-delay: 300ms">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary min-h-[54px] text-base">Mon espace</a>
                    @else
                        <a href="{{ route('welcome') }}" class="btn-primary min-h-[54px] text-base">Créer mon compte <x-icon name="arrow" :size="18" /></a>
                        <a href="{{ route('login') }}" class="btn-secondary min-h-[54px] text-base">Se connecter</a>
                    @endauth
                </div>
                <p class="mt-8 flex animate-rise items-center gap-2 text-sm text-ink-500" style="animation-delay: 360ms"><x-icon name="map" :size="16" class="text-gold-500" /> Agence Travel Express · Bobo-Dioulasso</p>
            </div>
        </nav>
    </header>

    <main class="overflow-x-clip">{{ $slot }}</main>

    {{-- Mobile : appel à l'action fixe une fois le héros dépassé --}}
    @guest
        <div x-data="{ show: false }" x-cloak
             @scroll.window.passive="show = window.scrollY > 640 && window.innerHeight + window.scrollY < document.documentElement.scrollHeight - 320"
             x-show="show" x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="translate-y-full opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
             x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-full opacity-0"
             class="fixed inset-x-3 bottom-[calc(env(safe-area-inset-bottom)+12px)] z-30 flex items-center gap-3 rounded-2xl bg-navy-950/95 p-2.5 pl-4 text-white shadow-lift ring-1 ring-white/10 backdrop-blur-lg sm:hidden">
            <span class="flex min-w-0 flex-1 flex-col leading-tight">
                <span class="truncate text-sm font-bold">Prêt à partir ?</span>
                <span class="truncate text-xs text-white/60">Création de compte gratuite</span>
            </span>
            <a href="{{ route('welcome') }}" class="btn-gold min-h-11 shrink-0 px-4 text-sm">Commencer</a>
        </div>
    @endguest

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
