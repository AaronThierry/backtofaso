<x-layouts.site>
    {{-- Héros --}}
    <section class="bg-hero relative overflow-hidden text-white">
        <div class="bg-weave pointer-events-none absolute inset-y-0 right-0 w-1/2 opacity-50 [mask-image:linear-gradient(to_left,black,transparent)]"></div>
        <div class="pointer-events-none absolute -left-40 top-1/3 size-[480px] rounded-full bg-brand-500/20 blur-3xl"></div>

        <div class="relative mx-auto grid max-w-[1200px] items-center gap-14 px-4 pb-24 pt-16 sm:px-6 lg:grid-cols-[1.15fr_1fr] lg:pb-32 lg:pt-24">
            <div class="flex animate-rise flex-col gap-7">
                <span class="glass inline-flex items-center gap-2 self-start rounded-full py-1.5 pl-1.5 pr-4 text-[13px] font-semibold text-white/90">
                    <span class="rounded-full bg-gold-400 px-2.5 py-0.5 text-xs font-extrabold text-navy-950">Nouveau</span>
                    Suivez votre dossier en ligne, étape par étape
                </span>
                <h1 class="text-[clamp(2.7rem,6vw,5rem)] font-extrabold leading-[0.98]">
                    Étudier en Chine.<br>
                    <span class="bg-linear-to-r from-gold-200 via-gold-300 to-gold-400 bg-clip-text text-transparent">Revenir bâtir le Faso.</span>
                </h1>
                <p class="max-w-xl text-lg leading-relaxed text-white/70">Candidature, visa, vie sur place et retour à l’emploi : Travel Express accompagne les étudiants burkinabè du départ jusqu’au retour, et met leurs compétences au service du pays.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('welcome') }}" class="btn-gold min-h-14 px-7 text-base">Commencer ma candidature <x-icon name="arrow" :size="18" /></a>
                    <a href="{{ route('register', ['profil' => 'recruiter']) }}" class="btn-outline-light min-h-14 px-7 text-base">Je recrute des talents</a>
                </div>
                <ul class="flex flex-wrap gap-x-6 gap-y-2 pt-2 text-sm text-white/70">
                    @foreach (['Conseiller dédié', 'Suivi en temps réel', 'Communauté active'] as $item)
                        <li class="flex items-center gap-2"><span class="flex size-5 items-center justify-center rounded-full bg-gold-400/20 text-gold-300"><x-icon name="check" :size="12" stroke-width="3" /></span>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>

            {{-- Aperçu de l'application --}}
            <div class="relative mx-auto w-full max-w-[440px] lg:mx-0 lg:justify-self-end">
                <div class="absolute -inset-6 rounded-[40px] bg-brand-500/20 blur-2xl"></div>
                <div class="relative flex animate-float flex-col gap-4 rounded-[28px] bg-white p-6 text-ink-900 shadow-[0_40px_80px_-30px_rgb(0_0_0/0.6)]">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2.5"><x-avatar initials="AO" :size="40" tone="gold" /><span class="flex flex-col leading-tight"><span class="text-sm font-bold">Awa O.</span><span class="text-xs text-ink-500">Futur étudiant</span></span></span>
                        <span class="badge-warn">Admission en cours</span>
                    </div>
                    <div class="flex items-center gap-5 rounded-2xl bg-ink-50 p-4">
                        <x-progress-ring :percent="50" :size="80" :stroke="8" />
                        <span class="flex flex-col gap-1">
                            <span class="text-xs font-bold uppercase tracking-[0.12em] text-ink-500">Mon dossier</span>
                            <span class="font-display text-lg font-bold text-navy-950">Licence informatique</span>
                            <span class="text-[13px] text-ink-600">Étape 3 sur 6</span>
                        </span>
                    </div>
                    <div class="flex flex-col gap-2">
                        @foreach ([['Passeport', 'badge-ok', 'Validé'], ['Diplôme du Bac', 'badge-ok', 'Validé'], ['Relevés de notes', 'badge-warn', 'En vérification']] as [$doc, $cls, $st])
                            <div class="flex items-center gap-3 rounded-xl px-1 py-1.5">
                                <span class="flex size-9 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><x-icon name="file" :size="17" /></span>
                                <span class="flex-1 text-sm font-semibold">{{ $doc }}</span>
                                <span class="{{ $cls }}">{{ $st }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="absolute -bottom-8 -left-4 flex max-w-[260px] items-start gap-3 rounded-2xl bg-white p-3.5 text-ink-900 shadow-lift sm:-left-12">
                    <x-logo :size="36" />
                    <span class="flex flex-col gap-0.5">
                        <span class="flex items-center gap-1.5 text-[12.5px] font-bold">Travel Express <span class="badge-gold px-1.5 py-0 text-[10px]">Officiel</span></span>
                        <span class="text-[12.5px] leading-snug text-ink-600">Nouvelle bourse ouverte, pensez à déposer vos pièces.</span>
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- Promesse --}}
    <section class="relative z-10 mx-auto -mt-12 max-w-[1200px] px-4 sm:px-6">
        <div class="card grid divide-y divide-ink-100 sm:grid-cols-3 sm:divide-x sm:divide-y-0">
            @foreach ([['folder', 'Un dossier suivi en ligne', 'Chaque étape visible, du choix du programme au billet.'], ['chat', 'Un conseiller qui répond', 'Posez vos questions par message, sans vous déplacer.'], ['briefcase', 'Un retour préparé', 'Votre profil mis en avant auprès des recruteurs.']] as [$icon, $t, $d])
                <div class="flex items-start gap-4 p-6">
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-brand-500 to-brand-600 text-white shadow-glow"><x-icon :name="$icon" :size="22" /></span>
                    <span class="flex flex-col gap-1"><span class="font-display text-[17px] font-bold text-navy-950">{{ $t }}</span><span class="text-sm leading-relaxed text-ink-600">{{ $d }}</span></span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Parcours --}}
    <section id="parcours" class="mx-auto flex max-w-[1200px] scroll-mt-24 flex-col gap-12 px-4 py-24 sm:px-6 lg:py-32">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <div class="flex max-w-2xl flex-col gap-4">
                <span class="eyebrow">Le parcours</span>
                <h2 class="text-[clamp(2rem,3.6vw,3.2rem)] font-extrabold leading-[1.05] text-navy-950">Du départ au retour,<br>on ne vous lâche pas.</h2>
            </div>
            <p class="max-w-md text-[16px] leading-relaxed text-ink-600">Trois temps, une seule équipe. Chaque étape de votre dossier est visible dans votre espace, sur le web comme sur l’application.</p>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
            @foreach ([
                ['plane', 'Partir', 'Nous vous aidons à choisir la bonne université et à constituer un dossier solide.', ['Choix du programme et des bourses', 'Dossier d’admission et JW202', 'Visa étudiant et billet']],
                ['graduation', 'Étudier', 'Sur place, vous gardez le lien avec l’agence et avec les autres étudiants burkinabè.', ['Accueil à l’arrivée', 'Groupes par ville et par promo', 'Fiches pratiques : HSK, banque, santé']],
                ['briefcase', 'Revenir', 'Votre diplôme en poche, votre profil est mis en avant auprès des recruteurs.', ['Profil dans le vivier de talents', 'Offres d’emploi ciblées', 'Appui aux projets de retour']],
            ] as [$icon, $t, $d, $items])
                <article class="card card-hover group relative flex flex-col gap-5 overflow-hidden p-7">
                    <span class="pointer-events-none absolute -right-4 -top-6 font-display text-[120px] font-extrabold leading-none text-ink-50 transition group-hover:text-brand-50">{{ $loop->iteration }}</span>
                    <span class="relative flex size-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600 transition group-hover:bg-linear-to-br group-hover:from-brand-500 group-hover:to-brand-600 group-hover:text-white group-hover:shadow-glow"><x-icon :name="$icon" :size="26" /></span>
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
        <div class="bg-hero relative overflow-hidden rounded-[32px] p-[clamp(24px,4vw,56px)] text-white">
            <div class="bg-weave pointer-events-none absolute inset-y-0 right-0 w-1/3 opacity-50 [mask-image:linear-gradient(to_left,black,transparent)]"></div>
            <div class="relative flex flex-col gap-10">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div class="flex flex-col gap-3">
                        <span class="eyebrow text-gold-300">Votre dossier</span>
                        <h2 class="text-[clamp(1.7rem,3vw,2.4rem)] font-bold">Six étapes, suivies en ligne</h2>
                    </div>
                    <a href="{{ route('welcome') }}" class="btn-light">Ouvrir mon dossier <x-icon name="arrow" :size="18" /></a>
                </div>
                <ol class="grid gap-6 sm:grid-cols-3 lg:grid-cols-6 lg:gap-4">
                    @foreach (\App\Models\Dossier::STEPS as $n => [$t, $d])
                        <li class="flex flex-col gap-3">
                            <span class="flex items-center gap-3">
                                <span class="flex size-11 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-gold-300 to-gold-500 font-display text-lg font-extrabold text-navy-950">{{ $n }}</span>
                                @unless ($loop->last)<span class="hidden h-px flex-1 bg-linear-to-r from-white/40 to-transparent lg:block"></span>@endunless
                            </span>
                            <span class="text-[15.5px] font-bold">{{ $t }}</span>
                            <span class="text-[13.5px] leading-normal text-white/60">{{ $d }}</span>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </section>

    {{-- Le projet --}}
    <section class="bg-canvas">
        <div class="mx-auto grid max-w-[1200px] items-center gap-14 px-4 py-24 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:py-32">
            <div class="relative mx-auto w-full max-w-md">
                <div class="absolute -inset-4 rotate-3 rounded-[36px] bg-linear-to-br from-gold-200 to-gold-400 opacity-60"></div>
                <div class="bg-hero relative flex aspect-[4/5] items-center justify-center overflow-hidden rounded-[32px] text-[15px] font-semibold text-white/70 shadow-lift">
                    <div class="bg-weave absolute inset-0 opacity-40"></div>
                    <span class="relative flex flex-col items-center gap-3"><x-icon name="camera" :size="32" />[Photo du fondateur]</span>
                </div>
                <div class="absolute -bottom-6 -right-4 rounded-2xl bg-white px-5 py-4 shadow-lift sm:-right-10">
                    <span class="block font-display text-[15px] font-bold text-navy-950">Yikonpin Armel Razak Bakoua</span>
                    <span class="text-[13px] text-ink-600">Directeur général, Travel Express</span>
                </div>
            </div>
            <div class="flex flex-col gap-6">
                <span class="eyebrow">Le projet</span>
                <h2 class="text-[clamp(2rem,3.6vw,3.2rem)] font-extrabold leading-[1.05] text-navy-950">Les compétences acquises ailleurs, <span class="text-brand-600">réinvesties ici.</span></h2>
                <p class="text-[17px] leading-relaxed text-ink-700">Yikonpin Armel Razak Bakoua, directeur général de Travel Express, a suivi des études d’ingénieur en informatique en Chine avant de choisir de rentrer au Burkina. Avec Back to Burkina, il veut que chaque étudiant parti se former à l’étranger puisse, à son retour, mettre son savoir au service du pays.</p>
                <a href="https://burkina24.com/?p=454535" target="_blank" rel="noopener" class="card card-hover group flex items-center gap-4 self-start py-3 pl-3 pr-5">
                    <span class="flex size-12 items-center justify-center rounded-xl bg-navy-950 text-white transition group-hover:scale-105"><x-icon name="play" :size="20" /></span>
                    <span class="flex flex-col"><span class="text-xs font-semibold text-ink-500">Vu dans la presse · Burkina 24</span><span class="font-bold text-navy-950">Lire le reportage</span></span>
                    <x-icon name="external" :size="16" class="ml-2 text-ink-400" />
                </a>
            </div>
        </div>
    </section>

    {{-- Communauté --}}
    <section id="communaute" class="mx-auto grid max-w-[1200px] scroll-mt-24 items-center gap-14 px-4 py-24 sm:px-6 lg:grid-cols-2 lg:py-32">
        <div class="flex flex-col gap-6">
            <span class="eyebrow">La communauté</span>
            <h2 class="text-[clamp(2rem,3.6vw,3.2rem)] font-extrabold leading-[1.05] text-navy-950">Personne ne part seul.</h2>
            <p class="text-[17px] leading-relaxed text-ink-600">Dès l’inscription, vous rejoignez le super groupe qui réunit tous les membres : futurs étudiants, étudiants en Chine, diplômés et recruteurs. Créez aussi vos propres groupes, par ville, par promo ou par projet.</p>
            <div class="grid grid-cols-2 gap-3">
                @foreach ([['bell', 'Annonces', 'Infos officielles de Travel Express'], ['help', 'Entraide', 'Questions et conseils entre membres'], ['briefcase', 'Emplois', 'Offres publiées par les recruteurs'], ['graduation', 'Bourses', 'Nouvelles bourses et dates limites']] as [$icon, $t, $d])
                    <div class="card flex flex-col gap-2 p-4">
                        <span class="flex size-9 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><x-icon :name="$icon" :size="18" /></span>
                        <span class="font-bold text-navy-950">{{ $t }}</span>
                        <span class="text-[13.5px] leading-snug text-ink-600">{{ $d }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="relative">
            <div class="absolute -inset-4 rounded-[36px] bg-linear-to-br from-brand-100 to-brand-50"></div>
            <div class="relative flex flex-col overflow-hidden rounded-[28px] bg-white shadow-lift ring-1 ring-ink-900/5">
                <div class="flex items-center gap-3 border-b border-ink-100 px-5 py-4">
                    <x-logo :size="42" />
                    <span class="flex flex-1 flex-col"><span class="font-bold text-navy-950">Communauté Back to Burkina</span><span class="flex items-center gap-1.5 text-[12.5px] text-ink-500"><span class="size-1.5 rounded-full bg-ok-500"></span> Super groupe · tous les membres</span></span>
                </div>
                <div class="flex gap-2 overflow-hidden border-b border-ink-100 px-5 py-3">
                    @foreach (['Annonces', 'Entraide', 'Emplois', 'Bourses'] as $i => $tab)
                        <span @class(['shrink-0 rounded-full px-3 py-1.5 text-xs font-semibold', 'bg-navy-900 text-white' => $i === 1, 'bg-ink-50 text-ink-600' => $i !== 1])>{{ $tab }}</span>
                    @endforeach
                </div>
                <div class="bg-dots flex flex-col gap-3 bg-canvas p-5">
                    <div class="flex max-w-[85%] items-end gap-2 self-start">
                        <x-avatar initials="TE" :size="30" tone="gold" />
                        <div class="rounded-[20px] rounded-bl-md bg-white px-3.5 py-2.5 shadow-card"><span class="flex items-center gap-1.5 text-[12.5px] font-bold text-brand-700">Travel Express <span class="badge-gold px-1.5 py-0 text-[10px]">Officiel</span></span><span class="text-sm">Nouvelle bourse ouverte, date limite le [date].</span></div>
                    </div>
                    <div class="flex max-w-[85%] items-end gap-2 self-start">
                        <x-avatar initials="IS" :size="30" />
                        <div class="rounded-[20px] rounded-bl-md bg-white px-3.5 py-2.5 shadow-card"><span class="text-[12.5px] font-bold text-brand-700">Issouf · étudiant en Chine</span><p class="text-sm">Je peux partager mon dossier de candidature comme exemple si ça aide.</p></div>
                    </div>
                    <div class="max-w-[75%] self-end rounded-[20px] rounded-br-md bg-linear-to-br from-brand-500 to-brand-600 px-3.5 py-2.5 text-sm text-white shadow-[0_6px_16px_-8px_rgb(0_113_227/0.6)]">Avec plaisir, merci Issouf !</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Talents --}}
    <section id="talents" class="scroll-mt-24 bg-canvas">
        <div class="mx-auto flex max-w-[1200px] flex-col gap-12 px-4 py-24 sm:px-6 lg:py-32">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <div class="flex max-w-2xl flex-col gap-4">
                    <span class="eyebrow">Le vivier de talents</span>
                    <h2 class="text-[clamp(2rem,3.6vw,3.2rem)] font-extrabold leading-[1.05] text-navy-950">Formés en Chine,<br>prêts pour le Burkina.</h2>
                </div>
                <a href="{{ route('register', ['profil' => 'recruiter']) }}" class="btn-secondary">Accéder au vivier <x-icon name="arrow" :size="18" /></a>
            </div>
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @forelse ($talents as $t)
                    <div class="card card-hover flex flex-col gap-3.5 p-6">
                        <div class="flex items-start justify-between">
                            <span class="relative"><x-avatar :user="$t->user" :size="56" /><span class="absolute -bottom-0.5 -right-0.5 flex size-5 items-center justify-center rounded-full bg-brand-600 text-white ring-2 ring-white"><x-icon name="check" :size="11" stroke-width="3.4" /></span></span>
                            <span class="badge-ok">Vérifié</span>
                        </div>
                        <span class="font-display text-[18px] font-bold text-navy-950">{{ $t->user->first_name }} {{ mb_substr($t->user->last_name, 0, 1) }}.</span>
                        <span class="text-sm leading-snug text-ink-600">{{ $t->headline }}</span>
                        @if ($t->hsk_level)<span class="mt-auto self-start rounded-lg bg-gold-50 px-2.5 py-1 text-xs font-bold text-gold-700 ring-1 ring-gold-200">Chinois {{ $t->hsk_level }}</span>@endif
                    </div>
                @empty
                    <x-empty icon="users" title="Les premiers profils arrivent bientôt" text="Les diplômés vérifiés par Travel Express apparaîtront ici." class="sm:col-span-2 lg:col-span-4" />
                @endforelse
            </div>
        </div>
    </section>

    {{-- Entreprises --}}
    <section id="entreprises" class="mx-auto grid max-w-[1200px] scroll-mt-24 gap-5 px-4 py-24 sm:px-6 lg:grid-cols-[1.4fr_1fr] lg:py-32">
        <div class="bg-hero relative flex flex-col gap-6 overflow-hidden rounded-[32px] p-[clamp(28px,4vw,56px)] text-white">
            <div class="bg-weave pointer-events-none absolute inset-y-0 right-0 w-1/2 opacity-50 [mask-image:linear-gradient(to_left,black,transparent)]"></div>
            <span class="eyebrow relative text-gold-300">Entreprises</span>
            <h2 class="relative text-[clamp(1.9rem,3.2vw,2.8rem)] font-extrabold leading-[1.05]">Recrutez des profils formés en Chine.</h2>
            <p class="relative max-w-xl text-base leading-relaxed text-white/70">Ingénieurs, techniciens, médecins, commerciaux : des diplômés vérifiés par Travel Express, qui parlent français et chinois. Idéal pour les entreprises burkinabè et les sociétés chinoises installées au pays.</p>
            <a href="{{ route('register', ['profil' => 'recruiter']) }}" class="btn-gold relative mt-2 self-start">Créer un compte recruteur <x-icon name="arrow" :size="18" /></a>
        </div>
        <div class="flex flex-col gap-5">
            @foreach ([['search', 'Recherche ciblée', 'Filtrez par domaine, niveau de chinois, ville et disponibilité.'], ['chat', 'Offres dans la communauté', 'Publiez vos offres dans le thème Emplois du super groupe.'], ['shield', 'Profils vérifiés', 'Chaque diplôme est contrôlé par Travel Express avant publication.']] as [$icon, $t, $d])
                <div class="card flex flex-1 items-start gap-4 p-6">
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"><x-icon :name="$icon" :size="22" /></span>
                    <span class="flex flex-col gap-1"><span class="font-display text-lg font-bold text-navy-950">{{ $t }}</span><span class="text-[15px] leading-relaxed text-ink-600">{{ $d }}</span></span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- FAQ --}}
    <section id="faq" class="scroll-mt-24 bg-canvas">
        <div class="mx-auto grid max-w-[1200px] gap-12 px-4 py-24 sm:px-6 lg:grid-cols-[0.8fr_1.2fr] lg:py-32" x-data="{ open: 0 }">
            <div class="flex flex-col gap-4 lg:sticky lg:top-28 lg:self-start">
                <span class="eyebrow">FAQ</span>
                <h2 class="text-[clamp(2rem,3.6vw,3rem)] font-extrabold leading-[1.05] text-navy-950">Questions fréquentes</h2>
                <p class="text-[16px] leading-relaxed text-ink-600">Une autre question ? Créez votre compte et posez-la directement à un conseiller.</p>
                <a href="{{ route('welcome') }}" class="btn-primary mt-2 self-start">Parler à un conseiller</a>
            </div>
            <div class="flex flex-col gap-3">
                @foreach ([
                    ['Faut-il parler chinois pour partir ?', 'Pas forcément. Certains programmes sont enseignés en anglais, d’autres commencent par une année de langue chinoise. Votre conseiller vous oriente selon votre profil.'],
                    ['Combien de temps prend la procédure ?', 'Cela dépend de l’université et de la période de l’année. Votre conseiller vous donne un calendrier précis dès l’ouverture du dossier.'],
                    ['Quels sont les frais d’accompagnement ?', '[À compléter par Travel Express : frais de dossier, frais d’accompagnement, modalités de paiement.]'],
                    ['Je suis diplômé, comment rejoindre le vivier de talents ?', 'Créez un compte « Diplômé de retour », complétez votre profil et ajoutez votre diplôme. L’équipe Travel Express le vérifie avant de le publier.'],
                ] as $i => [$q, $a])
                    <div class="card overflow-hidden transition" :class="open === {{ $i }} && 'ring-brand-200 shadow-lift'">
                        <button type="button" @click="open = open === {{ $i }} ? -1 : {{ $i }}" :aria-expanded="open === {{ $i }}" class="flex min-h-[68px] w-full items-center justify-between gap-4 px-6 text-left font-display text-[17px] font-bold text-navy-950">
                            {{ $q }}
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-full transition duration-300" :class="open === {{ $i }} ? 'rotate-45 bg-brand-600 text-white' : 'bg-ink-50 text-ink-700'"><x-icon name="plus" :size="18" stroke-width="2.2" /></span>
                        </button>
                        <div x-show="open === {{ $i }}" x-transition.opacity.duration.200ms x-cloak>
                            <p class="px-6 pb-6 text-[15.5px] leading-relaxed text-ink-600">{{ $a }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Appel final --}}
    <section class="mx-auto max-w-[1200px] px-4 py-24 sm:px-6">
        <div class="relative overflow-hidden rounded-[36px] bg-linear-to-br from-brand-500 to-brand-700 px-[clamp(24px,5vw,64px)] py-14 text-white shadow-lift">
            <div class="bg-weave pointer-events-none absolute inset-0 opacity-40"></div>
            <div class="pointer-events-none absolute -right-20 -top-20 size-72 rounded-full bg-white/10 blur-2xl"></div>
            <div class="relative flex flex-wrap items-center justify-between gap-8">
                <div class="flex max-w-xl flex-col gap-3">
                    <h2 class="text-[clamp(1.9rem,3.2vw,2.8rem)] font-extrabold leading-[1.05]">Votre dossier, toujours dans la poche.</h2>
                    <p class="text-[16px] leading-relaxed text-white/80">L’application est disponible sur Android et iPhone, et s’installe aussi depuis le navigateur.</p>
                </div>
                <div class="flex flex-wrap gap-3" x-data="{ prompt: null }" @beforeinstallprompt.window.prevent="prompt = $event">
                    <a href="#" class="btn-light"><x-icon name="phone" :size="18" /> Android</a>
                    <a href="#" class="btn-light"><x-icon name="phone" :size="18" /> iPhone</a>
                    <button type="button" x-show="prompt" x-cloak @click="prompt.prompt(); prompt = null" class="btn-outline-light">Installer depuis le navigateur</button>
                </div>
            </div>
        </div>
    </section>
</x-layouts.site>
