<x-layouts.app title="Vivier de talents" active="talents">
    <div class="flex flex-col gap-6 lg:gap-8">
        <x-page-header title="Vivier de talents" eyebrow="Formés à l’étranger" subtitle="Des diplômés formés à l’étranger et vérifiés par Travel Express, prêts à travailler au Burkina.">
            @if (auth()->user()->talentProfile)
                <x-slot:actions>
                    <a href="{{ route('talents.show', auth()->user()->talentProfile) }}" class="btn-secondary min-h-11 px-4 text-sm"><x-icon name="user" :size="18" /> Mon profil talent</a>
                </x-slot:actions>
            @endif
        </x-page-header>

        {{-- Recherche et filtres --}}
        <div class="card flex flex-col gap-4 p-4 sm:p-5">
            <form method="GET" action="{{ route('talents.index') }}" class="flex flex-col gap-2.5 sm:flex-row">
                @if ($field)<input type="hidden" name="domaine" value="{{ $field }}">@endif
                <label class="field-group min-h-[52px] flex-1 bg-ink-50/60">
                    <x-icon name="search" class="ml-4 text-ink-400" />
                    <span class="sr-only">Rechercher un profil</span>
                    <input name="q" value="{{ $q }}" placeholder="Métier, compétence, ville, nom…" class="min-w-0 flex-1 bg-transparent px-3 text-[15px] outline-none placeholder:text-ink-400">
                    @if ($q !== '')
                        <a href="{{ route('talents.index', array_filter(['domaine' => $field, 'pays' => $country])) }}" aria-label="Effacer la recherche" class="mr-2 flex size-8 items-center justify-center rounded-full text-ink-500 hover:bg-ink-100"><x-icon name="close" :size="16" /></a>
                    @endif
                </label>
                <label class="field-group min-h-[52px] bg-ink-50/60 sm:w-56">
                    <x-icon name="globe" :size="18" class="ml-4 shrink-0 text-ink-400" />
                    <span class="sr-only">Pays des études</span>
                    <select name="pays" onchange="this.form.submit()" class="min-w-0 flex-1 cursor-pointer bg-transparent px-3 text-[15px] outline-none">
                        <option value="">Tous les pays</option>
                        @foreach ($countries as $key => $name)
                            <option value="{{ $key }}" @selected($country === $key)>{{ $name }}</option>
                        @endforeach
                    </select>
                </label>
                <button class="btn-primary hidden min-h-[52px] sm:inline-flex">Rechercher</button>
            </form>

            <div class="-mx-1 flex gap-2 overflow-x-auto px-1 pb-1 scrollbar-thin sm:flex-wrap sm:overflow-visible sm:pb-0">
                <a href="{{ route('talents.index', array_filter(['q' => $q, 'pays' => $country])) }}" @class(['chip shrink-0', 'chip-on' => ! $field])>Tous les domaines</a>
                @foreach ($fields as $f)
                    <a href="{{ route('talents.index', array_filter(['q' => $q, 'pays' => $country, 'domaine' => $f])) }}" @class(['chip shrink-0', 'chip-on' => $field === $f])>{{ $f }}</a>
                @endforeach
            </div>
        </div>

        @if ($profiles->total())
            <div class="flex items-center justify-between">
                <span class="text-[14px] text-ink-600"><b class="text-navy-950">{{ $profiles->total() }}</b> profil{{ $profiles->total() > 1 ? 's' : '' }}{{ $field ? ' en '.$field : '' }}{{ $country ? ' · formés en '.$countries[$country] : '' }}{{ $q !== '' ? ' pour « '.$q.' »' : '' }}</span>
                <span class="hidden items-center gap-1.5 text-[13px] text-ink-500 sm:flex"><x-icon name="shield" :size="15" /> Profils vérifiés uniquement</span>
            </div>
        @endif

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($profiles as $profile)
                @include('app.talents._card')
            @empty
                <x-empty icon="users" title="Aucun profil trouvé" text="Essayez un autre mot-clé ou retirez le filtre de domaine." class="md:col-span-2 xl:col-span-3">
                    <a href="{{ route('talents.index') }}" class="btn-secondary min-h-11 text-sm">Réinitialiser la recherche</a>
                </x-empty>
            @endforelse
        </div>

        {{ $profiles->links() }}
    </div>
</x-layouts.app>
