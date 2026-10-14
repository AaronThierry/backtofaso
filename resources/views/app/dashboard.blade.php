@php
    $hour = (int) now()->format('G');
    $greeting = $hour >= 5 && $hour < 18 ? 'Bonjour' : 'Bonsoir';

    // Dossier : documents et prochaine action
    $docs = $dossier?->documents ?? collect();
    $docsTodo = $docs->whereIn('status', [\App\Models\DossierDocument::MISSING, \App\Models\DossierDocument::TO_COMPLETE]);
    $docsDone = $docs->where('status', \App\Models\DossierDocument::VALIDATED)->count();
    $docsPending = $docs->where('status', \App\Models\DossierDocument::PENDING)->count();

    // Profil talent : complétude
    $completion = $profile?->completion();

    // Prochaine étape, selon le profil : [icône, titre, texte, lien, bouton]
    $next = match (true) {
        $user->isStaff() => ['shield', 'Votre espace d’administration', 'Vérifiez les documents des dossiers, les profils talents en attente et les messages de contact.', route('admin.dashboard'), 'Ouvrir l’administration'],
        (bool) $dossier && $docsTodo->isNotEmpty() => ['upload', $docsTodo->count().' document(s) à fournir', 'Déposez-les dès maintenant pour que votre conseiller puisse avancer sur votre dossier.', route('dossier'), 'Déposer mes documents'],
        (bool) $dossier && $docsPending > 0 => ['clock', 'Documents en cours de vérification', 'Votre conseiller vérifie les pièces envoyées. Vous serez prévenu dès qu’il y a du nouveau.', route('dossier'), 'Suivre mon dossier'],
        (bool) $dossier => ['folder', 'Étape en cours : '.$dossier->stepLabel(), 'Suivez l’avancement de votre dossier et échangez avec votre conseiller.', route('dossier'), 'Voir mon dossier'],
        $profile && $completion['percent'] < 100 => ['user', 'Complétez votre profil talent', 'Un profil complet attire beaucoup plus les recruteurs. Il vous reste '.collect($completion['items'])->where('done', false)->count().' point(s) à remplir.', route('talents.edit'), 'Compléter mon profil'],
        $profile && ! $profile->verified_at => ['clock', 'Profil en attente de vérification', 'Travel Express vérifie votre profil et votre diplôme. Il sera ensuite visible des recruteurs.', route('talents.show', $profile), 'Voir mon profil'],
        (bool) $profile => ['check', 'Votre profil est visible des recruteurs', 'Gardez-le à jour et surveillez vos messages : les recruteurs vous contactent par la messagerie.', route('talents.show', $profile), 'Voir mon profil'],
        $user->isRecruiter() => ['search', ($stats['available'] ?? 0).' talent(s) disponible(s) maintenant', 'Parcourez le vivier, filtrez par domaine ou pays de formation, et contactez les candidats.', route('talents.index'), 'Parcourir le vivier'],
        default => ['chat', 'Rejoignez la conversation', 'Posez vos questions et échangez avec les membres de la communauté.', route('chat.show', $super), 'Ouvrir la communauté'],
    };
