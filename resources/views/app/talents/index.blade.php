<x-layouts.app title="Vivier de talents" active="talents">
    <div class="flex flex-col gap-4 lg:gap-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div class="flex flex-col gap-1">
                <h1 class="text-[26px] font-bold lg:text-[28px]">Vivier de talents</h1>
                <span class="text-[13.5px] text-ink-600 lg:text-[15px]">Diplômés formés en Chine, de retour au Burkina</span>
            </div>
            @if (auth()->user()->talentProfile)
                <a href="{{ route('talents.show', auth()->user()->talentProfile) }}" class="btn-secondary min-h-11 text-sm"><x-icon name="user" :size="18" /> Mon profil talent</a>
            @endif
        </div>

        <div class="flex flex-col gap-3 lg:rounded-xl lg:border lg:border-ink-200 lg:bg-white lg:p-4">
            <form method="GET" action="{{ route('talents.index') }}" class="flex gap-2.5">
                @if ($field)<input type="hidden" name="domaine" value="{{ $field }}">@endif
                <label class="flex min-h-[50px] flex-1 items-center gap-2.5 rounded-[10px] border border-ink-200 bg-white px-4 focus-within:border-brand-600 focus-within:ring-2 focus-within:ring-brand-100">
                    <x-icon name="search" class="text-ink-600" />
                    <span class="sr-only">Rechercher un profil</span>
                    <input name="q" value="{{ $q }}" placeholder="Métier, compétence, ville…" class="min-w-0 flex-1 bg-transparent text-[15px] outline-none">
                </label>
                <button class="btn-primary hidden min-h-[50px] sm:inline-flex">Rechercher</button>
            </form>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('talents.index', array_filter(['q' => $q])) }}" @class(['chip', 'chip-on' => ! $field])>Tous</a>
                @foreach ($fields as $f)
                    <a href="{{ route('talents.index', array_filter(['q' => $q, 'domaine' => $f])) }}" @class(['chip hover:border-brand-300', 'chip-on' => $field === $f])>{{ $f }}</a>
                @endforeach
            </div>
        </div>

        @if ($profiles->total())
            <span class="text-[13px] font-semibold text-ink-600">{{ $profiles->total() }} profil{{ $profiles->total() > 1 ? 's' : '' }}{{ $field ? ' en '.$field : '' }}{{ $q !== '' ? ' pour « '.$q.' »' : '' }}</span>
        @endif

        <div class="grid gap-3 md:grid-cols-2 lg:gap-4 xl:grid-cols-3">
            @forelse ($profiles as $profile)
                @include('app.talents._card')
            @empty
                <p class="rounded-xl bg-white p-6 text-center text-sm text-ink-600 md:col-span-2 xl:col-span-3">Aucun profil ne correspond à votre recherche.</p>
            @endforelse
        </div>

        {{ $profiles->links() }}
    </div>
</x-layouts.app>
