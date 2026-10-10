<x-layouts.app title="Vivier de talents" active="talents">
    <div class="flex flex-col gap-4">
        <div class="flex flex-col gap-1">
            <h1 class="text-[26px] font-bold">Vivier de talents</h1>
            <span class="text-[13.5px] text-ink-600">Diplômés formés en Chine, de retour au Burkina</span>
        </div>

        <form method="GET" action="{{ route('talents.index') }}" class="flex flex-col gap-3">
            @if ($field)<input type="hidden" name="domaine" value="{{ $field }}">@endif
            <label class="flex min-h-[50px] items-center gap-2.5 rounded-[10px] border border-ink-200 bg-white px-4 focus-within:border-brand-600">
                <x-icon name="search" class="text-ink-600" />
                <span class="sr-only">Rechercher un profil</span>
                <input name="q" value="{{ $q }}" placeholder="Métier, compétence, ville…" class="min-w-0 flex-1 bg-transparent text-[15px] outline-none">
            </label>
        </form>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('talents.index', array_filter(['q' => $q])) }}" @class(['chip', 'chip-on' => ! $field])>Tous</a>
            @foreach ($fields as $f)
                <a href="{{ route('talents.index', array_filter(['q' => $q, 'domaine' => $f])) }}" @class(['chip', 'chip-on' => $field === $f])>{{ $f }}</a>
            @endforeach
        </div>

        <div class="grid gap-3 lg:grid-cols-2">
            @forelse ($profiles as $profile)
                @include('app.talents._card')
            @empty
                <p class="rounded-xl bg-white p-6 text-center text-sm text-ink-600 lg:col-span-2">Aucun profil ne correspond à votre recherche.</p>
            @endforelse
        </div>

        {{ $profiles->links() }}
    </div>
</x-layouts.app>