@endphp
<x-layouts.app title="Accueil" active="dashboard">
    <div class="flex flex-col gap-6 lg:gap-7">
        {{-- En-tête --}}
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 lg:hidden">
                <x-logo :size="40" />
                <span class="flex flex-col leading-tight"><span class="font-display text-[18px] font-bold">Back to Burkina</span><span class="text-xs text-ink-600">par Travel Express</span></span>
            </a>
            <div class="hidden flex-col gap-1 lg:flex">
                <span class="eyebrow">{{ ucfirst(now()->translatedFormat('l j F')) }}</span>
                <h1 class="text-[32px] font-bold leading-tight text-navy-950">{{ $greeting }}, {{ $user->first_name }}</h1>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('chat.index') }}" aria-label="Messages{{ $unread ? ' ('.$unread.' non lus)' : '' }}" class="icon-btn relative">
                    <x-icon name="bell" />
                    @if ($unread)
                        <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-err-500 px-1 text-[10.5px] font-extrabold text-white ring-2 ring-canvas">{{ $unread > 9 ? '9+' : $unread }}</span>
                    @endif
                </a>
                <a href="{{ route('account') }}" class="hidden rounded-full ring-2 ring-white transition hover:ring-brand-200 sm:block" aria-label="Mon compte"><x-avatar :user="$user" :size="44" /></a>
            </div>
        </div>

        <x-flash />

        {{-- Prochaine étape --}}
        <section class="bg-hero relative animate-rise overflow-hidden rounded-[24px] p-5 text-white shadow-lift sm:p-7">
            <div class="bg-weave pointer-events-none absolute inset-y-0 right-0 w-1/3 opacity-60 [mask-image:linear-gradient(to_left,black,transparent)]"></div>
            <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center">
                @if ($dossier)
                    <span class="hidden sm:block"><x-progress-ring :percent="$dossier->progressPercent()" :size="76" :stroke="7" dark /></span>
                @elseif ($completion)
                    <span class="hidden sm:block"><x-progress-ring :percent="$completion['percent']" :size="76" :stroke="7" dark /></span>
                @else
                    <span class="glass hidden size-16 shrink-0 items-center justify-center rounded-2xl text-gold-300 sm:flex"><x-icon :name="$next[0]" :size="28" /></span>
                @endif
                <div class="flex min-w-0 flex-1 flex-col gap-1.5">
                    <span class="eyebrow text-gold-300">Votre prochaine étape</span>
                    <h2 class="text-[22px] font-bold leading-tight sm:text-[26px]">{{ $next[1] }}</h2>
                    <p class="max-w-2xl text-[14.5px] leading-relaxed text-white/70">{{ $next[2] }}</p>
                </div>
                <a href="{{ $next[3] }}" class="btn-gold shine shrink-0 self-start sm:self-center">{{ $next[4] }} <x-icon name="arrow" :size="18" /></a>
            </div>
        </section>

        {{-- Chiffres clés --}}
        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4 lg:gap-4" data-reveal-stagger="70">
            @if ($user->isStaff())
                <x-stat data-reveal icon="shield" label="Profils à vérifier" :value="$stats['pending_talents']" hint="Administration" :href="route('admin.talents.index')" tone="gold" />
                <x-stat data-reveal icon="mail" label="Messages de contact" :value="$stats['contacts']" hint="Non traités" :href="route('admin.contacts.index')" tone="warn" />
                <x-stat data-reveal icon="folder" label="Dossiers ouverts" :value="$stats['dossiers']" hint="Suivi" :href="route('admin.dossiers.index')" tone="brand" />
                <x-stat data-reveal icon="chat" label="Messages non lus" :value="$unread" hint="Messagerie" :href="route('chat.index')" tone="ok" />
            @elseif ($dossier)
                <x-stat data-reveal icon="folder" label="Avancement" :value="$dossier->progressPercent().' %'" :hint="'Étape '.$dossier->current_step.' sur '.count(\App\Models\Dossier::STEPS)" :href="route('dossier')" />
                <x-stat data-reveal icon="check" label="Documents validés" :value="$docsDone.' / '.$docs->count()" :hint="$docsTodo->count() ? $docsTodo->count().' à fournir' : 'Tout est envoyé'" :href="route('dossier')" tone="ok" />
                <x-stat data-reveal icon="globe" label="Destination" :value="$dossier->countryName() ?? 'À définir'" :hint="$dossier->program ?? 'Programme à définir'" tone="gold" />
                <x-stat data-reveal icon="chat" label="Messages non lus" :value="$unread" hint="Messagerie" :href="route('chat.index')" tone="ink" />
            @elseif ($profile)
                <x-stat data-reveal icon="user" label="Profil complété" :value="$completion['percent'].' %'" hint="Compléter" :href="route('talents.edit')" />
                <x-stat data-reveal icon="shield" label="Statut" :value="$profile->verified_at ? 'Vérifié' : 'En attente'" :hint="$profile->is_public ? 'Visible des recruteurs' : 'Masqué aux recruteurs'" :tone="$profile->verified_at ? 'ok' : 'warn'" />
                <x-stat data-reveal icon="briefcase" label="Offres récentes" :value="$jobs->count()" hint="Thème Emplois" :href="route('chat.show', [$super, 'theme' => 'emplois'])" tone="gold" />
                <x-stat data-reveal icon="chat" label="Messages non lus" :value="$unread" hint="Messagerie" :href="route('chat.index')" tone="ink" />
            @elseif ($user->isRecruiter())
                <x-stat data-reveal icon="users" label="Profils vérifiés" :value="$stats['talents']" hint="Dans le vivier" :href="route('talents.index')" />
                <x-stat data-reveal icon="check" label="Disponibles" :value="$stats['available']" hint="Dès maintenant" :href="route('talents.index')" tone="ok" />
                <x-stat data-reveal icon="briefcase" label="Offres récentes" :value="$jobs->count()" hint="Thème Emplois" :href="route('chat.show', [$super, 'theme' => 'emplois'])" tone="gold" />
                <x-stat data-reveal icon="chat" label="Messages non lus" :value="$unread" hint="Messagerie" :href="route('chat.index')" tone="ink" />
            @endif
        </div>

        <div class="grid gap-6 lg:grid-cols-3 lg:items-start lg:gap-7">
            {{-- Colonne principale --}}
            <div class="flex flex-col gap-6 lg:col-span-2 lg:gap-7">
                {{-- Dossier --}}
                @if ($dossier)
                    <section class="card flex flex-col">
                        <header class="flex items-center justify-between gap-3 border-b border-ink-100 px-5 py-4">
                            <h2 class="flex items-center gap-2.5 text-[16px] font-bold text-navy-950"><span class="flex size-8 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><x-icon name="folder" :size="16" /></span> Mon dossier</h2>
                            <a href="{{ route('dossier') }}" class="text-[13px] font-bold text-brand-600 hover:underline">Ouvrir</a>
                        </header>
                        <ol class="grid grid-cols-6 gap-1.5 px-5 pt-5">
                            @foreach ($dossier->stepsWithState() as $s)
                                <li class="flex flex-col gap-2">
                                    <span @class(['h-1.5 rounded-full', 'bg-brand-600' => $s['state'] === 'done', 'bg-gold-400' => $s['state'] === 'now', 'bg-ink-100' => $s['state'] === 'todo'])></span>
                                    <span @class(['hidden text-[11.5px] leading-tight sm:block', 'font-bold text-navy-950' => $s['state'] === 'now', 'font-semibold text-ink-500' => $s['state'] !== 'now'])>{{ $s['title'] }}</span>
                                </li>
                            @endforeach
                        </ol>
                        <div class="flex flex-col gap-1 px-5 py-4">
                            @forelse ($docsTodo->take(4) as $doc)
                                <a href="{{ route('dossier') }}" class="flex items-center gap-3 rounded-xl px-2 py-2.5 transition hover:bg-ink-50">
                                    <span class="flex size-9 shrink-0 items-center justify-center rounded-lg {{ $doc->status === \App\Models\DossierDocument::MISSING ? 'bg-err-50 text-err-700' : 'bg-warn-50 text-warn-700' }}"><x-icon name="file" :size="17" /></span>
                                    <span class="flex min-w-0 flex-1 flex-col"><span class="truncate text-[14px] font-semibold text-navy-950">{{ $doc->label }}</span>@if ($doc->note)<span class="truncate text-[12.5px] text-warn-700">{{ $doc->note }}</span>@endif</span>
                                    <span class="{{ $doc->statusClass() }}">{{ $doc->statusLabel() }}</span>
                                </a>
                            @empty
                                <p class="flex items-center gap-2.5 rounded-xl bg-ok-50 px-3.5 py-3 text-[13.5px] font-semibold text-ok-700"><x-icon name="check" :size="17" stroke-width="2.4" /> Tous vos documents ont été envoyés.</p>
                            @endforelse
                            @if ($docsTodo->count() > 4)
                                <a href="{{ route('dossier') }}" class="px-2 pt-1 text-[13px] font-bold text-brand-600 hover:underline">Et {{ $docsTodo->count() - 4 }} autre(s)…</a>
                            @endif
                        </div>
                    </section>
                @endif

                {{-- Profil talent --}}
                @if ($profile)
                    <section class="card flex flex-col">
                        <header class="flex items-center justify-between gap-3 border-b border-ink-100 px-5 py-4">
                            <h2 class="flex items-center gap-2.5 text-[16px] font-bold text-navy-950"><span class="flex size-8 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><x-icon name="briefcase" :size="16" /></span> Mon profil talent</h2>
                            <span class="text-[13px] font-bold text-navy-950">{{ $completion['percent'] }} %</span>
                        </header>
                        <div class="px-5 pt-4"><span class="block h-2 overflow-hidden rounded-full bg-ink-100"><span class="block h-full rounded-full bg-linear-to-r from-brand-500 to-gold-400 transition-all duration-700" style="width: {{ $completion['percent'] }}%"></span></span></div>
                        <ul class="grid gap-1 p-3 sm:grid-cols-2">
                            @foreach ($completion['items'] as $item)
                                <li>
                                    <a href="{{ route('talents.edit') }}" class="flex items-start gap-3 rounded-xl px-2.5 py-2.5 transition hover:bg-ink-50">
                                        <span @class(['mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full', 'bg-ok-500 text-white' => $item['done'], 'ring-2 ring-ink-200' => ! $item['done']])>@if ($item['done'])<x-icon name="check" :size="11" stroke-width="3.4" />@endif</span>
                                        <span class="flex flex-col"><span @class(['text-[14px] font-semibold', 'text-ink-500 line-through decoration-ink-300' => $item['done'], 'text-navy-950' => ! $item['done']])>{{ $item['label'] }}</span>@unless ($item['done'])<span class="text-[12.5px] text-ink-500">{{ $item['hint'] }}</span>@endunless</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="flex flex-wrap gap-2.5 border-t border-ink-100 px-5 py-4">
                            <a href="{{ route('talents.edit') }}" class="btn-primary min-h-11 px-4 text-sm">Modifier mon profil</a>
                            <a href="{{ route('talents.show', $profile) }}" class="btn-secondary min-h-11 px-4 text-sm"><x-icon name="eye" :size="17" /> Voir comme un recruteur</a>
                        </div>
                    </section>
                @endif

                {{-- Talents (recruteurs et équipe) --}}
                @if ($talents->isNotEmpty())
                    <section class="flex flex-col gap-3.5">
                        <div class="flex items-baseline justify-between">
                            <h2 class="text-[19px] font-bold text-navy-950">Talents récemment vérifiés</h2>
                            <a href="{{ route('talents.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-brand-600 transition-all hover:gap-2">Tout le vivier <x-icon name="arrow" :size="16" /></a>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach ($talents->take(2) as $t)
                                @include('app.talents._card', ['profile' => $t])
                            @endforeach
                        </div>
                    </section>
                @endif

                @include('app._feed', ['title' => 'Annonces', 'icon' => 'bell', 'messages' => $announcements, 'topic' => 'annonces', 'empty' => 'Aucune annonce pour le moment.'])

                @if ($profile || $user->isRecruiter())
                    @include('app._feed', ['title' => 'Offres d’emploi', 'icon' => 'briefcase', 'messages' => $jobs, 'topic' => 'emplois', 'empty' => 'Aucune offre publiée pour le moment.'])
                @endif
            </div>

            {{-- Colonne latérale --}}
            <aside class="flex flex-col gap-6 lg:sticky lg:top-10">
                {{-- Conseiller, recrutement ou aide --}}
                @if ($dossier?->advisor)
                    <section class="card flex flex-col items-center gap-3 p-6 text-center">
                        <x-avatar :user="$dossier->advisor" :size="64" tone="gold" />
                        <span class="flex flex-col gap-0.5">
                            <span class="text-xs font-bold uppercase tracking-[0.12em] text-ink-500">Votre conseiller</span>
                            <span class="font-display text-lg font-bold text-navy-950">{{ $dossier->advisor->name }}</span>
                            <span class="text-[13px] text-ink-600">Travel Express</span>
                        </span>
                        <form method="POST" action="{{ route('chat.direct', $dossier->advisor) }}" class="w-full">@csrf
                            <button class="btn-primary w-full"><x-icon name="chat" :size="18" /> Lui écrire</button>
                        </form>
                    </section>
                @elseif ($user->isRecruiter())
                    <section class="bg-hero relative flex flex-col gap-3 overflow-hidden rounded-2xl p-6 text-white">
                        <div class="bg-weave pointer-events-none absolute inset-0 opacity-40"></div>
                        <span class="glass relative flex size-11 items-center justify-center rounded-xl text-gold-300"><x-icon name="briefcase" :size="20" /></span>
                        <span class="relative font-display text-lg font-bold">Vous recrutez ?</span>
                        <span class="relative text-[13.5px] leading-relaxed text-white/70">Publiez votre offre dans le thème Emplois : tous les membres la verront.</span>
                        <a href="{{ route('chat.show', [$super, 'theme' => 'emplois']) }}" class="btn-light relative mt-1 min-h-11 text-sm">Publier une offre</a>
                    </section>
                @else
                    <section class="card flex flex-col gap-3 p-5">
                        <span class="flex size-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><x-icon name="help" :size="20" /></span>
                        <span class="font-display text-[17px] font-bold text-navy-950">Une question ?</span>
                        <span class="text-[13.5px] leading-relaxed text-ink-600">L’équipe Travel Express et les membres répondent dans le thème Entraide.</span>
                        <a href="{{ route('chat.show', [$super, 'theme' => 'entraide']) }}" class="btn-secondary min-h-11 text-sm">Poser une question</a>
                    </section>
                @endif

                @if ($scholarships->isNotEmpty())
                    @include('app._feed', ['title' => 'Bourses', 'icon' => 'graduation', 'messages' => $scholarships->take(2), 'topic' => 'bourses', 'empty' => ''])
                @endif

                <section class="flex flex-col gap-3">
                    <h2 class="text-[15px] font-bold text-navy-950">Accès rapide</h2>
                    <div class="card flex flex-col p-1.5">
                        @foreach (array_values(array_filter([
                            $dossier ? [route('dossier'), 'folder', 'Mon dossier', 'Étapes et documents'] : null,
                            $profile ? [route('talents.edit'), 'briefcase', 'Mon profil talent', 'Visible des recruteurs'] : null,
                            [route('chat.index'), 'chat', 'Messages', $unread ? $unread.' non lu(s)' : 'Conversations et groupes'],
                            [route('talents.index'), 'users', 'Vivier de talents', 'Diplômés formés à l’étranger'],
                            [route('account'), 'user', 'Mon compte', 'Profil, photo et notifications'],
                        ])) as [$url, $icon, $t, $d])
                            <a href="{{ $url }}" class="group flex items-center gap-3 rounded-xl px-3 py-2.5 transition hover:bg-ink-50">
                                <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600 transition group-hover:bg-brand-600 group-hover:text-white"><x-icon :name="$icon" :size="17" /></span>
                                <span class="flex min-w-0 flex-1 flex-col"><span class="text-sm font-bold">{{ $t }}</span><span class="truncate text-xs text-ink-600">{{ $d }}</span></span>
                                <x-icon name="chevron" :size="16" class="text-ink-300 transition group-hover:translate-x-0.5 group-hover:text-brand-600" />
                            </a>
                        @endforeach
                    </div>
                </section>

                <a href="https://burkina24.com/?p=454535" target="_blank" rel="noopener" class="group flex items-center gap-3.5 rounded-2xl bg-navy-950 p-4 text-white transition hover:bg-navy-900">
                    <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-white text-navy-950 transition group-hover:scale-105"><x-icon name="play" :size="16" /></span>
                    <span class="flex flex-col gap-0.5"><span class="text-[11px] font-bold uppercase tracking-[0.14em] text-gold-300">Vu dans la presse</span><span class="text-[13.5px] font-semibold leading-snug">Burkina 24 : un entrepreneur au service du retour des compétences</span></span>
                </a>
            </aside>
        </div>
    </div>
</x-layouts.app>
