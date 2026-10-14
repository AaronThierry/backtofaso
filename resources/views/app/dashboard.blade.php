@php
    // Étape du parcours mise en avant selon le profil
    $phase = match ($user->role) {
        \App\Models\User::ROLE_CANDIDATE => 1,
        \App\Models\User::ROLE_STUDENT_ABROAD => 2,
        \App\Models\User::ROLE_GRADUATE => 3,
        default => null,
    };
    $hour = (int) now()->format('G');
    $greeting = $hour < 18 && $hour >= 5 ? 'Bonjour' : 'Bonsoir';
@endphp
<x-layouts.app title="Accueil" active="dashboard">
    <div class="flex flex-col gap-6 lg:gap-8">
        {{-- En-tête --}}
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 lg:hidden">
                <x-logo :size="40" />
                <span class="flex flex-col leading-tight"><span class="font-display text-[18px] font-bold">Back to Burkina</span><span class="text-xs text-ink-600">par Travel Express</span></span>
            </a>
            <div class="hidden flex-col gap-1 lg:flex">
                <span class="eyebrow">{{ ucfirst(now()->translatedFormat('l j F')) }}</span>
                <h1 class="text-[34px] font-bold leading-tight text-navy-950">{{ $greeting }}, {{ $user->first_name }}</h1>
            </div>
            <a href="{{ route('chat.index') }}" aria-label="Messages" class="icon-btn relative">
                <x-icon name="bell" />
                @if ($user->unreadMessagesCount())
                    <span class="absolute right-2.5 top-2.5 size-2.5 rounded-full bg-err-500 ring-2 ring-white"></span>
                @endif
            </a>
        </div>

        <x-flash />

        {{-- Bannière --}}
        <section class="bg-hero relative animate-rise overflow-hidden rounded-[28px] p-6 text-white shadow-lift sm:p-8 lg:p-10">
            <div class="bg-weave pointer-events-none absolute inset-y-0 right-0 w-1/3 opacity-60 [mask-image:linear-gradient(to_left,black,transparent)]"></div>
            <div class="relative flex flex-col gap-8 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex max-w-xl flex-col gap-4">
                    <span class="eyebrow text-gold-300 lg:hidden">{{ $greeting }}, {{ $user->first_name }}</span>
                    <h2 class="text-[30px] font-extrabold leading-[1.04] sm:text-[36px] lg:text-[42px]">Étudier à l’étranger.<br><span class="bg-linear-to-r from-gold-200 to-gold-400 bg-clip-text text-transparent">Revenir bâtir le Faso.</span></h2>
                    <p class="text-[15px] leading-relaxed text-white/70">Candidature, visa, vie sur place et retour à l’emploi : un seul accompagnement, du départ au retour.</p>
                    <div class="flex flex-wrap gap-2.5 pt-1">
                        @if ($dossier)
                            <a href="{{ route('dossier') }}" class="btn-light">Suivre mon dossier <x-icon name="arrow" :size="18" /></a>
                        @elseif ($user->isRecruiter())
                            <a href="{{ route('talents.index') }}" class="btn-light">Voir les talents <x-icon name="arrow" :size="18" /></a>
                        @elseif ($user->talentProfile)
                            <a href="{{ route('talents.edit') }}" class="btn-light">Compléter mon profil talent <x-icon name="arrow" :size="18" /></a>
                        @endif
                        <a href="{{ route('chat.show', $super) }}" class="btn-outline-light">La communauté</a>
                    </div>
                </div>

                @if ($dossier)
                    <a href="{{ route('dossier') }}" class="glass group flex items-center gap-5 rounded-3xl p-5 transition hover:bg-white/15 lg:w-[340px] lg:shrink-0">
                        <x-progress-ring :percent="$dossier->progressPercent()" :size="92" :stroke="8" dark />
                        <span class="flex min-w-0 flex-col gap-1.5">
                            <span class="text-xs font-bold uppercase tracking-[0.14em] text-white/55">Mon dossier</span>
                            <span class="font-display text-lg font-bold leading-tight">{{ $dossier->stepLabel() }}</span>
                            <span class="text-[13px] text-white/65">Étape {{ $dossier->current_step }} sur {{ count(\App\Models\Dossier::STEPS) }}{{ $dossier->countryName() ? ' · '.$dossier->countryName() : '' }}</span>
                            @if ($dossier->missing_count)
                                <span class="mt-1 inline-flex items-center gap-1.5 self-start rounded-full bg-gold-400 px-2.5 py-1 text-xs font-bold text-navy-950">{{ $dossier->missing_count }} document(s) à fournir</span>
                            @endif
                        </span>
                    </a>
                @endif
            </div>
        </section>

        <div class="grid gap-6 lg:grid-cols-3 lg:items-start lg:gap-8">
            {{-- Colonne principale --}}
            <div class="flex flex-col gap-6 lg:col-span-2 lg:gap-8">
                <section class="flex flex-col gap-3.5">
                    <div class="flex items-baseline justify-between">
                        <h2 class="text-[20px] font-bold text-navy-950">Votre parcours</h2>
                        @if ($phase)<span class="text-[13px] font-semibold text-ink-500">Vous êtes à l’étape {{ $phase }}</span>@endif
                    </div>
                    <div class="card relative grid grid-cols-3 gap-2 p-4 sm:p-6">
                        <span class="absolute left-[16.6%] right-[16.6%] top-[42px] h-0.5 bg-ink-100 sm:top-[54px]"></span>
                        @if ($phase && $phase > 1)
                            <span class="absolute left-[16.6%] top-[42px] h-0.5 bg-linear-to-r from-brand-500 to-brand-600 sm:top-[54px]" style="width: {{ ($phase - 1) * 33.3 }}%"></span>
                        @endif
                        @foreach ([['plane', 'Partir', 'Admission, bourse, visa'], ['graduation', 'Étudier', 'Réseau et entraide sur place'], ['briefcase', 'Revenir', 'Emploi et projets au pays']] as $i => [$icon, $t, $d])
                            @php($n = $i + 1)
                            <div class="relative flex flex-col items-center gap-2 text-center">
                                <span @class([
                                    'flex size-[52px] items-center justify-center rounded-2xl ring-4 ring-white transition sm:size-[60px]',
                                    'bg-linear-to-br from-brand-500 to-brand-600 text-white shadow-glow' => $phase === $n,
                                    'bg-brand-600 text-white' => $phase && $n < $phase,
                                    'bg-brand-50 text-brand-600' => ! $phase || $n > $phase,
                                ])>
                                    @if ($phase && $n < $phase)
                                        <x-icon name="check" :size="22" stroke-width="2.6" />
                                    @else
                                        <x-icon :name="$icon" :size="24" />
                                    @endif
                                </span>
                                <span class="mt-1 text-[15px] font-bold text-navy-950">{{ $t }}</span>
                                <span class="hidden text-[13px] leading-snug text-ink-600 sm:block">{{ $d }}</span>
                                @if ($phase === $n)
                                    <span class="badge-ok">Vous êtes ici</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>

                @if ($talents->isNotEmpty())
                    <section class="flex flex-col gap-3.5">
                        <div class="flex items-baseline justify-between">
                            <h2 class="text-[20px] font-bold text-navy-950">Talents récents</h2>
                            <a href="{{ route('talents.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-brand-600 hover:gap-2 transition-all">Tout voir <x-icon name="arrow" :size="16" /></a>
                        </div>
                        <div class="grid gap-3 xl:grid-cols-2">
                            @foreach ($talents as $t)
                                @include('app.talents._card', ['profile' => $t])
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            {{-- Colonne latérale --}}
            <aside class="flex flex-col gap-6 lg:sticky lg:top-10">
                @if ($announcement)
                    <section class="flex flex-col gap-3.5">
                        <h2 class="text-[20px] font-bold text-navy-950">Dernière annonce</h2>
                        <a href="{{ route('chat.show', $super) }}" class="card card-hover group relative flex flex-col gap-3 overflow-hidden p-5">
                            <span class="absolute inset-y-0 left-0 w-1 bg-linear-to-b from-gold-400 to-gold-500"></span>
                            <span class="flex items-center gap-2.5">
                                <x-logo :size="34" />
                                <span class="flex min-w-0 flex-col leading-tight">
                                    <span class="truncate text-[13.5px] font-bold">Communauté Back to Burkina</span>
                                    <span class="text-xs text-ink-500">{{ $announcement->created_at->diffForHumans() }}</span>
                                </span>
                                <span class="badge-gold ml-auto">Officiel</span>
                            </span>
                            <span class="text-[14.5px] leading-relaxed text-ink-800">{{ \Illuminate\Support\Str::limit($announcement->body, 160) }}</span>
                            <span class="inline-flex items-center gap-1 text-[13px] font-bold text-brand-600">Lire dans la communauté <x-icon name="arrow" :size="15" class="transition group-hover:translate-x-0.5" /></span>
                        </a>
                    </section>
                @endif

                <section class="flex flex-col gap-3.5">
                    <h2 class="text-[20px] font-bold text-navy-950">Vu dans la presse</h2>
                    <a href="https://burkina24.com/?p=454535" target="_blank" rel="noopener" class="group relative flex min-h-[160px] flex-col justify-end gap-1.5 overflow-hidden rounded-2xl bg-navy-950 p-5 text-white shadow-card">
                        <div class="bg-weave pointer-events-none absolute inset-0 opacity-40"></div>
                        <span class="absolute right-5 top-5 flex size-12 items-center justify-center rounded-full bg-white text-navy-950 shadow-lg transition group-hover:scale-110"><x-icon name="play" :size="20" /></span>
                        <span class="relative text-xs font-bold uppercase tracking-[0.14em] text-gold-300">Burkina 24 · Reportage</span>
                        <span class="relative font-display text-[17px] font-bold leading-snug">Un entrepreneur au service du retour des compétences</span>
                    </a>
                </section>

                <section class="hidden flex-col gap-3.5 lg:flex">
                    <h2 class="text-[20px] font-bold text-navy-950">Accès rapide</h2>
                    <div class="card flex flex-col p-1.5">
                        @foreach ([
                            [route('chat.show', $super), 'chat', 'Communauté', 'Annonces, entraide, emplois, bourses'],
                            [route('talents.index'), 'users', 'Vivier de talents', 'Diplômés formés à l’étranger'],
                            [route('account'), 'user', 'Mon compte', 'Profil et notifications'],
                        ] as [$url, $icon, $t, $d])
                            <a href="{{ $url }}" class="group flex items-center gap-3 rounded-xl px-3 py-3 transition hover:bg-ink-50">
                                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600 transition group-hover:bg-brand-600 group-hover:text-white"><x-icon :name="$icon" :size="19" /></span>
                                <span class="flex flex-1 flex-col"><span class="text-sm font-bold">{{ $t }}</span><span class="text-xs text-ink-600">{{ $d }}</span></span>
                                <x-icon name="chevron" :size="16" class="text-ink-300 transition group-hover:translate-x-0.5 group-hover:text-brand-600" />
                            </a>
                        @endforeach
                    </div>
                </section>
            </aside>
        </div>
    </div>
</x-layouts.app>
