@props(['title' => null])
<x-layouts.base :title="$title">
    <div class="bg-brand-700 text-[13px] text-white">
        <div class="mx-auto flex max-w-6xl flex-wrap justify-between gap-2 px-4 py-2 sm:px-6">
            <span>Inscriptions ouvertes pour la prochaine rentrée · <a href="{{ route('welcome') }}" class="font-bold text-white underline">Déposer mon dossier</a></span>
            <span class="hidden sm:inline">Agence Travel Express · Bobo-Dioulasso</span>
        </div>
    </div>

    <header class="sticky top-0 z-30 border-b border-ink-200 bg-white/95 backdrop-blur" x-data="{ open: false }">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <x-logo :size="40" />
                <span class="flex flex-col leading-tight">
                    <span class="text-[18px] font-bold text-ink-900">Back to Burkina</span>
                    <span class="text-xs text-ink-600">par Travel Express</span>
                </span>
            </a>
            <nav class="hidden items-center gap-7 text-[15px] font-semibold md:flex" aria-label="Navigation du site">
                <a href="#parcours" class="text-ink-900 hover:text-brand-600">Étudier en Chine</a>
                <a href="#communaute" class="text-ink-900 hover:text-brand-600">Communauté</a>
                <a href="#talents" class="text-ink-900 hover:text-brand-600">Talents</a>
                <a href="#entreprises" class="text-ink-900 hover:text-brand-600">Entreprises</a>
                <a href="#faq" class="text-ink-900 hover:text-brand-600">FAQ</a>
            </nav>
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary min-h-11 text-sm">Mon espace</a>
                @else
                    <a href="{{ route('login') }}" class="btn-secondary hidden min-h-11 text-sm sm:inline-flex">Se connecter</a>
                    <a href="{{ route('welcome') }}" class="btn-primary min-h-11 text-sm">Créer mon compte</a>
                @endauth
            </div>
        </div>
    </header>

    <main>{{ $slot }}</main>

    <footer class="bg-brand-700 text-white">
        <div class="mx-auto grid max-w-6xl gap-8 px-4 py-14 text-sm sm:grid-cols-2 sm:px-6 lg:grid-cols-4">
            <div class="flex flex-col gap-2.5">
                <span class="text-lg font-bold">Back to Burkina</span>
                <span class="leading-relaxed">Un projet de Travel Express pour le retour des compétences au Burkina Faso.</span>
            </div>
            <div class="flex flex-col gap-2.5">
                <span class="font-bold">Étudiants</span>
                <a href="{{ route('welcome') }}" class="hover:underline">Déposer un dossier</a>
                <a href="#parcours" class="hover:underline">Étudier en Chine</a>
                <a href="#communaute" class="hover:underline">Communauté</a>
            </div>
            <div class="flex flex-col gap-2.5">
                <span class="font-bold">Entreprises</span>
                <a href="#talents" class="hover:underline">Vivier de talents</a>
                <a href="{{ route('register', ['profil' => 'recruiter']) }}" class="hover:underline">Compte recruteur</a>
            </div>
            <div class="flex flex-col gap-2.5">
                <span class="font-bold">Contact</span>
                <span>Bobo-Dioulasso, Burkina Faso</span>
                <span>[Téléphone] · contact@backtoburkina.bf</span>
                <a href="https://travels.express" class="font-bold hover:underline">travels.express</a>
            </div>
        </div>
        <div class="mx-auto flex max-w-6xl flex-wrap justify-between gap-3 border-t border-white/20 px-4 py-5 text-[13px] sm:px-6">
            <span>© {{ date('Y') }} Travel Express. Tous droits réservés.</span>
            <span>Mentions légales · Confidentialité</span>
        </div>
    </footer>
</x-layouts.base>
