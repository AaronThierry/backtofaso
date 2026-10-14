@php
    $roleLabels = \App\Http\Controllers\Admin\UserController::ROLES;
    $maxStep = max(1, $bySteps->max() ?? 1);
@endphp
<x-layouts.admin title="Tableau de bord" active="dashboard">
    <div class="flex flex-col gap-6 lg:gap-8">
        <x-page-header eyebrow="{{ ucfirst(now()->translatedFormat('l j F')) }}" title="Administration" subtitle="Ce qui demande votre attention aujourd’hui." />
        <x-flash />

        <div class="grid grid-cols-2 gap-3 lg:grid-cols-6 lg:gap-4">
            <x-stat icon="upload" label="Documents à vérifier" :value="$stats['docsToReview']" hint="Dossiers" :href="route('admin.dossiers.index', ['documents' => 'a-verifier'])" tone="warn" class="lg:col-span-2" />
            <x-stat icon="shield" label="Talents à vérifier" :value="$stats['pendingTalents']" hint="Vivier" :href="route('admin.talents.index')" tone="gold" class="lg:col-span-2" />
            <x-stat icon="mail" label="Contacts à traiter" :value="$stats['contacts']" hint="Formulaire" :href="route('admin.contacts.index')" tone="brand" class="lg:col-span-2" />
            <x-stat icon="folder" label="Dossiers ouverts" :value="$stats['openDossiers']" hint="Voir" :href="route('admin.dossiers.index')" tone="ok" class="lg:col-span-3" />
            <x-stat icon="users" label="Membres" :value="$stats['users']" :hint="'+'.$stats['newUsers'].' cette semaine'" :href="auth()->user()->role === 'admin' ? route('admin.users.index') : null" tone="ink" class="lg:col-span-3" />
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,3fr)_minmax(0,2fr)] lg:items-start lg:gap-8">
            <div class="flex flex-col gap-6">
                {{-- File de vérification --}}
                <section class="card flex flex-col">
                    <header class="flex items-center justify-between gap-3 border-b border-ink-100 px-5 py-4">
                        <h2 class="flex items-center gap-2.5 text-[16px] font-bold text-navy-950"><span class="flex size-8 items-center justify-center rounded-lg bg-warn-50 text-warn-700"><x-icon name="upload" :size="16" /></span> Documents à vérifier</h2>
                        <a href="{{ route('admin.dossiers.index', ['documents' => 'a-verifier']) }}" class="text-[13px] font-bold text-brand-600 hover:underline">Tout voir</a>
                    </header>
                    @forelse ($docsQueue as $doc)
                        <a href="{{ route('admin.dossiers.show', $doc->dossier_id) }}#documents" class="flex items-center gap-3 border-b border-ink-100 px-5 py-3.5 transition last:border-0 hover:bg-ink-50/60">
                            <x-avatar :user="$doc->dossier->user" :size="38" />
                            <span class="flex min-w-0 flex-1 flex-col">
                                <span class="truncate text-[14px] font-bold text-navy-950">{{ $doc->dossier->user->name }}</span>
                                <span class="truncate text-[12.5px] text-ink-500">{{ $doc->label }} · envoyé {{ $doc->submitted_at?->diffForHumans() }}</span>
                            </span>
                            <span class="badge-warn">À vérifier</span>
                        </a>
                    @empty
                        <p class="flex items-center justify-center gap-2 px-5 py-8 text-sm text-ink-500"><x-icon name="check" :size="16" class="text-ok-700" /> Aucun document en attente.</p>
                    @endforelse
                </section>

                {{-- Dossiers par étape --}}
                <section class="card flex flex-col gap-4 p-5">
                    <h2 class="text-[16px] font-bold text-navy-950">Dossiers ouverts par étape</h2>
                    <div class="flex flex-col gap-3">
                        @foreach (\App\Models\Dossier::STEPS as $n => [$label])
                            <a href="{{ route('admin.dossiers.index', ['etape' => $n]) }}" class="group grid grid-cols-[150px_1fr_32px] items-center gap-3 text-[13.5px]">
                                <span class="truncate font-semibold text-ink-700 group-hover:text-brand-600">{{ $n }}. {{ $label }}</span>
                                <span class="h-2.5 overflow-hidden rounded-full bg-ink-100"><span class="block h-full rounded-full bg-linear-to-r from-brand-500 to-brand-600" style="width: {{ round(($bySteps[$n] ?? 0) / $maxStep * 100) }}%"></span></span>
                                <span class="text-right font-bold tabular-nums text-navy-950">{{ $bySteps[$n] ?? 0 }}</span>
                            </a>
                        @endforeach
                    </div>
                </section>
            </div>

            <div class="flex flex-col gap-6">
                {{-- Contacts --}}
                <section class="card flex flex-col">
                    <header class="flex items-center justify-between gap-3 border-b border-ink-100 px-5 py-4">
                        <h2 class="flex items-center gap-2.5 text-[16px] font-bold text-navy-950"><span class="flex size-8 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><x-icon name="mail" :size="16" /></span> Contacts à traiter</h2>
                        <a href="{{ route('admin.contacts.index') }}" class="text-[13px] font-bold text-brand-600 hover:underline">Tout voir</a>
                    </header>
                    @forelse ($recentContacts as $m)
                        <a href="{{ route('admin.contacts.index') }}#message-{{ $m->id }}" class="flex flex-col gap-0.5 border-b border-ink-100 px-5 py-3.5 transition last:border-0 hover:bg-ink-50/60">
                            <span class="flex items-baseline justify-between gap-2"><span class="truncate text-[14px] font-bold text-navy-950">{{ $m->name }}</span><span class="shrink-0 text-[11.5px] text-ink-400">{{ $m->created_at->diffForHumans() }}</span></span>
                            <span class="text-[12.5px] font-semibold text-brand-700">{{ $m->subjectLabel() }}</span>
                            <span class="line-clamp-1 text-[13px] text-ink-600">{{ $m->message }}</span>
                        </a>
                    @empty
                        <p class="px-5 py-8 text-center text-sm text-ink-500">Aucun message en attente.</p>
                    @endforelse
                </section>

                {{-- Membres --}}
                <section class="card flex flex-col">
                    <header class="flex items-center justify-between gap-3 border-b border-ink-100 px-5 py-4">
                        <h2 class="text-[16px] font-bold text-navy-950">Dernières inscriptions</h2>
                        @if (auth()->user()->role === 'admin')<a href="{{ route('admin.users.index') }}" class="text-[13px] font-bold text-brand-600 hover:underline">Utilisateurs</a>@endif
                    </header>
                    @foreach ($recentUsers as $u)
                        <div class="flex items-center gap-3 border-b border-ink-100 px-5 py-3 last:border-0">
                            <x-avatar :user="$u" :size="34" />
                            <span class="flex min-w-0 flex-1 flex-col"><span class="truncate text-[13.5px] font-bold text-navy-950">{{ $u->name }}</span><span class="truncate text-[12px] text-ink-500">{{ $roleLabels[$u->role] ?? $u->role }}</span></span>
                            <span class="shrink-0 text-[11.5px] text-ink-400">{{ $u->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                    <div class="flex flex-wrap gap-1.5 border-t border-ink-100 px-5 py-3.5">
                        @foreach ($byRole as $role => $total)
                            <span class="rounded-full bg-ink-50 px-2.5 py-1 text-xs font-semibold text-ink-700 ring-1 ring-ink-100">{{ $roleLabels[$role] ?? $role }} · {{ $total }}</span>
                        @endforeach
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-layouts.admin>
