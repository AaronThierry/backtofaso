<x-layouts.app title="Accueil" active="dashboard">
    <div class="flex flex-col gap-[22px]">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5 lg:hidden">
                <x-logo :size="40" />
                <span class="flex flex-col leading-tight"><span class="text-[18px] font-bold">Back to Burkina</span><span class="text-xs text-ink-600">par Travel Express</span></span>
            </div>
            <h1 class="hidden text-2xl font-bold lg:block">Bonjour, {{ $user->first_name }}</h1>
            <a href="{{ route('chat.index') }}" aria-label="Messages" class="flex size-11 items-center justify-center rounded-full border border-ink-200 bg-white"><x-icon name="bell" /></a>
        </div>

        <x-flash />

        <section class="flex flex-col gap-3.5 rounded-2xl bg-brand-600 p-6 text-white">
            <h2 class="text-[30px] font-bold leading-[1.08]">Étudier en Chine.<br>Revenir bâtir le Faso.</h2>
            <p class="text-sm leading-relaxed">Candidature, visa, vie sur place et retour à l’emploi : un seul accompagnement, du départ au retour.</p>
            @if ($dossier)
                <a href="{{ route('dossier') }}" class="btn self-start bg-white text-brand-600">Suivre mon dossier <x-icon name="arrow" :size="18" /></a>
            @elseif ($user->isRecruiter())
                <a href="{{ route('talents.index') }}" class="btn self-start bg-white text-brand-600">Voir les talents <x-icon name="arrow" :size="18" /></a>
            @elseif ($user->talentProfile)
                <a href="{{ route('talents.edit') }}" class="btn self-start bg-white text-brand-600">Compléter mon profil talent <x-icon name="arrow" :size="18" /></a>
            @endif
        </section>

        <section class="flex flex-col gap-3">
            <h2 class="text-[19px] font-semibold">Votre parcours</h2>
            <div class="grid grid-cols-3 gap-2.5">
                @foreach ([['Partir', 'Admission, bourse, visa'], ['Étudier', 'Réseau et entraide sur place'], ['Revenir', 'Emploi et projets au pays']] as [$t, $d])
                    <div class="card flex flex-col gap-2 px-3 py-3.5">
                        <span class="flex size-7 items-center justify-center rounded-full bg-brand-100 text-[13px] font-bold text-brand-700">{{ $loop->iteration }}</span>
                        <span class="text-sm font-bold">{{ $t }}</span>
                        <span class="text-xs leading-snug text-ink-600">{{ $d }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        @if ($dossier)
            <a href="{{ route('dossier') }}" class="card flex flex-col gap-3 px-[18px] py-4">
                <span class="flex items-center justify-between">
                    <span class="text-[15px] font-bold">Mon dossier</span>
                    <span class="badge-warn">{{ $dossier->stepLabel() }} en cours</span>
                </span>
                <span class="h-2 overflow-hidden rounded bg-ink-200"><span class="block h-2 bg-brand-600" style="width: {{ $dossier->progressPercent() }}%"></span></span>
                <span class="text-[13px] text-ink-600">Étape {{ $dossier->current_step }} sur {{ count(\App\Models\Dossier::STEPS) }}{{ $dossier->missing_count ? ' · '.$dossier->missing_count.' document(s) à fournir' : '' }}</span>
            </a>
        @endif

        @if ($announcement)
            <a href="{{ route('chat.show', $super) }}" class="card flex flex-col gap-2 px-[18px] py-4">
                <span class="flex items-center gap-2"><span class="text-[13px] font-bold">Communauté Back to Burkina</span><span class="badge-gold">Officiel</span></span>
                <span class="text-sm leading-snug">{{ \Illuminate\Support\Str::limit($announcement->body, 140) }}</span>
            </a>
        @endif

        @if ($talents->isNotEmpty())
            <section class="flex flex-col gap-3">
                <div class="flex items-baseline justify-between"><h2 class="text-[19px] font-semibold">Talents récents</h2><a href="{{ route('talents.index') }}" class="text-sm font-bold text-brand-600">Tout voir</a></div>
                @foreach ($talents as $t)
                    @include('app.talents._card', ['profile' => $t])
                @endforeach
            </section>
        @endif

        <section class="flex flex-col gap-3">
            <h2 class="text-[19px] font-semibold">Vu dans la presse</h2>
            <a href="https://burkina24.com/?p=454535" target="_blank" rel="noopener" class="card flex items-center gap-3.5 p-3.5">
                <span class="flex size-16 shrink-0 items-center justify-center rounded-xl bg-ink-900 text-white"><x-icon name="play" :size="26" /></span>
                <span class="flex flex-col gap-1">
                    <span class="text-xs font-bold text-brand-600">Burkina 24 · Reportage</span>
                    <span class="text-sm font-semibold leading-snug">Un entrepreneur au service du retour des compétences</span>
                </span>
            </a>
        </section>
    </div>
</x-layouts.app>
