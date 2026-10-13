<x-layouts.site>
    {{-- Héros --}}
    <section class="bg-hero relative overflow-hidden text-white" data-parallax-scope>
        <div class="bg-weave pointer-events-none absolute inset-y-0 right-0 w-1/2 opacity-50 [mask-image:linear-gradient(to_left,black,transparent)]"></div>
        <div class="pointer-events-none absolute -left-40 top-1/4 size-[420px] animate-drift rounded-full bg-brand-500/25 blur-3xl sm:size-[520px]"></div>
        <div class="pointer-events-none absolute -right-32 bottom-0 size-[360px] animate-drift rounded-full bg-gold-400/15 blur-3xl [animation-delay:-9s]"></div>

        <div class="relative mx-auto grid max-w-[1200px] items-center gap-12 px-4 pb-24 pt-10 sm:px-6 sm:pt-16 lg:grid-cols-[1.15fr_1fr] lg:gap-14 lg:pb-32 lg:pt-24">
            <div class="flex flex-col gap-6 sm:gap-7">
                <span data-reveal="fade" class="glass inline-flex items-center gap-2 self-start rounded-full py-1.5 pl-1.5 pr-3.5 text-[12.5px] font-semibold text-white/90 sm:pr-4 sm:text-[13px]">
                    <span class="rounded-full bg-gold-400 px-2.5 py-0.5 text-xs font-extrabold text-navy-950">Nouveau</span>
                    Votre dossier suivi en ligne
                </span>
                <h1 data-lines class="text-[clamp(2.4rem,8.5vw,5rem)] font-extrabold leading-[0.98] lg:text-[clamp(3.2rem,5.6vw,5rem)]">
                    <span class="block">Étudier en Chine.</span>
                    <span class="block bg-linear-to-r from-gold-200 via-gold-300 to-gold-400 bg-clip-text pb-1 text-transparent">Revenir bâtir le Faso.</span>
                </h1>
                <p data-reveal style="--reveal-delay: 250ms" class="max-w-xl text-[16.5px] leading-relaxed text-white/70 sm:text-lg">Candidature, visa, vie sur place et retour à l’emploi : Travel Express accompagne les étudiants burkinabè du départ jusqu’au retour, et met leurs compétences au service du pays.</p>
                <div data-reveal style="--reveal-delay: 350ms" class="flex flex-col gap-3 min-[460px]:flex-row min-[460px]:flex-wrap">
                    <a href="{{ route('welcome') }}" class="btn-gold shine min-h-14 px-7 text-base">Commencer ma candidature <x-icon name="arrow" :size="18" /></a>
                    <a href="{{ route('register', ['profil' => 'recruiter']) }}" class="btn-outline-light min-h-14 px-7 text-base">Je recrute des talents</a>
                </div>
                <ul data-reveal style="--reveal-delay: 450ms" class="flex flex-wrap gap-x-6 gap-y-2.5 pt-1 text-sm text-white/70">
                    @foreach (['Conseiller dédié', 'Suivi en temps réel', 'Communauté active'] as $item)
                        <li class="flex items-center gap-2"><span class="flex size-5 items-center justify-center rounded-full bg-gold-400/20 text-gold-300"><x-icon name="check" :size="12" stroke-width="3" /></span>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>

            {{-- Aperçu de l'application --}}
            <div data-reveal="zoom" style="--reveal-delay: 200ms" class="relative mx-auto w-full max-w-[400px] sm:max-w-[440px] lg:mx-0 lg:justify-self-end">
                <div class="absolute -inset-6 rounded-[40px] bg-brand-500/20 blur-2xl"></div>
                <div data-parallax="0.12">
                    <div class="relative flex animate-float flex-col gap-4 rounded-[28px] bg-white p-5 text-ink-900 shadow-[0_40px_80px_-30px_rgb(0_0_0/0.6)] sm:p-6">
                        <div class="flex items-center justify-between gap-2">
                            <span class="flex items-center gap-2.5"><x-avatar initials="AO" :size="40" tone="gold" /><span class="flex flex-col leading-tight"><span class="text-sm font-bold">Awa O.</span><span class="text-xs text-ink-500">Futur étudiant</span></span></span>
                            <span class="badge-warn">Admission</span>
                        </div>
                        <div class="flex items-center gap-4 rounded-2xl bg-ink-50 p-4 sm:gap-5">
                            <x-progress-ring :percent="50" :size="76" :stroke="8" />
                            <span class="flex min-w-0 flex-col gap-1">
                                <span class="text-xs font-bold uppercase tracking-[0.12em] text-ink-500">Mon dossier</span>
                                <span class="font-display text-[17px] font-bold leading-tight text-navy-950">Licence informatique</span>
                                <span class="text-[13px] text-ink-600">Étape 3 sur 6</span>
                            </span>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            @foreach ([['Passeport', 'badge-ok', 'Validé'], ['Diplôme du Bac', 'badge-ok', 'Validé'], ['Relevés de notes', 'badge-warn', 'En cours']] as [$doc, $cls, $st])
                                <div class="flex items-center gap-3 rounded-xl px-1 py-1.5">
                                    <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><x-icon name="file" :size="17" /></span>
                                    <span class="min-w-0 flex-1 truncate text-sm font-semibold">{{ $doc }}</span>
                                    <span class="{{ $cls }}">{{ $st }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div data-parallax="0.28" class="absolute -bottom-8 -left-2 max-w-[250px] sm:-left-12">
                    <div class="flex items-start gap-3 rounded-2xl bg-white p-3.5 text-ink-900 shadow-lift">
                        <x-logo :size="36" />
                        <span class="flex flex-col gap-0.5">
                            <span class="flex items-center gap-1.5 text-[12.5px] font-bold">Travel Express <span class="badge-gold px-1.5 py-0 text-[10px]">Officiel</span></span>
                            <span class="text-[12.5px] leading-snug text-ink-600">Nouvelle bourse ouverte, pensez à déposer vos pièces.</span>
                        </span>
                    </div>
                </div>
                <div data-parallax="0.2" class="absolute -right-2 -top-5 sm:-right-8">
                    <div class="flex items-center gap-2 rounded-full bg-white py-2 pl-2 pr-3.5 text-[12.5px] font-bold text-navy-950 shadow-lift">
                        <span class="flex size-7 items-center justify-center rounded-full bg-ok-500 text-white"><x-icon name="check" :size="14" stroke-width="3" /></span> Document validé
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Bandeau défilant --}}
    <div class="border-b border-ink-100 bg-white py-5" aria-hidden="true">
        <div class="fade-edges flex overflow-hidden">
            <div class="flex w-max animate-marquee items-center gap-10 pr-10 hover:[animation-play-state:paused]">
                @foreach (range(1, 2) as $_)
                    @foreach (['Admission', 'Bourses d’études', 'Formulaire JW202', 'Visa X1', 'Accueil à l’arrivée', 'Cours de chinois · HSK', 'Entraide', 'Retour à l’emploi', 'Vivier de talents'] as $word)
                        <span class="flex shrink-0 items-center gap-10 font-display text-[19px] font-bold text-navy-950/70 sm:text-[22px]">{{ $word }} <x-icon name="star" :size="16" class="text-gold-400" /></span>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>

    {{-- Promesse --}}
    <section class="mx-auto max-w-[1200px] px-4 pt-16 sm:px-6 sm:pt-20">
        <div class="grid gap-4 sm:grid-cols-3" data-reveal-stagger="110">
            @foreach ([['folder', 'Un dossier suivi en ligne', 'Chaque étape visible, du choix du programme au billet.'], ['chat', 'Un conseiller qui répond', 'Posez vos questions par message, sans vous déplacer.'], ['briefcase', 'Un retour préparé', 'Votre profil mis en avant auprès des recruteurs.']] as [$icon, $t, $d])
                <div data-reveal class="card card-hover flex items-start gap-4 p-6">
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-brand-500 to-brand-600 text-white shadow-glow"><x-icon :name="$icon" :size="22" /></span>
                    <span class="flex flex-col gap-1"><span class="font-display text-[17px] font-bold text-navy-950">{{ $t }}</span><span class="text-sm leading-relaxed text-ink-600">{{ $d }}</span></span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Chiffres clés --}}
    <section class="mx-auto max-w-[1200px] px-4 pt-6 sm:px-6">
        <div class="grid grid-cols-2 gap-px overflow-hidden rounded-3xl bg-ink-100 ring-1 ring-ink-100 lg:grid-cols-4" data-reveal="fade">
            @foreach ([[6, 'étapes', 'suivies en ligne, de l’inscription au départ'], [3, 'temps', 'partir, étudier, revenir'], [4, 'thèmes', 'dans la communauté : annonces, entraide, emplois, bourses'], [1, 'conseiller', 'dédié à votre dossier']] as [$n, $unit, $d])
                <div class="flex flex-col gap-1 bg-white p-5 sm:p-7">
                    <span class="flex items-baseline gap-1.5"><span data-count="{{ $n }}" class="font-display text-[42px] font-extrabold leading-none text-navy-950 tabular-nums sm:text-[52px]">{{ $n }}</span><span class="font-display text-lg font-bold text-brand-600">{{ $unit }}</span></span>
                    <span class="text-[13.5px] leading-snug text-ink-600">{{ $d }}</span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Parcours --}}
    <section id="parcours" class="mx-auto flex max-w-[1200px] scroll-mt-24 flex-col gap-10 py-16 sm:gap-12 sm:py-24 lg:py-32">
        <div class="flex flex-wrap items-end justify-between gap-6 px-4 sm:px-6">
            <div data-reveal class="flex max-w-2xl flex-col gap-4">
                <span class="eyebrow">Le parcours</span>
                <h2 class="text-[clamp(2rem,5.5vw,3.2rem)] font-extrabold leading-[1.05] text-navy-950">Du départ au retour,<br>on ne vous lâche pas.</h2>
            </div>
            <div data-reveal style="--reveal-delay: 120ms" class="flex max-w-md flex-col gap-3">
                <p class="text-[16px] leading-relaxed text-ink-600">Trois temps, une seule équipe. Chaque étape de votre dossier est visible dans votre espace, sur le web comme sur l’application.</p>
                <a href="{{ route('study') }}" class="inline-flex items-center gap-1.5 self-start text-[15px] font-bold text-brand-600 transition-all hover:gap-2.5">Le guide complet pour étudier en Chine <x-icon name="arrow" :size="17" /></a>
            </div>
        </div>

        <div class="snap-row md:mx-6 md:grid md:grid-cols-3 md:gap-5 md:overflow-visible md:p-0" data-reveal-stagger="120">
            @foreach ([
                ['plane', 'Partir', 'Nous vous aidons à choisir la bonne université et à constituer un dossier solide.', ['Choix du programme et des bourses', 'Dossier d’admission et JW202', 'Visa étudiant et billet']],
                ['graduation', 'Étudier', 'Sur place, vous gardez le lien avec l’agence et avec les autres étudiants burkinabè.', ['Accueil à l’arrivée', 'Groupes par ville et par promo', 'Fiches pratiques : HSK, banque, santé']],
                ['briefcase', 'Revenir', 'Votre diplôme en poche, votre profil est mis en avant auprès des recruteurs.', ['Profil dans le vivier de talents', 'Offres d’emploi ciblées', 'Appui aux projets de retour']],
            ] as [$icon, $t, $d, $items])
                <article data-reveal class="card card-hover group relative flex w-[82%] max-w-[340px] flex-col gap-5 overflow-hidden p-6 sm:p-7 md:w-auto md:max-w-none">
                    <span class="pointer-events-none absolute -right-4 -top-6 font-display text-[120px] font-extrabold leading-none text-ink-50 transition duration-500 group-hover:-translate-y-1 group-hover:text-brand-50">{{ $loop->iteration }}</span>
                    <span class="relative flex size-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600 transition duration-300 group-hover:bg-linear-to-br group-hover:from-brand-500 group-hover:to-brand-600 group-hover:text-white group-hover:shadow-glow"><x-icon :name="$icon" :size="26" /></span>
                    <div class="relative flex flex-col gap-2">
                        <h3 class="text-[26px] font-bold text-navy-950">{{ $t }}</h3>
                        <p class="text-[15px] leading-relaxed text-ink-600">{{ $d }}</p>
                    </div>
                    <ul class="relative mt-auto flex flex-col gap-2.5 border-t border-ink-100 pt-5">
                        @foreach ($items as $it)
                            <li class="flex items-center gap-2.5 text-sm font-semibold text-ink-800"><span class="flex size-5 shrink-0 items-center justify-center rounded-full bg-ok-50 text-ok-700"><x-icon name="check" :size="12" stroke-width="3" /></span>{{ $it }}</li>
                        @endforeach
                    </ul>
                </article>
            @endforeach
        </div>

        {{-- Étapes du dossier --}}
        <div class="px-4 sm:px-6">
            <div data-reveal class="bg-hero relative overflow-hidden rounded-[28px] p-6 text-white sm:rounded-[32px] sm:p-10 lg:p-14">
                <div class="bg-weave pointer-events-none absolute inset-y-0 right-0 w-1/3 opacity-50 [mask-image:linear-gradient(to_left,black,transparent)]"></div>
                <div class="relative flex flex-col gap-10">
                    <div class="flex flex-wrap items-end justify-between gap-5">
                        <div class="flex flex-col gap-3">
                            <span class="eyebrow text-gold-300">Votre dossier</span>
                            <h2 class="text-[clamp(1.7rem,4.5vw,2.4rem)] font-bold">Six étapes, suivies en ligne</h2>
                        </div>
                        <a href="{{ route('welcome') }}" class="btn-light">Ouvrir mon dossier <x-icon name="arrow" :size="18" /></a>
                    </div>
                    <div class="relative">
                        <span data-draw class="absolute left-[22px] right-[8%] top-[22px] hidden h-px bg-linear-to-r from-gold-300 via-white/40 to-transparent lg:block"></span>
                        <ol class="relative grid gap-x-6 gap-y-7 min-[480px]:grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 lg:gap-4" data-reveal-stagger="90">
                            @foreach (\App\Models\Dossier::STEPS as $n => [$t, $d])
                                <li data-reveal class="flex gap-4 lg:flex-col lg:gap-3">
                                    <span class="flex size-11 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-gold-300 to-gold-500 font-display text-lg font-extrabold text-navy-950 shadow-[0_8px_20px_-8px_rgb(200_146_43/0.8)]">{{ $n }}</span>
                                    <span class="flex flex-col gap-1">
                                        <span class="text-[15.5px] font-bold">{{ $t }}</span>
                                        <span class="text-[13.5px] leading-normal text-white/60">{{ $d }}</span>
                                    </span>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Le projet --}}
    <section class="bg-canvas">
        <div class="mx-auto grid max-w-[1200px] items-center gap-14 px-4 py-16 sm:px-6 sm:py-24 lg:grid-cols-[0.9fr_1.1fr] lg:py-32">
            <div data-reveal="left" class="relative mx-auto w-full max-w-[340px] sm:max-w-md">
                <div class="absolute -inset-3 rotate-3 rounded-[36px] bg-linear-to-br from-gold-200 to-gold-400 opacity-60 sm:-inset-4"></div>
                <div class="bg-hero relative flex aspect-[4/5] items-center justify-center overflow-hidden rounded-[32px] text-[15px] font-semibold text-white/70 shadow-lift">
                    <div class="bg-weave absolute inset-0 opacity-40"></div>
                    <span class="relative flex flex-col items-center gap-3"><x-icon name="camera" :size="32" />[Photo du fondateur]</span>
                </div>
                <div class="absolute -bottom-6 -right-2 max-w-[260px] rounded-2xl bg-white px-5 py-4 shadow-lift sm:-right-10">
                    <span class="block font-display text-[15px] font-bold text-navy-950">Yikonpin Armel Razak Bakoua</span>
                    <span class="text-[13px] text-ink-600">Directeur général, Travel Express</span>
                </div>
            </div>
            <div data-reveal="right" class="flex flex-col gap-6 pt-4 lg:pt-0">
                <span class="eyebrow">Le projet</span>
                <h2 class="text-[clamp(2rem,5.5vw,3.2rem)] font-extrabold leading-[1.05] text-navy-950">Les compétences acquises ailleurs, <span class="text-brand-600">réinvesties ici.</span></h2>
                <p class="text-[16.5px] leading-relaxed text-ink-700 sm:text-[17px]">Yikonpin Armel Razak Bakoua, directeur général de Travel Express, a suivi des études d’ingénieur en informatique en Chine avant de choisir de rentrer au Burkina. Avec Back to Burkina, il veut que chaque étudiant parti se former à l’étranger puisse, à son retour, mettre son savoir au service du pays.</p>
                <a href="https://burkina24.com/?p=454535" target="_blank" rel="noopener" class="card card-hover group flex items-center gap-4 self-start py-3 pl-3 pr-5">
                    <span class="flex size-12 items-center justify-center rounded-xl bg-navy-950 text-white transition group-hover:scale-105"><x-icon name="play" :size="20" /></span>
                    <span class="flex flex-col"><span class="text-xs font-semibold text-ink-500">Vu dans la presse · Burkina 24</span><span class="font-bold text-navy-950">Lire le reportage</span></span>
                    <x-icon name="external" :size="16" class="ml-2 text-ink-400 transition group-hover:translate-x-0.5 group-hover:text-brand-600" />
                </a>
            </div>
        </div>
    </section>

    {{-- Communauté --}}
    <section id="communaute" class="mx-auto grid max-w-[1200px] scroll-mt-24 items-center gap-12 px-4 py-16 sm:px-6 sm:py-24 lg:grid-cols-2 lg:gap-14 lg:py-32">
        <div class="flex flex-col gap-6">
            <div data-reveal class="flex flex-col gap-4">
                <span class="eyebrow">La communauté</span>
                <h2 class="text-[clamp(2rem,5.5vw,3.2rem)] font-extrabold leading-[1.05] text-navy-950">Personne ne part seul.</h2>
                <p class="text-[16.5px] leading-relaxed text-ink-600 sm:text-[17px]">Dès l’inscription, vous rejoignez le super groupe qui réunit tous les membres : futurs étudiants, étudiants en Chine, diplômés et recruteurs. Créez aussi vos propres groupes, par ville, par promo ou par projet.</p>
            </div>
            <div class="grid grid-cols-2 gap-3" data-reveal-stagger="80">
                @foreach ([['bell', 'Annonces', 'Infos officielles de Travel Express'], ['help', 'Entraide', 'Questions et conseils entre membres'], ['briefcase', 'Emplois', 'Offres publiées par les recruteurs'], ['graduation', 'Bourses', 'Nouvelles bourses et dates limites']] as [$icon, $t, $d])
                    <div data-reveal class="card card-hover flex flex-col gap-2 p-4">
                        <span class="flex size-9 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><x-icon :name="$icon" :size="18" /></span>
                        <span class="font-bold text-navy-950">{{ $t }}</span>
                        <span class="text-[13px] leading-snug text-ink-600 sm:text-[13.5px]">{{ $d }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div data-reveal="zoom" class="relative">
            <div class="absolute -inset-3 rounded-[36px] bg-linear-to-br from-brand-100 to-brand-50 sm:-inset-4"></div>
            <div class="relative flex flex-col overflow-hidden rounded-[28px] bg-white shadow-lift ring-1 ring-ink-900/5">
                <div class="flex items-center gap-3 border-b border-ink-100 px-4 py-4 sm:px-5">
                    <x-logo :size="42" />
                    <span class="flex min-w-0 flex-1 flex-col"><span class="truncate font-bold text-navy-950">Communauté Back to Burkina</span><span class="flex items-center gap-1.5 text-[12.5px] text-ink-500"><span class="relative flex size-1.5"><span class="absolute inset-0 animate-ping rounded-full bg-ok-500"></span><span class="relative size-1.5 rounded-full bg-ok-500"></span></span> Super groupe · tous les membres</span></span>
                </div>
                <div class="flex gap-2 overflow-hidden border-b border-ink-100 px-4 py-3 sm:px-5">
                    @foreach (['Annonces', 'Entraide', 'Emplois', 'Bourses'] as $i => $tab)
                        <span @class(['shrink-0 rounded-full px-3 py-1.5 text-xs font-semibold', 'bg-navy-900 text-white' => $i === 1, 'bg-ink-50 text-ink-600' => $i !== 1])>{{ $tab }}</span>
                    @endforeach
                </div>
                <div class="bg-dots flex flex-col gap-3 bg-canvas p-4 sm:p-5" data-reveal-stagger="380">
                    <div data-reveal class="flex max-w-[88%] items-end gap-2 self-start">
                        <x-avatar initials="TE" :size="30" tone="gold" />
                        <div class="rounded-[20px] rounded-bl-md bg-white px-3.5 py-2.5 shadow-card"><span class="flex items-center gap-1.5 text-[12.5px] font-bold text-brand-700">Travel Express <span class="badge-gold px-1.5 py-0 text-[10px]">Officiel</span></span><span class="text-sm">Nouvelle bourse ouverte, date limite le [date].</span></div>
                    </div>
                    <div data-reveal class="flex max-w-[88%] items-end gap-2 self-start">
                        <x-avatar initials="IS" :size="30" />
                        <div class="rounded-[20px] rounded-bl-md bg-white px-3.5 py-2.5 shadow-card"><span class="text-[12.5px] font-bold text-brand-700">Issouf · étudiant en Chine</span><p class="text-sm">Je peux partager mon dossier de candidature comme exemple si ça aide.</p></div>
                    </div>
                    <div data-reveal="right" class="max-w-[78%] self-end rounded-[20px] rounded-br-md bg-linear-to-br from-brand-500 to-brand-600 px-3.5 py-2.5 text-sm text-white shadow-[0_6px_16px_-8px_rgb(0_113_227/0.6)]">Avec plaisir, merci Issouf !</div>
                    <div data-reveal class="flex items-center gap-1.5 self-start rounded-full bg-white px-3 py-2 shadow-card" aria-hidden="true">
                        @foreach ([0, 150, 300] as $delay)
                            <span class="size-1.5 animate-bounce rounded-full bg-ink-400" style="animation-delay: {{ $delay }}ms"></span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Talents --}}
    <section id="talents" class="scroll-mt-24 bg-canvas">
        <div class="mx-auto flex max-w-[1200px] flex-col gap-10 py-16 sm:gap-12 sm:py-24 lg:py-32">
            <div class="flex flex-wrap items-end justify-between gap-6 px-4 sm:px-6">
                <div data-reveal class="flex max-w-2xl flex-col gap-4">
                    <span class="eyebrow">Le vivier de talents</span>
                    <h2 class="text-[clamp(2rem,5.5vw,3.2rem)] font-extrabold leading-[1.05] text-navy-950">Formés en Chine,<br>prêts pour le Burkina.</h2>
                </div>
                <a data-reveal style="--reveal-delay: 120ms" href="{{ route('register', ['profil' => 'recruiter']) }}" class="btn-secondary">Accéder au vivier <x-icon name="arrow" :size="18" /></a>
            </div>
            @if ($talents->isNotEmpty())
                <div class="snap-row sm:mx-6 sm:grid sm:grid-cols-2 sm:gap-5 sm:overflow-visible sm:p-0 lg:grid-cols-4" data-reveal-stagger="100">
                    @foreach ($talents as $t)
                        <div data-reveal class="card card-hover flex w-[74%] max-w-[290px] flex-col gap-3.5 p-6 sm:w-auto sm:max-w-none">
                            <div class="flex items-start justify-between">
                                <span class="relative"><x-avatar :user="$t->user" :size="56" /><span class="absolute -bottom-0.5 -right-0.5 flex size-5 items-center justify-center rounded-full bg-brand-600 text-white ring-2 ring-white"><x-icon name="check" :size="11" stroke-width="3.4" /></span></span>
                                <span class="badge-ok">Vérifié</span>
                            </div>
                            <span class="font-display text-[18px] font-bold text-navy-950">{{ $t->user->first_name }} {{ mb_substr($t->user->last_name, 0, 1) }}.</span>
                            <span class="text-sm leading-snug text-ink-600">{{ $t->headline }}</span>
                            @if ($t->hsk_level)<span class="mt-auto self-start rounded-lg bg-gold-50 px-2.5 py-1 text-xs font-bold text-gold-700 ring-1 ring-gold-200">Chinois {{ $t->hsk_level }}</span>@endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-4 sm:px-6"><x-empty icon="users" title="Les premiers profils arrivent bientôt" text="Les diplômés vérifiés par Travel Express apparaîtront ici." /></div>
            @endif
        </div>
    </section>

    {{-- Entreprises --}}
    <section id="entreprises" class="mx-auto grid max-w-[1200px] scroll-mt-24 gap-5 px-4 py-16 sm:px-6 sm:py-24 lg:grid-cols-[1.4fr_1fr] lg:py-32">
        <div data-reveal="left" class="bg-hero relative flex flex-col gap-6 overflow-hidden rounded-[28px] p-7 text-white sm:rounded-[32px] sm:p-10 lg:p-14">
            <div class="bg-weave pointer-events-none absolute inset-y-0 right-0 w-1/2 opacity-50 [mask-image:linear-gradient(to_left,black,transparent)]"></div>
            <div class="pointer-events-none absolute -bottom-24 -right-16 size-72 animate-drift rounded-full bg-brand-500/25 blur-3xl"></div>
            <span class="eyebrow relative text-gold-300">Entreprises</span>
            <h2 class="relative text-[clamp(1.9rem,5vw,2.8rem)] font-extrabold leading-[1.05]">Recrutez des profils formés en Chine.</h2>
            <p class="relative max-w-xl text-base leading-relaxed text-white/70">Ingénieurs, techniciens, médecins, commerciaux : des diplômés vérifiés par Travel Express, qui parlent français et chinois. Idéal pour les entreprises burkinabè et les sociétés chinoises installées au pays.</p>
            <div class="relative mt-2 flex flex-col gap-3 min-[460px]:flex-row min-[460px]:flex-wrap">
                <a href="{{ route('register', ['profil' => 'recruiter']) }}" class="btn-gold shine">Créer un compte recruteur <x-icon name="arrow" :size="18" /></a>
                <a href="{{ route('companies') }}" class="btn-outline-light">En savoir plus</a>
            </div>
        </div>
        <div class="flex flex-col gap-5" data-reveal-stagger="110">
            @foreach ([['search', 'Recherche ciblée', 'Filtrez par domaine, niveau de chinois, ville et disponibilité.'], ['chat', 'Offres dans la communauté', 'Publiez vos offres dans le thème Emplois du super groupe.'], ['shield', 'Profils vérifiés', 'Chaque diplôme est contrôlé par Travel Express avant publication.']] as [$icon, $t, $d])
                <div data-reveal="right" class="card card-hover flex flex-1 items-start gap-4 p-6">
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"><x-icon :name="$icon" :size="22" /></span>
                    <span class="flex flex-col gap-1"><span class="font-display text-lg font-bold text-navy-950">{{ $t }}</span><span class="text-[15px] leading-relaxed text-ink-600">{{ $d }}</span></span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- FAQ --}}
    <section id="faq" class="scroll-mt-24 bg-canvas">
        <div class="mx-auto grid max-w-[1200px] gap-10 px-4 py-16 sm:px-6 sm:py-24 lg:grid-cols-[0.8fr_1.2fr] lg:gap-12 lg:py-32" x-data="{ open: 0 }">
            <div data-reveal class="flex flex-col gap-4 lg:sticky lg:top-28 lg:self-start">
                <span class="eyebrow">FAQ</span>
                <h2 class="text-[clamp(2rem,5.5vw,3rem)] font-extrabold leading-[1.05] text-navy-950">Questions fréquentes</h2>
                <p class="text-[16px] leading-relaxed text-ink-600">Les réponses aux questions qu’on nous pose le plus. Vous ne trouvez pas la vôtre ? Écrivez-nous.</p>
                <div class="mt-2 flex flex-col gap-2.5 min-[460px]:flex-row lg:flex-col xl:flex-row">
                    <a href="{{ route('faq') }}" class="btn-primary">Toutes les questions <x-icon name="arrow" :size="18" /></a>
                    <a href="{{ route('contact') }}" class="btn-secondary">Poser ma question</a>
                </div>
            </div>
            <div class="flex flex-col gap-3" data-reveal-stagger="80">
                @foreach ($faq as $i => [$q, $a])
                    <div data-reveal class="card overflow-hidden transition duration-300" :class="open === {{ $i }} && 'ring-brand-200 shadow-lift'">
                        <button type="button" @click="open = open === {{ $i }} ? -1 : {{ $i }}" :aria-expanded="open === {{ $i }}" class="flex min-h-[68px] w-full items-center justify-between gap-4 px-5 py-4 text-left font-display text-[16.5px] font-bold text-navy-950 sm:px-6 sm:text-[17px]">
                            {{ $q }}
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-full transition duration-300" :class="open === {{ $i }} ? 'rotate-45 bg-brand-600 text-white' : 'bg-ink-50 text-ink-700'"><x-icon name="plus" :size="18" stroke-width="2.2" /></span>
                        </button>
                        {{-- Ouverture fluide : la hauteur passe de 0fr à 1fr --}}
                        <div class="grid transition-[grid-template-rows] duration-400 ease-[cubic-bezier(0.2,0.7,0.2,1)]" :class="open === {{ $i }} ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'">
                            <div class="overflow-hidden">
                                <p class="px-5 pb-6 text-[15.5px] leading-relaxed text-ink-600 transition-opacity duration-300 sm:px-6" :class="open === {{ $i }} ? 'opacity-100' : 'opacity-0'">{{ $a }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Appel final --}}
    <section class="mx-auto max-w-[1200px] px-4 py-16 sm:px-6 sm:py-24">
        <div data-reveal="zoom" class="relative overflow-hidden rounded-[28px] bg-linear-to-br from-brand-500 to-brand-700 px-6 py-12 text-white shadow-lift sm:rounded-[36px] sm:px-12 sm:py-14 lg:px-16">
            <div class="bg-weave pointer-events-none absolute inset-0 opacity-40"></div>
            <div class="pointer-events-none absolute -right-20 -top-20 size-72 animate-drift rounded-full bg-white/15 blur-2xl"></div>
            <div class="pointer-events-none absolute -bottom-24 -left-10 size-64 animate-drift rounded-full bg-gold-300/20 blur-2xl [animation-delay:-7s]"></div>
            <div class="relative flex flex-wrap items-center justify-between gap-8">
                <div class="flex max-w-xl flex-col gap-3">
                    <h2 class="text-[clamp(1.9rem,5vw,2.8rem)] font-extrabold leading-[1.05]">Votre dossier, toujours dans la poche.</h2>
                    <p class="text-[16px] leading-relaxed text-white/80">Les applications Android et iPhone arrivent bientôt. En attendant, installez Back to Burkina sur votre téléphone directement depuis le navigateur.</p>
                </div>
                <div class="flex w-full flex-col gap-3 min-[460px]:w-auto min-[460px]:flex-row min-[460px]:flex-wrap" x-data="{ prompt: null }" @beforeinstallprompt.window.prevent="prompt = $event">
                    <button type="button" x-show="prompt" x-cloak @click="prompt.prompt(); prompt = null" class="btn-light"><x-icon name="phone" :size="18" /> Installer l’application</button>
                    <span class="glass inline-flex min-h-12 items-center justify-center gap-2 rounded-xl px-5 text-[14px] font-semibold text-white/85"><x-icon name="clock" :size="17" /> Bientôt sur Android et iPhone</span>
                </div>
            </div>
        </div>
    </section>
</x-layouts.site>
