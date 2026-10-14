@php
    $candidate = $dossier->user;
    $docs = $dossier->documents;
@endphp
<x-layouts.admin :title="'Dossier · '.$candidate->name" active="dossiers">
    <div class="flex flex-col gap-6">
        <a href="{{ route('admin.dossiers.index') }}" class="inline-flex items-center gap-1.5 self-start text-sm font-bold text-ink-600 hover:text-brand-600"><x-icon name="back" :size="18" /> Tous les dossiers</a>

        <x-page-header :title="$candidate->name" :subtitle="'Dossier ouvert le '.$dossier->created_at->translatedFormat('j F Y').' · étape '.$dossier->current_step.' sur '.count(\App\Models\Dossier::STEPS)">
            <x-slot:actions>
                <form method="POST" action="{{ route('chat.direct', $candidate) }}">@csrf
                    <button class="btn-primary min-h-11 px-4 text-sm"><x-icon name="chat" :size="18" /> Écrire au candidat</button>
                </form>
            </x-slot:actions>
        </x-page-header>
        <x-flash />
        @if ($errors->any())
            <div class="flex items-center gap-3 rounded-2xl bg-err-50 px-4 py-3.5 text-sm font-semibold text-err-700 ring-1 ring-err-700/15"><x-icon name="help" :size="18" /> {{ $errors->first() }}</div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)] lg:items-start lg:gap-8">
            <div class="flex flex-col gap-6 lg:sticky lg:top-10">
                {{-- Candidat --}}
                <section class="card flex flex-col">
                    <div class="flex items-center gap-4 border-b border-ink-100 p-5">
                        <x-avatar :user="$candidate" :size="56" />
                        <span class="flex min-w-0 flex-col"><span class="truncate font-display text-lg font-bold text-navy-950">{{ $candidate->name }}</span><span class="text-[13px] text-ink-500">{{ $candidate->role_label }}</span></span>
                    </div>
                    <dl class="flex flex-col p-2 text-[13.5px]">
                        @foreach (array_filter([
                            ['mail', 'E-mail', $candidate->email],
                            ['phone', 'Téléphone', \App\Support\Phone::format($candidate->phone)],
                            ['map', 'Ville', $candidate->city],
                            ['graduation', 'Dernier diplôme', $candidate->last_degree],
                            ['globe', 'Pays souhaité', \App\Support\Destinations::name($candidate->destination)],
                            ['language', 'Niveau de langue', \App\Models\User::LANGUAGE_LEVELS[$candidate->language_level] ?? null],
                            ['spark', 'Domaines', $candidate->interests ? implode(', ', $candidate->interests) : null],
                        ], fn ($r) => $r[2]) as [$icon, $label, $value])
                            <div class="flex items-start gap-3 rounded-xl px-3 py-2.5">
                                <x-icon :name="$icon" :size="16" class="mt-0.5 shrink-0 text-ink-400" />
                                <span class="flex min-w-0 flex-col"><dt class="text-xs text-ink-500">{{ $label }}</dt><dd class="break-words font-semibold text-navy-950">{{ $value }}</dd></span>
                            </div>
                        @endforeach
                    </dl>
                </section>

                {{-- Suivi du dossier --}}
                <section class="card">
                    <header class="border-b border-ink-100 px-5 py-4"><h2 class="text-[16px] font-bold text-navy-950">Suivi du dossier</h2></header>
                    <form method="POST" action="{{ route('admin.dossiers.update', $dossier) }}" class="flex flex-col gap-4 p-5">
                        @csrf @method('PUT')
                        <label class="flex flex-col gap-1.5"><span class="field-label">Étape</span>
                            <select name="current_step" class="field">@foreach (\App\Models\Dossier::STEPS as $n => [$label, $sub])<option value="{{ $n }}" @selected($dossier->current_step == $n)>{{ $n }}. {{ $label }}</option>@endforeach</select></label>
                        <label class="flex flex-col gap-1.5"><span class="field-label">Conseiller</span>
                            <select name="advisor_id" class="field"><option value="">Aucun</option>@foreach ($advisors as $a)<option value="{{ $a->id }}" @selected($dossier->advisor_id == $a->id)>{{ $a->name }}</option>@endforeach</select></label>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="flex flex-col gap-1.5"><span class="field-label">Pays</span>
                                <select name="country" class="field"><option value="">À définir</option>@foreach ($countries as $key => $name)<option value="{{ $key }}" @selected($dossier->country === $key)>{{ $name }}</option>@endforeach</select></label>
                            <label class="flex flex-col gap-1.5"><span class="field-label">Ville</span>
                                <input name="city" value="{{ old('city', $dossier->city) }}" class="field"></label>
                        </div>
                        <label class="flex flex-col gap-1.5"><span class="field-label">Programme</span>
                            <input name="program" value="{{ old('program', $dossier->program) }}" placeholder="Licence en informatique" class="field"></label>
                        <label class="flex flex-col gap-1.5"><span class="field-label">Université ou école</span>
                            <input name="university" value="{{ old('university', $dossier->university) }}" class="field"></label>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="flex flex-col gap-1.5"><span class="field-label">Rentrée</span>
                                <input name="intake_year" type="number" value="{{ old('intake_year', $dossier->intake_year) }}" class="field"></label>
                            <label class="flex flex-col gap-1.5"><span class="field-label">Statut</span>
                                <select name="status" class="field">@foreach (['open' => 'Ouvert', 'completed' => 'Terminé', 'cancelled' => 'Annulé'] as $k => $l)<option value="{{ $k }}" @selected($dossier->status === $k)>{{ $l }}</option>@endforeach</select></label>
                        </div>
                        <button class="btn-primary min-h-11 text-sm">Enregistrer</button>
                    </form>
                </section>
            </div>

            {{-- Documents --}}
            <section id="documents" class="flex scroll-mt-6 flex-col gap-3.5">
                <div class="flex items-baseline justify-between">
                    <h2 class="text-[20px] font-bold text-navy-950">Documents</h2>
                    <span class="text-[13px] font-semibold text-ink-500">{{ $docs->where('status', 'validated')->count() }} sur {{ $docs->count() }} validés</span>
                </div>

                @foreach ($docs as $doc)
                    <article class="card flex flex-col" x-data="{ note: {{ $doc->status === 'to_complete' ? 'true' : 'false' }} }">
                        <div class="flex flex-wrap items-center gap-3.5 p-4 sm:p-5">
                            <span @class(['flex size-11 shrink-0 items-center justify-center rounded-xl', 'bg-ok-50 text-ok-700' => $doc->status === 'validated', 'bg-warn-50 text-warn-700' => in_array($doc->status, ['pending', 'to_complete']), 'bg-err-50 text-err-700' => $doc->status === 'missing'])><x-icon :name="$doc->status === 'validated' ? 'check' : 'file'" :size="20" /></span>
                            <span class="flex min-w-0 flex-1 flex-col gap-1">
                                <span class="text-[15px] font-bold text-navy-950">{{ $doc->label }}</span>
                                <span class="flex flex-wrap items-center gap-2 text-[12.5px] text-ink-500">
                                    <span class="{{ $doc->statusClass() }}">{{ $doc->statusLabel() }}</span>
                                    @if ($doc->submitted_at)<span>Envoyé {{ $doc->submitted_at->diffForHumans() }}</span>@endif
                                </span>
                            </span>
                            @if ($doc->path)
                                <a href="{{ route('admin.documents.download', $doc) }}" target="_blank" rel="noopener" class="btn-secondary min-h-10 px-3.5 text-sm"><x-icon name="eye" :size="16" /> Ouvrir</a>
                            @endif
                        </div>
                        @if ($doc->note)
                            <p class="mx-4 mb-3 rounded-xl bg-warn-50 px-3.5 py-2.5 text-[13px] text-warn-700 sm:mx-5">Note au candidat : {{ $doc->note }}</p>
                        @endif

                        {{-- Décision --}}
                        @if ($doc->path || $doc->status !== 'missing')
                            <div class="flex flex-wrap gap-2 border-t border-ink-100 bg-ink-50/40 px-4 py-3 sm:px-5">
                                @unless ($doc->status === 'validated')
                                    <form method="POST" action="{{ route('admin.documents.review', $doc) }}">@csrf
                                        <input type="hidden" name="decision" value="validate">
                                        <button class="btn min-h-9 bg-ok-500 px-3.5 text-[13px] text-white hover:brightness-95"><x-icon name="check" :size="15" stroke-width="2.6" /> Valider</button>
                                    </form>
                                @endunless
                                <button type="button" @click="note = !note" class="btn min-h-9 bg-warn-50 px-3.5 text-[13px] text-warn-700 ring-1 ring-warn-700/15 hover:bg-warn-500 hover:text-white">Demander un complément</button>
                                <form method="POST" action="{{ route('admin.documents.review', $doc) }}" onsubmit="return confirm('Réinitialiser ce document ? Le candidat devra le renvoyer.')">@csrf
                                    <input type="hidden" name="decision" value="reset">
                                    <button class="btn-ghost min-h-9 px-3 text-[13px] text-ink-600">Réinitialiser</button>
                                </form>
                            </div>
                            <form x-show="note" x-cloak method="POST" action="{{ route('admin.documents.review', $doc) }}" class="flex flex-col gap-2.5 border-t border-ink-100 p-4 sm:p-5">
                                @csrf
                                <input type="hidden" name="decision" value="complete">
                                <label class="flex flex-col gap-1.5"><span class="field-label">Ce que le candidat doit corriger</span>
                                    <textarea name="note" rows="2" required maxlength="500" placeholder="Ex. Le relevé de terminale est manquant." class="field py-2.5 text-sm">{{ $doc->note }}</textarea></label>
                                <button class="btn-primary min-h-10 self-start px-4 text-sm">Renvoyer au candidat</button>
                            </form>
                        @else
                            <p class="border-t border-ink-100 px-5 py-3 text-[13px] text-ink-500">En attente d’envoi par le candidat.</p>
                        @endif
                    </article>
                @endforeach

                {{-- Pièce supplémentaire --}}
                <form method="POST" action="{{ route('admin.dossiers.documents.add', $dossier) }}" class="flex flex-col gap-2.5 rounded-2xl border-2 border-dashed border-ink-200 p-4 sm:flex-row sm:items-end">
                    @csrf
                    <label class="flex flex-1 flex-col gap-1.5"><span class="field-label">Demander une pièce supplémentaire</span>
                        <input name="label" required maxlength="120" placeholder="Ex. Lettre de recommandation" class="field"></label>
                    <button class="btn-secondary min-h-12 px-4 text-sm"><x-icon name="plus" :size="16" stroke-width="2.4" /> Ajouter</button>
                </form>
            </section>
        </div>
    </div>
</x-layouts.admin>
