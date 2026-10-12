<x-layouts.app title="Accueil" active="dashboard">
    <div class="flex flex-col gap-[22px] lg:gap-7">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-2.5 lg:hidden">
                <x-logo :size="40" />
                <span class="flex flex-col leading-tight"><span class="text-[18px] font-bold">Back to Burkina</span><span class="text-xs text-ink-600">par Travel Express</span></span>
            </div>
            <div class="hidden flex-col gap-1 lg:flex">
                <h1 class="text-[28px] font-bold leading-tight">Bonjour, {{ $user->first_name }}</h1>
                <span class="text-[15px] text-ink-600">{{ $user->role_label }} · {{ now()->translatedFormat('l j F') }}</span>
            </div>
            <a href="{{ route('chat.index') }}" aria-label="Messages" class="flex size-11 items-center justify-center rounded-full border border-ink-200 bg-white hover:bg-ink-50"><x-icon name="bell" /></a>
        </div>

        <x-flash />

        <div class="grid gap-[22px] lg:grid-cols-3 lg:items-start lg:gap-7">
            {{-- Colonne principale --}}
            <div class="flex flex-col gap-[22px] lg:col-span-2 lg:gap-7">
                <section class="flex flex-col gap-3.5 rounded-2xl bg-brand-600 p-6 text-white lg:gap-4 lg:p-9">
                    <h2 class="text-[30px] font-bold leading-[1.08] lg:text-[38px]">Étudier en Chine.<br>Revenir bâtir le Faso.</h2>
                    <p class="text-sm leading-relaxed lg:max-w-xl lg:text-base">Candidature, visa, vie sur place et retour à l’emploi : un seul accompagnement, du départ au retour.</p>
                    @if ($dossier)
                        <a href="{{ route('dossier') }}" class="btn self-start bg-white text-brand-600 hover:bg-brand-50">Suivre mon dossier <x-icon name="arrow" :size="18" /></a>
                    @elseif ($user->isRecruiter())
                        <a href="{{ route('talents.index') }}" class="btn self-start bg-white text-brand-600 hover:bg-brand-50">Voir les talents <x-icon name="arrow" :size="18" /></a>
                    @elseif ($user->talentProfile)
                        <a href="{{ route('talents.edit') }}" class="btn self-start bg-white text-brand-600 hover:bg-brand-50">Compléter mon profil talent <x-icon name="arrow" :size="18" /></a>
                    @endif
                </section>

                @if ($dossier)
                    <a href="{{ route('dossier') }}" class="card flex flex-col gap-3 px-[18px] py-4 transition hover:border-brand-300 lg:px-6 lg:py-5">
                        <span class="flex items-center justify-between gap-3">
                            <span class="text-[15px] font-bold lg:text-[17px]">Mon dossier</span>
                            <span class="badge-warn">{{ $dossier->stepLabel() }} en cours</span>
                        </span>
                        <span class="h-2 overflow-hidden rounded bg-ink-200"><span class="block h-2 bg-brand-600" style="width: {{ $dossier->progressPercent() }}%"></span></span>
                        <span class="text-[13px] text-ink-600">Étape {{ $dossier->current_step }} sur {{ count(\App\Models\Dossier::STEPS) }}{{ $dossier->missing_count ? ' · '.$dossier->missing_count.' document(s) à fournir' : '' }}</span>
                    </a>
                @endif

                <section class="flex flex-col gap-3">
                    <h2 class="text-[19px] font-semibold">Votre parcours</h2>
                    <div class="grid grid-cols-3 gap-2.5 lg:gap-4">
                        @foreach ([['Partir', 'Admission, bourse, visa'], ['Étudier', 'Réseau et entraide sur place'], ['Revenir', 'Emploi et projets au pays']] as [$t, $d])
                            <div class="card flex flex-col gap-2 px-3 py-3.5 lg:gap-2.5 lg:p-5">
                                <span class="flex size-7 items-center justify-center rounded-full bg-brand-100 text-[13px] font-bold text-brand-700 lg:size-9 lg:text-[15px]">{{ $loop->iteration }}</span>
                                <span class="text-sm font-bold lg:text-base">{{ $t }}</span>
                                <span class="text-xs leading-snug text-ink-600 lg:text-[13.5px]">{{ $d }}</span>
                            </div>
                        @endforeach
                    </div>
                </section>

                @if ($talents->isNotEmpty())
                    <section class="flex flex-col gap-3">
                        <div class="flex items-baseline justify-between"><h2 class="text-[19px] font-semibold">Talents récents</h2><a href="{{ route('talents.index') }}" class="text-sm font-bold text-brand-600 hover:underline">Tout voir</a></div>
                        <div class="grid gap-3 xl:grid-cols-2">
                            @foreach ($talents as $t)
                                @include('app.talents._card', ['profile' => $t])
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            {{-- Colonne latérale --}}
            <aside class="flex flex-col gap-[22px] lg:sticky lg:top-10">
                @if ($announcement)
                    <section class="flex flex-col gap-3">
                        <h2 class="hidden text-[19px] font-semibold lg:block">Dernière annonce</h2>
                        <a href="{{ route('chat.show', $super) }}" class="card flex flex-col gap-2 px-[18px] py-4 transition hover:border-brand-300">
                            <span class="flex items-center gap-2"><span class="text-[13px] font-bold">Communauté Back to Burkina</span><span class="badge-gold">Officiel</span></span>
                            <span class="text-sm leading-snug">{{ \Illuminate\Support\Str::limit($announcement->body, 140) }}</span>
                            <span class="text-xs text-ink-600">{{ $announcement->user->name }} · {{ $announcement->created_at->diffForHumans() }}</span>
                        </a>
                    </section>
                @endif

                <section class="flex flex-col gap-3">
                    <h2 class="text-[19px] font-semibold">Vu dans la presse</h2>
                    <a href="https://burkina24.com/?p=454535" target="_blank" rel="noopener" class="card flex items-center gap-3.5 p-3.5 transition hover:border-brand-300">
                        <span class="flex size-16 shrink-0 items-center justify-center rounded-xl bg-ink-900 text-white"><x-icon name="play" :size="26" /></span>
                        <span class="flex flex-col gap-1">
                            <span class="text-xs font-bold text-brand-600">Burkina 24 · Reportage</span>
                            <span class="text-sm font-semibold leading-snug">Un entrepreneur au service du retour des compétences</span>
                        </span>
                    </a>
                </section>

                <section class="hidden flex-col gap-3 lg:flex">
                    <h2 class="text-[19px] font-semibold">Accès rapide</h2>
                    <div class="flex flex-col divide-y divide-ink-100 rounded-xl border border-ink-200 bg-white">
                        @foreach ([
                            [route('chat.show', $super), 'chat', 'Communauté', 'Annonces, entraide, emplois, bourses'],
                            [route('talents.index'), 'users', 'Vivier de talents', 'Diplômés formés en Chine'],
                            [route('account'), 'user', 'Mon compte', 'Profil et notifications'],
                        ] as [$url, $icon, $t, $d])
                            <a href="{{ $url }}" class="flex items-center gap-3 px-4 py-3 hover:bg-ink-50">
                                <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><x-icon :name="$icon" :size="18" /></span>
                                <span class="flex flex-1 flex-col"><span class="text-sm font-bold">{{ $t }}</span><span class="text-xs text-ink-600">{{ $d }}</span></span>
                                <x-icon name="chevron" :size="16" class="text-ink-400" />
                            </a>
                        @endforeach
                    </div>
                </section>
            </aside>
        </div>
    </div>
</x-layouts.app>
