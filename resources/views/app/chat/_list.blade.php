{{-- Liste des conversations. Variables : $user, $filter, $super, $groups, $directs, $publicGroups, $activeId (conversation ouverte ou null) --}}
<div class="flex flex-col gap-4" x-data="{ q: '' }">
    <div class="flex items-center justify-between gap-3">
        <h1 class="text-[26px] font-bold text-navy-950 lg:text-[28px]">Messages</h1>
        <a href="{{ route('groups.create') }}" class="btn-primary min-h-10 rounded-full px-3.5 text-sm" title="Nouveau groupe"><x-icon name="plus" :size="18" stroke-width="2.4" /> Groupe</a>
    </div>

    <label class="field-group min-h-11 bg-ink-50/70">
        <x-icon name="search" :size="18" class="ml-3.5 text-ink-400" />
        <span class="sr-only">Filtrer les conversations</span>
        <input x-model="q" type="search" placeholder="Rechercher une conversation" class="min-w-0 flex-1 bg-transparent px-2.5 text-sm outline-none placeholder:text-ink-400">
    </label>

    <div class="grid grid-cols-3 gap-1 rounded-xl bg-ink-100/80 p-1" role="tablist">
        @foreach (['tous' => 'Tous', 'groupes' => 'Groupes', 'prives' => 'Privés'] as $key => $label)
            <a href="{{ route('chat.index', ['filtre' => $key]) }}" role="tab" aria-selected="{{ $filter === $key ? 'true' : 'false' }}"
               @class(['flex min-h-9 items-center justify-center rounded-lg text-[13.5px] transition', 'bg-white font-bold text-navy-950 shadow-sm' => $filter === $key, 'font-semibold text-ink-600 hover:text-ink-900' => $filter !== $key])>{{ $label }}</a>
        @endforeach
    </div>

    @if ($super)
        @php($lastAnn = $super->messages()->where('topic', 'annonces')->with('user')->latest()->first())
        <a href="{{ route('chat.show', $super) }}" x-show="!q || @js(mb_strtolower($super->name)).includes(q.toLowerCase())"
           @class(['bg-hero group relative flex flex-col gap-3 overflow-hidden rounded-2xl p-4 text-white transition hover:shadow-lift', 'ring-2 ring-gold-400 ring-offset-2' => $activeId === $super->id])>
            <div class="bg-weave pointer-events-none absolute inset-y-0 right-0 w-1/2 opacity-50 [mask-image:linear-gradient(to_left,black,transparent)]"></div>
            <span class="relative flex items-center gap-3">
                <x-logo :size="46" />
                <span class="flex min-w-0 flex-1 flex-col gap-0.5">
                    <span class="truncate text-[15px] font-bold">{{ $super->name }}</span>
                    <span class="flex items-center gap-1.5 text-[12.5px] text-white/65"><span class="size-1.5 rounded-full bg-ok-500"></span> Super groupe · {{ $super->users->count() }} membres</span>
                </span>
                @if ($n = $super->unreadCountFor($user))
                    <span class="flex h-6 min-w-6 items-center justify-center rounded-full bg-gold-400 px-2 text-xs font-extrabold text-navy-950">{{ $n }}</span>
                @endif
            </span>
            @if ($lastAnn)
                <span class="glass relative rounded-xl px-3 py-2.5 text-[13px] leading-snug text-white/85"><b class="text-white">{{ $lastAnn->user->name }} :</b> {{ \Illuminate\Support\Str::limit($lastAnn->body, 100) }}</span>
            @endif
        </a>
    @endif

    @if ($filter !== 'prives')
        <section class="flex flex-col gap-1">
            <span class="px-1 pb-1 text-[11px] font-bold uppercase tracking-[0.14em] text-ink-500">Mes groupes</span>
            @forelse ($groups as $c)
                @include('app.chat._row', ['conversation' => $c, 'active' => $activeId === $c->id])
            @empty
                <p class="rounded-xl bg-ink-50 px-4 py-3.5 text-[13.5px] text-ink-600">Pas encore de groupe. <a href="{{ route('groups.create') }}" class="font-bold text-brand-600 hover:underline">Créez-en un</a> par ville, promo ou projet.</p>
            @endforelse
        </section>

        @if ($publicGroups->isNotEmpty())
            <section class="flex flex-col gap-2">
                <span class="px-1 text-[11px] font-bold uppercase tracking-[0.14em] text-ink-500">À découvrir</span>
                @foreach ($publicGroups as $g)
                    <form method="POST" action="{{ route('groups.join', $g) }}" class="flex items-center gap-3 rounded-xl bg-white p-2.5 ring-1 ring-ink-100" x-show="!q || @js(mb_strtolower($g->name)).includes(q.toLowerCase())">@csrf
                        <x-avatar :initials="mb_strtoupper(mb_substr($g->name, 0, 2))" :size="40" tone="gold" square />
                        <span class="flex min-w-0 flex-1 flex-col"><span class="truncate text-sm font-bold">{{ $g->name }}</span><span class="text-xs text-ink-500">{{ $g->users_count }} membres · public</span></span>
                        <button class="rounded-lg bg-brand-50 px-3 py-2 text-[13px] font-bold text-brand-700 transition hover:bg-brand-600 hover:text-white">Rejoindre</button>
                    </form>
                @endforeach
            </section>
        @endif
    @endif

    @if ($filter !== 'groupes')
        <section class="flex flex-col gap-1">
            <span class="px-1 pb-1 text-[11px] font-bold uppercase tracking-[0.14em] text-ink-500">Messages privés</span>
            @forelse ($directs as $c)
                @include('app.chat._row', ['conversation' => $c, 'active' => $activeId === $c->id])
            @empty
                <p class="rounded-xl bg-ink-50 px-4 py-3.5 text-[13.5px] text-ink-600">Aucune conversation privée. Contactez un membre depuis son profil.</p>
            @endforelse
        </section>
    @endif
</div>
