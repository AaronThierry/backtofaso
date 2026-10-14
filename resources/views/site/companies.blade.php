<x-layouts.site title="Recruter des talents formés à l’étranger" description="Ingénieurs, médecins, commerciaux, techniciens : recrutez des diplômés formés à l’étranger, souvent multilingues et vérifiés par Travel Express.">
    <x-site.page-hero eyebrow="Entreprises" crumb="Entreprises" title="Recrutez des talents formés à l’étranger." lead="Des diplômés burkinabè revenus de Chine, de France, du Canada, du Maroc et d’ailleurs, dont le profil est vérifié par Travel Express. Pour les entreprises burkinabè comme pour les sociétés étrangères installées au pays.">
        <a href="{{ route('register', ['profil' => 'recruiter']) }}" class="btn-gold shine min-h-14 px-7 text-base">Créer un compte recruteur <x-icon name="arrow" :size="18" /></a>
        <a href="{{ route('contact', ['sujet' => 'recrutement']) }}" class="btn-outline-light min-h-14 px-7 text-base">Parler à l’équipe</a>

        <x-slot:aside>
            <div class="absolute -inset-6 rounded-[40px] bg-brand-500/20 blur-2xl"></div>
            <div class="relative flex flex-col gap-3">
                @forelse ($talents->take(3) as $i => $t)
                    <div data-parallax="{{ 0.06 + $i * 0.06 }}" @class(['flex items-center gap-3.5 rounded-2xl bg-white p-4 text-ink-900 shadow-lift', 'sm:ml-10' => $i === 1, 'sm:ml-4' => $i === 2])>
                        <span class="relative"><x-avatar :user="$t->user" :size="48" /><span class="absolute -bottom-0.5 -right-0.5 flex size-5 items-center justify-center rounded-full bg-brand-600 text-white ring-2 ring-white"><x-icon name="check" :size="11" stroke-width="3.4" /></span></span>
                        <span class="flex min-w-0 flex-1 flex-col leading-tight">
                            <span class="truncate font-bold text-navy-950">{{ $t->user->first_name }} {{ mb_substr($t->user->last_name, 0, 1) }}.</span>
                            <span class="truncate text-[13px] text-ink-600">{{ $t->headline }}</span>
                        </span>
                        <span class="{{ $t->availabilityClass() }} shrink-0">{{ $t->availabilityLabel() }}</span>
                    </div>
                @empty
                    @foreach ([['Ingénieur génie civil', 'Formé en Chine'], ['Médecin généraliste', 'Formée au Maroc'], ['Développeuse web', 'Formée au Canada']] as $i => [$job, $hsk])
                        <div data-parallax="{{ 0.06 + $i * 0.06 }}" @class(['flex items-center gap-3.5 rounded-2xl bg-white p-4 text-ink-900 shadow-lift', 'sm:ml-10' => $i === 1, 'sm:ml-4' => $i === 2])>
                            <span class="flex size-12 items-center justify-center rounded-full bg-brand-50 text-brand-600"><x-icon name="user" :size="22" /></span>
                            <span class="flex min-w-0 flex-1 flex-col leading-tight"><span class="truncate font-bold text-navy-950">{{ $job }}</span><span class="text-[13px] text-ink-600">{{ $hsk }}</span></span>
                            <span class="badge-ok">Exemple</span>
                        </div>
                    @endforeach
                @endforelse
            </div>
        </x-slot:aside>
    </x-site.page-hero>

    {{-- Atouts --}}
    <section class="mx-auto flex max-w-[1200px] flex-col gap-10 px-4 py-16 sm:px-6 sm:py-24">
        <div data-reveal class="flex max-w-2xl flex-col gap-4">
            <span class="eyebrow">Pourquoi ces profils</span>
            <h2 class="text-[clamp(2rem,5.5vw,3rem)] font-extrabold leading-[1.05] text-navy-950">Le meilleur des deux mondes.</h2>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" data-reveal-stagger="100">
            @foreach ([
                ['language', 'Multilingues', 'Français, et selon leur parcours anglais, chinois, allemand, arabe… un atout pour travailler avec des partenaires étrangers.'],
                ['graduation', 'Formés à l’étranger', 'Ingénierie, santé, commerce, informatique, agronomie : des formations solides dans des universités reconnues.'],
                ['globe', 'Ouverts sur le monde', 'Ils connaissent les pratiques de travail de leur pays d’études et le contexte burkinabè.'],
                ['shield', 'Vérifiés', 'Chaque profil est contrôlé par Travel Express avant d’être visible des recruteurs.'],
            ] as [$icon, $t, $d])
                <div data-reveal class="card card-hover flex flex-col gap-3 p-6">
                    <span class="flex size-12 items-center justify-center rounded-2xl bg-linear-to-br from-brand-500 to-brand-600 text-white shadow-glow"><x-icon :name="$icon" :size="22" /></span>
                    <span class="font-display text-lg font-bold text-navy-950">{{ $t }}</span>
                    <span class="text-[14.5px] leading-relaxed text-ink-600">{{ $d }}</span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Fonctionnement --}}
    <section class="bg-canvas">
        <div class="mx-auto grid max-w-[1200px] gap-10 px-4 py-16 sm:px-6 sm:py-24 lg:grid-cols-[0.85fr_1.15fr] lg:gap-14">
            <div data-reveal class="flex flex-col gap-4 lg:sticky lg:top-24 lg:self-start">
                <span class="eyebrow">Comment ça marche</span>
                <h2 class="text-[clamp(2rem,5.5vw,3rem)] font-extrabold leading-[1.05] text-navy-950">Trois étapes pour recruter.</h2>
                <p class="text-[16.5px] leading-relaxed text-ink-600">Tout se passe sur la plateforme, sur le web ou l’application.</p>
                <a href="{{ route('register', ['profil' => 'recruiter']) }}" class="btn-primary mt-2 self-start">Créer mon compte recruteur</a>
            </div>
            <ol class="flex flex-col gap-4" data-reveal-stagger="120">
                @foreach ([
                    ['user', 'Créez votre compte recruteur', 'Quelques informations sur vous et votre entreprise. Votre adresse e-mail est vérifiée par un code.'],
                    ['search', 'Cherchez les bons profils', 'Filtrez le vivier par domaine, pays de formation, compétence ou disponibilité, et consultez les CV.'],
                    ['chat', 'Contactez directement', 'Écrivez aux candidats par la messagerie, ou publiez votre offre dans le thème Emplois de la communauté.'],
                ] as $i => [$icon, $t, $d])
                    <li data-reveal="right" class="card flex items-start gap-5 p-6">
                        <span class="relative flex size-14 shrink-0 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                            <x-icon :name="$icon" :size="24" />
                            <span class="absolute -right-1.5 -top-1.5 flex size-6 items-center justify-center rounded-full bg-gold-400 text-xs font-extrabold text-navy-950 ring-2 ring-white">{{ $i + 1 }}</span>
                        </span>
                        <span class="flex flex-col gap-1"><span class="font-display text-lg font-bold text-navy-950">{{ $t }}</span><span class="text-[15px] leading-relaxed text-ink-600">{{ $d }}</span></span>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Domaines --}}
    <section class="mx-auto flex max-w-[1200px] flex-col gap-8 px-4 py-16 sm:px-6 sm:py-24">
        <div data-reveal class="flex flex-wrap items-end justify-between gap-6">
            <div class="flex max-w-2xl flex-col gap-4">
                <span class="eyebrow">Le vivier</span>
                <h2 class="text-[clamp(2rem,5.5vw,3rem)] font-extrabold leading-[1.05] text-navy-950">Des profils dans tous les domaines.</h2>
            </div>
            @if ($fields->sum())
                <span class="text-[15px] text-ink-600"><b data-count="{{ $fields->sum() }}" class="font-display text-2xl text-navy-950">{{ $fields->sum() }}</b> profil{{ $fields->sum() > 1 ? 's' : '' }} vérifié{{ $fields->sum() > 1 ? 's' : '' }}</span>
            @endif
        </div>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6" data-reveal-stagger="60">
            @foreach (\App\Models\User::FIELDS as $f)
                @continue($f === 'Autre')
                <div data-reveal class="card card-hover flex flex-col gap-1 p-5">
                    <span class="font-display text-[16px] font-bold text-navy-950">{{ $f }}</span>
                    <span class="text-[13px] text-ink-500">{{ ($fields[$f] ?? 0) ? $fields[$f].' profil'.($fields[$f] > 1 ? 's' : '') : 'Bientôt' }}</span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Questions des recruteurs --}}
    <section class="bg-canvas">
        <div class="mx-auto flex max-w-[1200px] flex-col gap-8 px-4 py-16 sm:px-6 sm:py-24">
            <h2 data-reveal class="text-[clamp(1.8rem,4.5vw,2.6rem)] font-extrabold text-navy-950">Vos questions</h2>
            <div class="grid gap-4 md:grid-cols-2" data-reveal-stagger="90">
                @foreach ([
                    ['Combien coûte l’accès au vivier ?', '[À compléter par Travel Express : gratuit, abonnement ou commission.]'],
                    ['Comment les profils sont-ils vérifiés ?', 'L’équipe Travel Express contrôle chaque profil et le diplôme déclaré avant de le rendre visible. Les profils vérifiés portent un badge.'],
                    ['Puis-je publier une offre d’emploi ?', 'Oui : publiez-la dans le thème Emplois du super groupe de la communauté, visible de tous les membres.'],
                    ['Et si je cherche un profil très précis ?', 'Écrivez-nous : nous pouvons diffuser votre besoin auprès des diplômés et des étudiants en fin d’études.'],
                ] as [$q, $a])
                    <div data-reveal class="card flex flex-col gap-2 p-6">
                        <span class="font-display text-[17px] font-bold text-navy-950">{{ $q }}</span>
                        <span class="text-[15px] leading-relaxed text-ink-600">{{ $a }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <x-site.cta title="Trouvez vos prochains talents." text="Créez votre compte recruteur et accédez aux profils vérifiés par Travel Express.">
        <a href="{{ route('register', ['profil' => 'recruiter']) }}" class="btn-gold shine">Créer un compte recruteur <x-icon name="arrow" :size="18" /></a>
        <a href="{{ route('contact', ['sujet' => 'recrutement']) }}" class="btn-outline-light">Nous contacter</a>
    </x-site.cta>
</x-layouts.site>
