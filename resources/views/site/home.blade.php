<x-layouts.site>
    {{-- Héros --}}
    <section class="bg-brand-600 text-white">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-12 px-4 py-20 sm:px-6">
            <div class="flex flex-[1_1_480px] flex-col gap-6">
                <h1 class="text-[clamp(2.5rem,5.2vw,4.4rem)] font-bold leading-none">Étudier en Chine.<br>Revenir bâtir le Faso.</h1>
                <p class="max-w-xl text-lg leading-relaxed">Candidature, visa, vie sur place et retour à l’emploi : Travel Express accompagne les étudiants burkinabè du départ jusqu’au retour, et met leurs compétences au service du pays.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('welcome') }}" class="btn min-h-14 bg-white px-6 text-base text-brand-600">Commencer ma candidature <x-icon name="arrow" :size="18" /></a>
                    <a href="{{ route('register', ['profil' => 'recruiter']) }}" class="btn min-h-14 border border-white/60 px-6 text-base text-white">Je recrute des talents</a>
                </div>
            </div>
            <div class="flex flex-[0_1_340px] flex-col gap-3.5">
                <div class="flex flex-col gap-3 rounded-2xl bg-white p-5 text-ink-900">
                    <span class="flex items-center justify-between font-bold">Mon dossier <span class="badge-warn">Admission en cours</span></span>
                    <span class="h-2 overflow-hidden rounded bg-ink-200"><span class="block h-2 w-2/5 bg-brand-600"></span></span>
                    <span class="text-[13px] text-ink-600">Étape 3 sur 6 · Licence informatique</span>
                </div>
                <div class="flex flex-col gap-2 rounded-2xl bg-white p-5 text-ink-900 sm:ml-10">
                    <span class="flex items-center gap-2 text-[13px] font-bold">Communauté Back to Burkina <span class="badge-gold">Officiel</span></span>
                    <span class="text-sm text-ink-700">Séance d’information à l’agence de Bobo-Dioulasso, ouverte aux parents.</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Parcours --}}
    <section id="parcours" class="mx-auto flex max-w-6xl flex-col gap-10 px-4 py-24 sm:px-6">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <h2 class="max-w-xl text-[clamp(1.9rem,3.2vw,2.8rem)] font-bold leading-tight">Du départ au retour, on ne vous lâche pas.</h2>
            <p class="max-w-md text-base leading-relaxed text-ink-600">Trois temps, une seule équipe. Chaque étape de votre dossier est visible dans votre espace, sur le web comme sur l’application.</p>
        </div>
        <div class="grid gap-5 md:grid-cols-3">
            @foreach ([
                ['Partir', 'Nous vous aidons à choisir la bonne université et à constituer un dossier solide.', ['Choix du programme et des bourses', 'Dossier d’admission et JW202', 'Visa étudiant et billet']],
                ['Étudier', 'Sur place, vous gardez le lien avec l’agence et avec les autres étudiants burkinabè.', ['Accueil à l’arrivée', 'Groupes par ville et par promo', 'Fiches pratiques : HSK, banque, santé']],
                ['Revenir', 'Votre diplôme en poche, votre profil est mis en avant auprès des recruteurs.', ['Profil dans le vivier de talents', 'Offres d’emploi ciblées', 'Appui aux projets de retour']],
            ] as [$t, $d, $items])
                <div class="card flex flex-col gap-3.5 p-7">
                    <div class="flex items-center gap-3">
                        <span class="flex size-11 items-center justify-center rounded-full bg-brand-100 text-lg font-bold text-brand-700">{{ $loop->iteration }}</span>
                        <h3 class="text-2xl font-bold">{{ $t }}</h3>
                    </div>
                    <p class="text-[15px] leading-relaxed text-ink-600">{{ $d }}</p>
                    <ul class="flex flex-col gap-2 border-t border-brand-100 pt-3">
                        @foreach ($items as $it)
                            <li class="flex items-center gap-2.5 text-sm font-semibold"><x-icon name="check" :size="16" stroke-width="2.6" class="text-brand-600" />{{ $it }}</li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        <div class="flex flex-col gap-7 rounded-2xl border border-ink-200 bg-white p-[clamp(20px,4vw,48px)]">
            <div class="flex flex-wrap items-baseline justify-between gap-4">
                <h2 class="text-[26px] font-bold">Votre dossier, étape par étape</h2>
                <span class="text-sm text-ink-600">Suivi en ligne à chaque étape</span>
            </div>
            <ol class="grid gap-[18px] sm:grid-cols-3 lg:grid-cols-6">
                @foreach (\App\Models\Dossier::STEPS as $n => [$t, $d])
                    <li class="flex flex-col gap-2.5">
                        <span class="flex items-center gap-2"><span class="flex size-[34px] shrink-0 items-center justify-center rounded-full bg-brand-600 text-sm font-bold text-white">{{ $n }}</span><span class="h-0.5 flex-1 bg-ink-200"></span></span>
                        <span class="text-[15px] font-bold">{{ $t }}</span>
                        <span class="text-[13.5px] leading-normal text-ink-600">{{ $d }}</span>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Le projet --}}
    <section class="bg-brand-700 text-white">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-14 px-4 py-24 sm:px-6">
            <div class="flex aspect-[4/5] max-w-md flex-[1_1_340px] items-center justify-center rounded-2xl border border-dashed border-white/40 bg-white/10 text-[15px] font-semibold">[Photo du fondateur]</div>
            <div class="flex flex-[1_1_460px] flex-col gap-5">
                <h2 class="text-[clamp(1.9rem,3.2vw,2.8rem)] font-bold leading-tight">Les compétences acquises ailleurs, réinvesties ici.</h2>
                <p class="text-[17px] leading-relaxed">Yikonpin Armel Razak Bakoua, directeur général de Travel Express, a suivi des études d’ingénieur en informatique en Chine avant de choisir de rentrer au Burkina. Avec Back to Burkina, il veut que chaque étudiant parti se former à l’étranger puisse, à son retour, mettre son savoir au service du pays.</p>
                <a href="https://burkina24.com/?p=454535" target="_blank" rel="noopener" class="flex items-center gap-3 self-start rounded-2xl bg-white/10 py-3 pl-3 pr-4">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-white text-brand-700"><x-icon name="play" :size="18" /></span>
                    <span class="flex flex-col"><span class="text-xs">Vu dans la presse · Burkina 24</span><span class="font-bold">Lire le reportage</span></span>
                </a>
            </div>
        </div>
    </section>

    {{-- Communauté --}}
    <section id="communaute" class="mx-auto flex max-w-6xl flex-wrap items-center gap-14 px-4 py-24 sm:px-6">
        <div class="flex flex-[1_1_440px] flex-col gap-5">
            <h2 class="text-[clamp(1.9rem,3.2vw,2.8rem)] font-bold leading-tight">Personne ne part seul.</h2>
            <p class="text-[17px] leading-relaxed text-ink-600">Dès l’inscription, vous rejoignez le super groupe qui réunit tous les membres : futurs étudiants, étudiants en Chine, diplômés et recruteurs. Créez aussi vos propres groupes, par ville, par promo ou par projet.</p>
            <div class="grid grid-cols-2 gap-3">
                @foreach ([['Annonces', 'Infos officielles de Travel Express'], ['Entraide', 'Questions et conseils entre membres'], ['Emplois', 'Offres publiées par les recruteurs'], ['Bourses', 'Nouvelles bourses et dates limites']] as [$t, $d])
                    <div class="card flex flex-col gap-1 p-4"><span class="font-bold">{{ $t }}</span><span class="text-[13.5px] text-ink-600">{{ $d }}</span></div>
                @endforeach
            </div>
        </div>
        <div class="flex flex-[1_1_420px] flex-col gap-3 rounded-2xl bg-ink-50 p-6">
            <div class="flex items-center gap-3 rounded-xl bg-white px-3.5 py-3"><x-logo :size="40" /><span class="flex flex-col"><span class="font-bold">Communauté Back to Burkina</span><span class="text-[12.5px] text-ink-600">Super groupe · tous les membres</span></span></div>
            <div class="max-w-sm self-start rounded-2xl rounded-bl-sm bg-white px-3.5 py-3"><span class="flex items-center gap-1.5 text-[12.5px] font-bold text-brand-700">Travel Express <span class="badge-gold px-1.5 py-0.5 text-[10.5px]">Officiel</span></span><span class="text-sm">Nouvelle bourse ouverte, date limite le [date].</span></div>
            <div class="max-w-sm self-start rounded-2xl rounded-bl-sm bg-white px-3.5 py-3"><span class="text-[12.5px] font-bold text-brand-700">Issouf · étudiant en Chine</span><p class="text-sm">Je peux partager mon dossier de candidature comme exemple si ça aide.</p></div>
            <div class="max-w-xs self-end rounded-2xl rounded-br-sm bg-brand-600 px-3.5 py-3 text-sm text-white">Avec plaisir, merci Issouf !</div>
        </div>
    </section>

    {{-- Talents --}}
    <section id="talents" class="border-y border-ink-200 bg-white">
        <div class="mx-auto flex max-w-6xl flex-col gap-7 px-4 py-24 sm:px-6">
            <h2 class="text-[clamp(1.9rem,3.2vw,2.8rem)] font-bold leading-tight">Formés en Chine, prêts pour le Burkina.</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @forelse ($talents as $t)
                    <div class="card flex flex-col gap-2.5 p-5">
                        <div class="flex items-start justify-between"><x-avatar :user="$t->user" :size="52" /><span class="badge-ok">Vérifié</span></div>
                        <span class="text-[17px] font-bold">{{ $t->user->first_name }} {{ mb_substr($t->user->last_name, 0, 1) }}.</span>
                        <span class="text-sm text-ink-600">{{ $t->headline }}</span>
                        @if ($t->hsk_level)<span class="self-start rounded-lg border border-ink-200 bg-white px-2 py-1 text-xs font-semibold">Chinois {{ $t->hsk_level }}</span>@endif
                    </div>
                @empty
                    <p class="text-ink-600">Les premiers profils arrivent bientôt.</p>
                @endforelse
            </div>
            <a href="{{ route('register', ['profil' => 'recruiter']) }}" class="btn-secondary self-center">Accéder au vivier de talents</a>
        </div>
    </section>

    {{-- Entreprises --}}
    <section id="entreprises" class="mx-auto flex max-w-6xl flex-wrap gap-5 px-4 py-24 sm:px-6">
        <div class="flex flex-[1_1_520px] flex-col gap-[18px] rounded-2xl bg-brand-600 p-[clamp(28px,4vw,48px)] text-white">
            <h2 class="text-[clamp(1.8rem,3vw,2.5rem)] font-bold leading-tight">Recrutez des profils formés en Chine.</h2>
            <p class="max-w-xl text-base leading-relaxed">Ingénieurs, techniciens, médecins, commerciaux : des diplômés vérifiés par Travel Express, qui parlent français et chinois. Idéal pour les entreprises burkinabè et les sociétés chinoises installées au pays.</p>
            <a href="{{ route('register', ['profil' => 'recruiter']) }}" class="btn self-start bg-white text-brand-600">Créer un compte recruteur</a>
        </div>
        <div class="flex flex-[1_1_320px] flex-col gap-5">
            <div class="card flex flex-1 flex-col gap-2 p-7"><span class="text-lg font-bold">Recherche ciblée</span><span class="text-[15px] leading-relaxed text-ink-600">Filtrez par domaine, niveau de chinois, ville et disponibilité.</span></div>
            <div class="card flex flex-1 flex-col gap-2 p-7"><span class="text-lg font-bold">Offres dans la communauté</span><span class="text-[15px] leading-relaxed text-ink-600">Publiez vos offres dans le thème Emplois du super groupe.</span></div>
        </div>
    </section>

    {{-- FAQ --}}
    <section id="faq" class="border-t border-ink-200 bg-white">
        <div class="mx-auto flex max-w-3xl flex-col gap-7 px-4 py-24 sm:px-6" x-data="{ open: 0 }">
            <h2 class="text-center text-[clamp(1.9rem,3.2vw,2.6rem)] font-bold">Questions fréquentes</h2>
            @foreach ([
                ['Faut-il parler chinois pour partir ?', 'Pas forcément. Certains programmes sont enseignés en anglais, d’autres commencent par une année de langue chinoise. Votre conseiller vous oriente selon votre profil.'],
                ['Combien de temps prend la procédure ?', 'Cela dépend de l’université et de la période de l’année. Votre conseiller vous donne un calendrier précis dès l’ouverture du dossier.'],
                ['Quels sont les frais d’accompagnement ?', '[À compléter par Travel Express : frais de dossier, frais d’accompagnement, modalités de paiement.]'],
                ['Je suis diplômé, comment rejoindre le vivier de talents ?', 'Créez un compte « Diplômé de retour », complétez votre profil et ajoutez votre diplôme. L’équipe Travel Express le vérifie avant de le publier.'],
            ] as $i => [$q, $a])
                <div class="card bg-ink-50">
                    <button type="button" @click="open = open === {{ $i }} ? -1 : {{ $i }}" :aria-expanded="open === {{ $i }}" class="flex min-h-16 w-full items-center justify-between gap-4 px-5 text-left text-base font-bold">
                        {{ $q }}
                        <span class="flex size-8 shrink-0 items-center justify-center rounded-full text-xl transition" :class="open === {{ $i }} ? 'rotate-45 bg-brand-600 text-white' : 'border border-ink-300 bg-white'">+</span>
                    </button>
                    <p x-show="open === {{ $i }}" x-cloak class="px-5 pb-5 text-[15px] leading-relaxed text-ink-600">{{ $a }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- App --}}
    <section class="bg-brand-600 text-white">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-7 px-4 py-[72px] sm:px-6">
            <div class="flex flex-[1_1_420px] flex-col gap-2.5">
                <h2 class="text-[clamp(1.8rem,3vw,2.4rem)] font-bold">Votre dossier, toujours dans la poche.</h2>
                <p class="text-base">L’application est disponible sur Android et iPhone, et s’installe aussi depuis le navigateur.</p>
            </div>
            <div class="flex flex-wrap gap-3" x-data="{ prompt: null }" @beforeinstallprompt.window.prevent="prompt = $event">
                <a href="#" class="btn bg-white text-brand-600">Android</a>
                <a href="#" class="btn bg-white text-brand-600">iPhone</a>
                <button type="button" x-show="prompt" x-cloak @click="prompt.prompt(); prompt = null" class="btn border border-white/60 text-white">Installer depuis le navigateur</button>
            </div>
        </div>
    </section>
</x-layouts.site>
