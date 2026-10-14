{{-- Liste des conversations. Variables : $user, $filter, $super, $groups, $directs, $publicGroups, $activeId (conversation ouverte ou null) --}}
<div class="flex flex-col gap-4" x-data="newConversation(@js(route('chat.members')))">
    <div class="flex items-center justify-between gap-3">
        <h1 class="text-[26px] font-bold text-navy-950 lg:text-[28px]">Messages</h1>
        <div class="flex items-center gap-2">
            <button type="button" @click="open()" class="icon-btn size-10" aria-label="Nouveau message" title="Nouveau message"><x-icon name="edit" :size="18" /></button>
            <a href="{{ route('groups.create') }}" class="btn-primary min-h-10 rounded-full px-3.5 text-sm" title="Nouveau groupe"><x-icon name="plus" :size="18" stroke-width="2.4" /> Groupe</a>
        </div>
    </div>

    {{-- Nouveau message : choisir un membre --}}
    <template x-teleport="body">
        <div x-show="show" x-cloak @keydown.escape.window="show = false" class="fixed inset-0 z-[60] flex items-end justify-center p-0 sm:items-center sm:p-5">
            <div x-show="show" x-transition.opacity @click="show = false" class="absolute inset-0 bg-navy-950/45 backdrop-blur-[2px]"></div>
            <div x-show="show" x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="translate-y-6 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
                 class="relative flex max-h-[85dvh] w-full max-w-md flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl sm:rounded-3xl" role="dialog" aria-label="Nouveau message">
                <div class="flex items-center justify-between gap-3 border-b border-ink-100 px-5 py-4">
                    <span class="font-display text-lg font-bold text-navy-950">Nouveau message</span>
                    <button type="button" @click="show = false" class="flex size-9 items-center justify-center rounded-full text-ink-500 hover:bg-ink-50" aria-label="Fermer"><x-icon name="close" :size="18" /></button>
                </div>
                <div class="p-4 pb-2">
                    <label class="field-group min-h-11 bg-ink-50/70">
                        <x-icon name="search" :size="18" class="ml-3.5 text-ink-400" />
                        <span class="sr-only">Rechercher un membre</span>
                        <input x-ref="search" x-model="term" @input.debounce.250ms="search()" type="search" placeholder="Nom d’un membre…" class="min-w-0 flex-1 bg-transparent px-2.5 text-sm outline-none placeholder:text-ink-400">
                    </label>
                </div>
                <div class="scrollbar-thin flex-1 overflow-y-auto px-2 pb-3">
                    <template x-if="loading"><p class="px-3 py-6 text-center text-sm text-ink-500">Recherche…</p></template>
                    <template x-if="!loading && results.length === 0"><p class="px-3 py-6 text-center text-sm text-ink-500">Aucun membre trouvé.</p></template>
                    <template x-for="p in results" :key="p.id">
                        <form method="POST" :action="p.url">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <button class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left transition hover:bg-ink-50">
                                <template x-if="p.avatar"><img :src="p.avatar" alt="" class="size-10 rounded-full object-cover"></template>
                                <template x-if="!p.avatar"><span class="flex size-10 items-center justify-center rounded-full bg-linear-to-br from-brand-100 to-brand-200 text-xs font-bold text-brand-800" x-text="p.initials"></span></template>
                                <span class="flex min-w-0 flex-1 flex-col"><span class="truncate text-sm font-bold text-navy-950" x-text="p.name"></span><span class="truncate text-xs text-ink-500" x-text="p.role"></span></span>
                                <x-icon name="chevron" :size="16" class="text-ink-300" />
                            </button>
                        </form>
                    </template>
                </div>
            </div>
        </div>
    </template>

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
