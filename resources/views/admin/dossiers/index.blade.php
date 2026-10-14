<x-layouts.admin title="Dossiers" active="dossiers">
    <div class="flex flex-col gap-6">
        <x-page-header eyebrow="Candidatures" title="Dossiers" subtitle="Suivez les dossiers, vérifiez les documents et attribuez les conseillers." />
        <x-flash />

        {{-- Filtres --}}
        <form method="GET" class="card grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-[1.4fr_repeat(4,1fr)_auto] lg:items-end">
            <label class="flex flex-col gap-1.5 sm:col-span-2 lg:col-span-1"><span class="field-label">Recherche</span>
                <span class="field-group min-h-11"><x-icon name="search" :size="17" class="ml-3 text-ink-400" /><input name="q" value="{{ $q }}" placeholder="Nom ou e-mail" class="min-w-0 flex-1 bg-transparent px-2.5 text-sm outline-none"></span></label>
            <label class="flex flex-col gap-1.5"><span class="field-label">Étape</span>
                <select name="etape" class="field min-h-11 text-sm"><option value="">Toutes</option>@foreach (\App\Models\Dossier::STEPS as $n => [$label])<option value="{{ $n }}" @selected(($filters['etape'] ?? '') == $n)>{{ $n }}. {{ $label }}</option>@endforeach</select></label>
            <label class="flex flex-col gap-1.5"><span class="field-label">Pays</span>
                <select name="pays" class="field min-h-11 text-sm"><option value="">Tous</option>@foreach ($countries as $key => $name)<option value="{{ $key }}" @selected(($filters['pays'] ?? '') === $key)>{{ $name }}</option>@endforeach</select></label>
            <label class="flex flex-col gap-1.5"><span class="field-label">Conseiller</span>
                <select name="conseiller" class="field min-h-11 text-sm"><option value="">Tous</option><option value="aucun" @selected(($filters['conseiller'] ?? '') === 'aucun')>Sans conseiller</option>@foreach ($advisors as $a)<option value="{{ $a->id }}" @selected(($filters['conseiller'] ?? '') == $a->id)>{{ $a->name }}</option>@endforeach</select></label>
            <label class="flex flex-col gap-1.5"><span class="field-label">Statut</span>
                <select name="statut" class="field min-h-11 text-sm">@foreach (['open' => 'Ouverts', 'completed' => 'Terminés', 'cancelled' => 'Annulés'] as $k => $l)<option value="{{ $k }}" @selected(($filters['statut'] ?? 'open') === $k)>{{ $l }}</option>@endforeach</select></label>
            <button class="btn-primary min-h-11 px-5 text-sm sm:col-span-2 lg:col-span-1">Filtrer</button>
            <label class="flex cursor-pointer items-center gap-2 text-[13px] font-semibold text-ink-700 sm:col-span-2 lg:col-span-6"><input type="checkbox" name="documents" value="a-verifier" @checked(($filters['documents'] ?? '') === 'a-verifier') class="size-4 accent-brand-600"> Seulement les dossiers avec des documents à vérifier</label>
        </form>

        {{-- Liste --}}
        <div class="card overflow-hidden">
            <div class="hidden grid-cols-[minmax(0,2.2fr)_minmax(0,1.4fr)_minmax(0,1fr)_minmax(0,1.2fr)_120px] gap-4 border-b border-ink-100 bg-ink-50/60 px-5 py-3 text-xs font-bold uppercase tracking-[0.1em] text-ink-500 lg:grid">
                <span>Candidat</span><span>Étape</span><span>Destination</span><span>Conseiller</span><span class="text-right">Documents</span>
            </div>
            @forelse ($dossiers as $d)
                <a href="{{ route('admin.dossiers.show', $d) }}" class="grid gap-3 border-b border-ink-100 px-5 py-4 transition last:border-0 hover:bg-ink-50/60 lg:grid-cols-[minmax(0,2.2fr)_minmax(0,1.4fr)_minmax(0,1fr)_minmax(0,1.2fr)_120px] lg:items-center lg:gap-4">
                    <span class="flex items-center gap-3">
                        <x-avatar :user="$d->user" :size="40" />
                        <span class="flex min-w-0 flex-col"><span class="truncate text-[14.5px] font-bold text-navy-950">{{ $d->user->name }}</span><span class="truncate text-[12.5px] text-ink-500">{{ $d->user->email }}</span></span>
                    </span>
                    <span class="flex flex-col gap-1.5">
                        <span class="text-[13.5px] font-semibold text-navy-950">{{ $d->current_step }}. {{ $d->stepLabel() }}</span>
                        <span class="h-1.5 overflow-hidden rounded-full bg-ink-100"><span class="block h-full rounded-full bg-brand-600" style="width: {{ $d->progressPercent() }}%"></span></span>
                    </span>
                    <span class="text-[13.5px] text-ink-700">{{ $d->countryName() ?? '—' }}<span class="block truncate text-[12px] text-ink-500">{{ $d->program }}</span></span>
                    <span class="text-[13.5px] {{ $d->advisor ? 'text-ink-700' : 'font-semibold text-warn-700' }}">{{ $d->advisor?->name ?? 'Aucun conseiller' }}</span>
                    <span class="flex flex-wrap gap-1.5 lg:justify-end">
                        @if ($d->pending_count)<span class="badge-warn">{{ $d->pending_count }} à vérifier</span>@endif
                        @if ($d->todo_count)<span class="badge-err">{{ $d->todo_count }} manquant(s)</span>@endif
                        @if (! $d->pending_count && ! $d->todo_count)<span class="badge-ok">Complet</span>@endif
                    </span>
                </a>
            @empty
                <x-empty icon="folder" title="Aucun dossier" text="Aucun dossier ne correspond à ces filtres." class="m-5" />
            @endforelse
        </div>

        {{ $dossiers->links() }}
    </div>
</x-layouts.admin>
