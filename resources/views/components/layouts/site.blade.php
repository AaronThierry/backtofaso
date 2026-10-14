@props(['title' => null, 'description' => null])
@php
$links = [
    route('study') => ['Étudier à l’étranger', 'study'],
    route('home').'#communaute' => ['Communauté', null],
    route('companies') => ['Entreprises', 'companies'],
    route('faq') => ['FAQ', 'faq'],
    route('contact') => ['Contact', 'contact'],
];
$contact = config('backtoburkina.contact');
@endphp
<x-layouts.base :title="$title" :description="$description">
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
            <a href="{{ route('home') }}" class="flex min-w-0 items-center" aria-label="Back to Faso, accueil">
                <x-wordmark :size="42" caption="par Travel Express" />
            </a>
            <nav class="hidden items-center gap-1 text-[15px] font-semibold lg:flex" aria-label="Navigation du site">
                @foreach ($links as $href => [$label, $name])
                    @php($current = $name && request()->routeIs($name))
                    <a href="{{ $href }}" @if ($current) aria-current="page" @endif @class(['group relative rounded-full px-3.5 py-2 transition hover:text-navy-950', 'text-navy-950' => $current, 'text-ink-700' => ! $current])>
                        {{ $label }}
                        <span @class(['absolute inset-x-3.5 bottom-1 h-0.5 origin-left rounded-full bg-gold-400 transition-transform duration-300 group-hover:scale-x-100', 'scale-x-100' => $current, 'scale-x-0' => ! $current])></span>
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
                @foreach ($links as $href => [$label, $name])
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

        {{-- Bandeau d'appel --}}
        <div class="relative mx-auto max-w-[1200px] px-4 pt-14 sm:px-6">
            <div class="glass flex flex-col gap-5 rounded-3xl p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
                <div class="flex flex-col gap-1.5">
                    <span class="font-display text-[22px] font-bold leading-tight sm:text-[26px]">Une question avant de vous lancer ?</span>
                    <span class="text-[14.5px] text-white/65">Notre équipe vous répond, à l’agence de Bobo-Dioulasso ou en ligne.</span>
                </div>
                <div class="flex flex-col gap-2.5 min-[460px]:flex-row">
                    <a href="{{ route('contact') }}" class="btn-light">Nous écrire <x-icon name="mail" :size="18" /></a>
                    @if ($contact['whatsapp'])
                        <a href="https://wa.me/{{ $contact['whatsapp'] }}" target="_blank" rel="noopener" class="btn-outline-light"><x-icon name="chat" :size="18" /> WhatsApp</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="relative mx-auto grid max-w-[1200px] gap-10 px-4 py-14 text-sm sm:grid-cols-2 sm:px-6 lg:grid-cols-[1.4fr_1fr_1fr_1.2fr]">
            <div class="flex flex-col gap-4">
                <a href="{{ route('home') }}" class="flex items-center" aria-label="Back to Faso, accueil"><x-wordmark :size="48" dark /></a>
                <span class="max-w-xs leading-relaxed text-white/65">Un projet de Travel Express pour le retour des compétences au Burkina Faso. Étudier à l’étranger, revenir bâtir le Faso.</span>
            </div>
            <nav class="flex flex-col gap-3" aria-label="Étudiants">
                <span class="text-xs font-bold uppercase tracking-[0.14em] text-gold-300">Étudiants</span>
                <a href="{{ route('study') }}" class="text-white/75 transition hover:text-white">Étudier à l’étranger</a>
                <a href="{{ route('study') }}#destinations" class="text-white/75 transition hover:text-white">Destinations</a>
                <a href="{{ route('study') }}#bourses" class="text-white/75 transition hover:text-white">Bourses d’études</a>
                <a href="{{ route('study') }}#documents" class="text-white/75 transition hover:text-white">Documents à préparer</a>
                <a href="{{ route('welcome') }}" class="text-white/75 transition hover:text-white">Déposer un dossier</a>
                <a href="{{ route('faq') }}" class="text-white/75 transition hover:text-white">Questions fréquentes</a>
            </nav>
            <nav class="flex flex-col gap-3" aria-label="Entreprises">
                <span class="text-xs font-bold uppercase tracking-[0.14em] text-gold-300">Entreprises</span>
                <a href="{{ route('companies') }}" class="text-white/75 transition hover:text-white">Recruter des talents</a>
                <a href="{{ route('register', ['profil' => 'recruiter']) }}" class="text-white/75 transition hover:text-white">Compte recruteur</a>
                <a href="{{ route('contact', ['sujet' => 'partenariat']) }}" class="text-white/75 transition hover:text-white">Devenir partenaire</a>
            </nav>
            <div class="flex flex-col gap-3">
                <span class="text-xs font-bold uppercase tracking-[0.14em] text-gold-300">Contact</span>
                <span class="flex items-start gap-2 text-white/75"><x-icon name="map" :size="16" class="mt-0.5 shrink-0" /> {{ $contact['address'] }}</span>
                @if ($contact['phone'])
                    <a href="tel:{{ str_replace(' ', '', $contact['phone']) }}" class="flex items-center gap-2 text-white/75 transition hover:text-white"><x-icon name="phone" :size="16" /> {{ $contact['phone'] }}</a>
                @endif
                <a href="mailto:{{ $contact['email'] }}" class="flex items-center gap-2 text-white/75 transition hover:text-white"><x-icon name="mail" :size="16" /> {{ $contact['email'] }}</a>
                @if ($contact['hours'])
                    <span class="flex items-start gap-2 text-white/75"><x-icon name="clock" :size="16" class="mt-0.5 shrink-0" /> {{ $contact['hours'] }}</span>
                @endif
                <a href="https://travels.express" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 font-bold text-white hover:text-gold-200">travels.express <x-icon name="external" :size="14" /></a>
            </div>
        </div>
        <div class="relative border-t border-white/10">
            <div class="mx-auto flex max-w-[1200px] flex-wrap items-center justify-between gap-3 px-4 py-5 text-[13px] text-white/55 sm:px-6">
                <span>© {{ date('Y') }} Travel Express. Tous droits réservés.</span>
                <nav class="flex flex-wrap gap-x-5 gap-y-2" aria-label="Informations légales">
                    <a href="{{ route('legal.notice') }}" class="transition hover:text-white">Mentions légales</a>
                    <a href="{{ route('legal.privacy') }}" class="transition hover:text-white">Confidentialité</a>
                    <a href="{{ route('legal.terms') }}" class="transition hover:text-white">Conditions d’utilisation</a>
                    <a href="{{ route('contact') }}" class="transition hover:text-white">Contact</a>
                </nav>
            </div>
        </div>
    </footer>
</x-layouts.base>
