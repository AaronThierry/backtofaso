@php
$sections = ['destinations' => 'Destinations', 'formations' => 'Formations', 'bourses' => 'Bourses', 'documents' => 'Documents', 'etapes' => 'Étapes', 'calendrier' => 'Calendrier', 'vie' => 'Vie sur place'];
$stepDetails = [
    1 => 'Vous créez votre compte et complétez votre profil : diplômes, pays et domaine visés, niveau de langue. Votre conseiller vous propose ensuite les programmes adaptés.',
    2 => 'Vous déposez vos pièces en ligne. Votre conseiller les vérifie une à une et vous indique ce qui manque ou doit être refait.',
    3 => 'Votre dossier complet est transmis à l’université ou à l’école choisie, ou à la plateforme de candidature du pays. Vous suivez la réponse dans votre espace.',
    4 => 'Après l’admission, vous recevez la lettre d’admission et les documents exigés pour le visa, qui varient selon le pays : formulaire JW202 pour la Chine, certificat d’acceptation (CAQ) pour le Québec, etc.',
    5 => 'Avec ces documents, vous demandez votre visa étudiant auprès de l’ambassade ou du consulat. Nous vous aidons à préparer le dossier et le rendez-vous.',
    6 => 'Billet, valise, arrivée : vous partez préparé, et la communauté des étudiants burkinabè vous accueille sur place.',
];
@endphp
<x-layouts.site title="Étudier à l’étranger" description="Destinations, formations, bourses, documents à préparer, étapes du dossier et vie sur place : tout pour partir étudier à l’étranger avec Travel Express.">
    <x-site.page-hero eyebrow="Le guide" title="Étudier à l’étranger" lead="Chine, France, Canada, Maroc, Turquie et bien d’autres : choisissez votre destination, et Travel Express vous accompagne de la candidature au départ, puis jusqu’au retour.">
        <a href="{{ route('welcome') }}" class="btn-gold shine min-h-14 px-7 text-base">Commencer ma candidature <x-icon name="arrow" :size="18" /></a>
        <a href="#destinations" class="btn-outline-light min-h-14 px-7 text-base">Voir les destinations</a>

        <x-slot:aside>
            <div class="absolute -inset-6 rounded-[40px] bg-brand-500/20 blur-2xl"></div>
            <div data-parallax="0.1" class="relative flex flex-col gap-3 rounded-[28px] bg-white p-5 text-ink-900 shadow-[0_40px_80px_-30px_rgb(0_0_0/0.6)] sm:p-6">
                <span class="text-xs font-bold uppercase tracking-[0.14em] text-ink-500">Votre parcours</span>
                @foreach (\App\Models\Dossier::STEPS as $n => [$t, $d])
                    <div class="flex items-center gap-3">
                        <span @class(['flex size-8 shrink-0 items-center justify-center rounded-full text-[13px] font-bold', 'bg-brand-600 text-white' => $n < 3, 'bg-linear-to-br from-gold-400 to-gold-500 text-navy-950' => $n === 3, 'bg-ink-50 text-ink-500 ring-1 ring-ink-200' => $n > 3])>
                            @if ($n < 3)<x-icon name="check" :size="14" stroke-width="3" />@else{{ $n }}@endif
                        </span>
                        <span class="flex min-w-0 flex-1 flex-col leading-tight">
                            <span @class(['truncate text-sm', 'font-bold text-navy-950' => $n <= 3, 'font-semibold text-ink-500' => $n > 3])>{{ $t }}</span>
                            <span class="truncate text-xs text-ink-500">{{ $d }}</span>
                        </span>
                        @if ($n === 3)<span class="badge-warn">En cours</span>@endif
                    </div>
                @endforeach
            </div>
        </x-slot:aside>
    </x-site.page-hero>

    {{-- Rubriques --}}
    <nav class="sticky top-0 z-30 border-b border-ink-100 bg-white/90 backdrop-blur-xl" aria-label="Rubriques de la page">
        <div class="mx-auto flex max-w-[1200px] gap-1.5 overflow-x-auto px-4 py-3 sm:px-6" style="scrollbar-width: none">
            @foreach ($sections as $id => $label)
                <a href="#{{ $id }}" class="shrink-0 rounded-full px-3.5 py-2 text-[13.5px] font-semibold text-ink-700 transition hover:bg-ink-50 hover:text-navy-950">{{ $label }}</a>
            @endforeach
        </div>
    </nav>

    {{-- Destinations --}}
    <section id="destinations" class="mx-auto flex max-w-[1200px] scroll-mt-20 flex-col gap-10 px-4 py-16 sm:px-6 sm:py-24">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <div data-reveal class="flex max-w-2xl flex-col gap-4">
                <span class="eyebrow">Destinations</span>
                <h2 class="text-[clamp(2rem,5.5vw,3rem)] font-extrabold leading-[1.05] text-navy-950">Le monde comme campus.</h2>
                <p class="text-[16.5px] leading-relaxed text-ink-600">Voici les destinations les plus demandées. Votre projet vise un autre pays ? Nous l’étudions avec vous.</p>
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3" data-reveal-stagger="70">
            @foreach ($destinations as $slug => $d)
                <article data-reveal class="card card-hover group flex flex-col gap-4 p-6">
                    <div class="flex items-center gap-3.5">
                        <span class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-navy-950 font-display text-[15px] font-extrabold tracking-wide text-gold-300 transition group-hover:bg-linear-to-br group-hover:from-brand-500 group-hover:to-brand-600 group-hover:text-white">{{ $d['code'] }}</span>
                        <span class="flex flex-col">
                            <span class="font-display text-[20px] font-bold text-navy-950">{{ $d['name'] }}</span>
                            <span class="flex items-center gap-1.5 text-[13px] text-ink-500"><x-icon name="language" :size="14" /> {{ $d['languages'] }}</span>
                        </span>
                    </div>
                    <p class="text-[14.5px] leading-relaxed text-ink-600">{{ $d['about'] }}</p>
                    <p class="mt-auto flex items-start gap-2 border-t border-ink-100 pt-4 text-[13.5px] font-semibold text-ink-800">
                        <x-icon name="graduation" :size="16" class="mt-0.5 shrink-0 text-gold-500" /> {{ $d['scholarships'] }}
                    </p>
                </article>
            @endforeach
            <a data-reveal href="{{ route('contact', ['sujet' => 'candidature']) }}" class="group flex flex-col justify-between gap-4 rounded-2xl border-2 border-dashed border-ink-200 p-6 transition hover:border-brand-300 hover:bg-brand-50/40">
                <span class="flex size-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"><x-icon name="globe" :size="22" /></span>
                <span class="flex flex-col gap-1.5">
                    <span class="font-display text-[20px] font-bold text-navy-950">Un autre pays ?</span>
                    <span class="text-[14.5px] leading-relaxed text-ink-600">Ghana, Côte d’Ivoire, Inde, Royaume-Uni… Parlez-nous de votre projet, nous regardons ensemble ce qui est possible.</span>
                </span>
                <span class="inline-flex items-center gap-1.5 text-sm font-bold text-brand-600 transition-all group-hover:gap-2.5">Nous écrire <x-icon name="arrow" :size="16" /></span>
            </a>
        </div>
    </section>

    {{-- Formations --}}
    <section id="formations" class="scroll-mt-20 bg-canvas">
        <div class="mx-auto flex max-w-[1200px] flex-col gap-10 px-4 py-16 sm:px-6 sm:py-24">
            <div data-reveal class="flex max-w-2xl flex-col gap-4">
                <span class="eyebrow">Formations</span>
                <h2 class="text-[clamp(2rem,5.5vw,3rem)] font-extrabold leading-[1.05] text-navy-950">Du cours de langue au doctorat.</h2>
                <p class="text-[16.5px] leading-relaxed text-ink-600">Durées indicatives : elles varient selon le pays, l’établissement et le programme.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" data-reveal-stagger="100">
                @foreach ([
                    ['Année de langue', '1 an', 'Apprendre la langue du pays avant d’entrer en licence ou en master : chinois, turc, russe, allemand…', 'Après le Bac'],
                    ['Licence', '3 à 4 ans', 'Selon le pays. Les études de médecine durent plus longtemps.', 'Avec le Bac'],
                    ['Master', '1 à 3 ans', 'Spécialisation après une licence, souvent avec un stage ou un mémoire.', 'Avec une licence'],
                    ['Doctorat', '3 ans et plus', 'Recherche encadrée par un directeur de thèse dans un laboratoire.', 'Avec un master'],
                ] as [$t, $dur, $d, $req])
                    <div data-reveal class="card card-hover relative flex flex-col gap-3 overflow-hidden p-6">
                        <span class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-brand-500 to-gold-400"></span>
                        <span class="flex items-baseline justify-between gap-2">
                            <span class="font-display text-xl font-bold text-navy-950">{{ $t }}</span>
                            <span class="shrink-0 rounded-full bg-brand-50 px-2.5 py-1 text-xs font-bold text-brand-700">{{ $dur }}</span>
                        </span>
                        <span class="text-[14.5px] leading-relaxed text-ink-600">{{ $d }}</span>
                        <span class="mt-auto flex items-center gap-1.5 border-t border-ink-100 pt-3 text-[13px] font-semibold text-ink-700"><x-icon name="check" :size="14" stroke-width="2.6" class="text-ok-700" /> {{ $req }}</span>
                    </div>
                @endforeach
            </div>
            <div data-reveal class="flex flex-col gap-3">
                <span class="text-sm font-bold text-navy-950">Domaines les plus demandés</span>
                <div class="flex flex-wrap gap-2">
                    @foreach ($fields as $f)
                        @continue($f === 'Autre')
                        <span class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-ink-800 shadow-xs ring-1 ring-ink-200">{{ $f }}</span>
                    @endforeach
                    <span class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-ink-500 shadow-xs ring-1 ring-ink-200">et bien d’autres</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Bourses --}}
    <section id="bourses" class="mx-auto grid max-w-[1200px] scroll-mt-20 gap-10 px-4 py-16 sm:px-6 sm:py-24 lg:grid-cols-[0.9fr_1.1fr] lg:gap-14">
        <div data-reveal class="flex flex-col gap-4 lg:sticky lg:top-24 lg:self-start">
            <span class="eyebrow">Bourses</span>
            <h2 class="text-[clamp(2rem,5.5vw,3rem)] font-extrabold leading-[1.05] text-navy-950">Financer vos études.</h2>
            <p class="text-[16.5px] leading-relaxed text-ink-600">De nombreux pays et établissements financent les études d’étudiants africains. Selon la bourse, elle peut couvrir les frais de scolarité, le logement, une allocation mensuelle ou l’assurance.</p>
            <div class="flex items-start gap-3 rounded-2xl bg-gold-50 p-4 text-[14px] leading-relaxed text-gold-700 ring-1 ring-gold-200">
                <x-icon name="help" :size="20" class="mt-0.5 shrink-0" />
                <span>Les conditions, les montants et les dates changent chaque année. Votre conseiller vous indique les bourses réellement ouvertes à votre profil.</span>
            </div>
        </div>
        <div class="flex flex-col gap-4" data-reveal-stagger="100">
            @foreach ([
                ['Bourses des gouvernements étrangers', 'Plusieurs pays financent des étudiants étrangers : Chine, Turquie, Russie, France, entre autres. Ce sont souvent les plus complètes.'],
                ['Bourses de coopération', 'Proposées par des agences de coopération ou des organismes comme l’AMCI au Maroc, le DAAD en Allemagne ou le programme Fulbright aux États-Unis.'],
                ['Bourses des universités', 'Beaucoup d’établissements accordent des réductions ou des exemptions de frais, souvent selon les résultats.'],
                ['Bourses de langue', 'Pour apprendre une langue pendant quelques mois ou une année avant ou pendant le cursus.'],
            ] as [$t, $d])
                <div data-reveal="right" class="card card-hover flex items-start gap-4 p-6">
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-gold-300 to-gold-500 text-navy-950"><x-icon name="graduation" :size="22" /></span>
                    <span class="flex flex-col gap-1"><span class="font-display text-lg font-bold text-navy-950">{{ $t }}</span><span class="text-[14.5px] leading-relaxed text-ink-600">{{ $d }}</span></span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Documents --}}
    <section id="documents" class="scroll-mt-20 bg-canvas">
        <div class="mx-auto flex max-w-[1200px] flex-col gap-10 px-4 py-16 sm:px-6 sm:py-24">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <div data-reveal class="flex max-w-2xl flex-col gap-4">
                    <span class="eyebrow">Documents</span>
                    <h2 class="text-[clamp(2rem,5.5vw,3rem)] font-extrabold leading-[1.05] text-navy-950">Les pièces à préparer.</h2>
                    <p class="text-[16.5px] leading-relaxed text-ink-600">La liste exacte dépend du pays, de l’établissement et du programme. Commencez par celles-ci : certaines demandent du temps.</p>
                </div>
                <a data-reveal href="{{ route('welcome') }}" class="btn-primary">Déposer mes documents en ligne</a>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3" data-reveal-stagger="60">
                @foreach ([
                    ['Passeport', 'Valide pendant toute la durée du séjour de préférence, avec des pages libres.', true],
                    ['Diplômes', 'Bac, licence ou master selon le niveau visé, avec leur traduction si nécessaire.', true],
                    ['Relevés de notes', 'Ceux des dernières années d’études.', true],
                    ['Lettre de motivation', 'Votre projet d’études et pourquoi ce programme, dans ce pays.', true],
                    ['Photos d’identité', 'Récentes, au format demandé par l’ambassade.', true],
                    ['Certificat de langue', 'TCF ou DELF pour le français, IELTS ou TOEFL pour l’anglais, HSK pour le chinois… selon le programme.', false],
                    ['Lettres de recommandation', 'Demandées surtout pour un master ou un doctorat.', false],
                    ['Certificat médical', 'Exigé par certains pays, parfois sur un formulaire officiel.', false],
                    ['Justificatif de ressources', 'Preuve que vous pouvez financer votre séjour, demandée pour de nombreux visas.', false],
                ] as [$t, $d, $always])
                    <div data-reveal class="flex items-start gap-3.5 rounded-2xl bg-white p-4 shadow-xs ring-1 ring-ink-900/[0.05]">
                        <span @class(['flex size-10 shrink-0 items-center justify-center rounded-xl', 'bg-brand-50 text-brand-600' => $always, 'bg-ink-50 text-ink-500' => ! $always])><x-icon name="file" :size="19" /></span>
                        <span class="flex flex-col gap-0.5">
                            <span class="flex flex-wrap items-center gap-2 text-[15px] font-bold text-navy-950">{{ $t }} @unless ($always)<span class="rounded-full bg-ink-100 px-2 py-0.5 text-[11px] font-semibold text-ink-600">Selon le pays</span>@endunless</span>
                            <span class="text-[13.5px] leading-snug text-ink-600">{{ $d }}</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Étapes --}}
    <section id="etapes" class="mx-auto flex max-w-[1200px] scroll-mt-20 flex-col gap-10 px-4 py-16 sm:px-6 sm:py-24">
        <div data-reveal class="flex max-w-2xl flex-col gap-4">
            <span class="eyebrow">Étapes</span>
            <h2 class="text-[clamp(2rem,5.5vw,3rem)] font-extrabold leading-[1.05] text-navy-950">Votre dossier, de A à Z.</h2>
            <p class="text-[16.5px] leading-relaxed text-ink-600">Six étapes, quel que soit le pays, toutes visibles dans votre espace en ligne. Vous savez toujours où vous en êtes.</p>
        </div>
        <ol class="relative flex flex-col gap-4">
            <span data-draw="y" class="absolute bottom-6 left-[27px] top-6 w-0.5 bg-linear-to-b from-brand-500 via-gold-400 to-brand-200 sm:left-[31px]"></span>
            @foreach (\App\Models\Dossier::STEPS as $n => [$t, $d])
                <li data-reveal class="relative flex gap-5 sm:gap-6">
                    <span class="relative z-10 flex size-14 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-brand-500 to-brand-600 font-display text-xl font-extrabold text-white shadow-glow ring-4 ring-white sm:size-16">{{ $n }}</span>
                    <div class="card flex flex-1 flex-col gap-1.5 p-5 sm:p-6">
                        <span class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
                            <span class="font-display text-[19px] font-bold text-navy-950">{{ $t }}</span>
                            <span class="text-[13px] font-semibold text-brand-600">{{ $d }}</span>
                        </span>
                        <p class="text-[15px] leading-relaxed text-ink-600">{{ $stepDetails[$n] ?? '' }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </section>

    {{-- Calendrier --}}
    <section id="calendrier" class="scroll-mt-20 px-4 sm:px-6">
        <div data-reveal class="bg-hero relative mx-auto max-w-[1200px] overflow-hidden rounded-[28px] p-6 text-white sm:rounded-[32px] sm:p-10 lg:p-14">
            <div class="bg-weave pointer-events-none absolute inset-y-0 right-0 w-1/3 opacity-50 [mask-image:linear-gradient(to_left,black,transparent)]"></div>
            <div class="relative flex flex-col gap-10">
                <div class="flex flex-col gap-3">
                    <span class="eyebrow text-gold-300">Calendrier type</span>
                    <h2 class="text-[clamp(1.8rem,4.5vw,2.6rem)] font-bold">Pour une rentrée en septembre</h2>
                    <p class="max-w-2xl text-[15.5px] text-white/65">C’est la rentrée la plus courante. Certains pays et établissements proposent aussi une rentrée en janvier ou en mars : votre conseiller vous donne les dates exactes.</p>
                </div>
                <ol class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5" data-reveal-stagger="100">
                    @foreach ([
                        ['Sept. – oct.', 'Préparer son projet', 'Choisir le pays, le niveau, le domaine et la langue d’enseignement.'],
                        ['Nov. – mars', 'Candidater', 'Réunir les documents, passer les tests de langue, déposer les dossiers et les demandes de bourse.'],
                        ['Mars – juin', 'Recevoir l’admission', 'Réponses des établissements et documents pour le visa.'],
                        ['Juin – août', 'Visa et billet', 'Rendez-vous à l’ambassade ou au consulat, réservation du vol.'],
                        ['Septembre', 'La rentrée', 'Arrivée, installation et démarches de séjour.'],
                    ] as [$when, $t, $d])
                        <li data-reveal class="glass flex flex-col gap-2 rounded-2xl p-5">
                            <span class="text-xs font-bold uppercase tracking-[0.12em] text-gold-300">{{ $when }}</span>
                            <span class="font-display text-[17px] font-bold">{{ $t }}</span>
                            <span class="text-[13.5px] leading-snug text-white/65">{{ $d }}</span>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </section>

    {{-- Vie sur place --}}
    <section id="vie" class="mx-auto flex max-w-[1200px] scroll-mt-20 flex-col gap-10 px-4 py-16 sm:px-6 sm:py-24">
        <div data-reveal class="flex max-w-2xl flex-col gap-4">
            <span class="eyebrow">Vie sur place</span>
            <h2 class="text-[clamp(2rem,5.5vw,3rem)] font-extrabold leading-[1.05] text-navy-950">S’installer sereinement.</h2>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3" data-reveal-stagger="90">
            @foreach ([
                ['home', 'Logement', 'Résidence universitaire, colocation ou logement privé : nous vous aidons à trouver une solution avant le départ.'],
                ['shield', 'Assurance santé', 'Une assurance maladie est obligatoire ou fortement recommandée dans la plupart des pays.'],
                ['file', 'Titre de séjour', 'Selon le pays, le visa doit être validé ou transformé en titre de séjour peu après l’arrivée.'],
                ['users', 'Communauté', 'Les étudiants burkinabè déjà sur place vous accueillent et partagent leurs conseils dans la communauté.'],
                ['language', 'Langue', 'Même dans un programme en anglais, parler la langue du pays facilite la vie quotidienne et les stages.'],
                ['briefcase', 'Préparer le retour', 'Dès la dernière année, votre profil peut rejoindre le vivier de talents pour trouver un emploi au pays.'],
            ] as [$icon, $t, $d])
                <div data-reveal class="card card-hover flex items-start gap-4 p-6">
                    <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><x-icon :name="$icon" :size="20" /></span>
                    <span class="flex flex-col gap-1"><span class="font-display text-[17px] font-bold text-navy-950">{{ $t }}</span><span class="text-[14.5px] leading-relaxed text-ink-600">{{ $d }}</span></span>
                </div>
            @endforeach
        </div>
        <a data-reveal href="{{ route('faq') }}" class="inline-flex items-center gap-1.5 self-start text-[15px] font-bold text-brand-600 transition-all hover:gap-2.5">Voir toutes les questions fréquentes <x-icon name="arrow" :size="17" /></a>
    </section>

    <x-site.cta title="Votre projet commence ici." text="Créez votre compte : un conseiller Travel Express étudie votre profil et vous propose les destinations et les programmes adaptés." />
</x-layouts.site>
