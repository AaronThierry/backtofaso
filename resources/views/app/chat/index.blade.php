<x-layouts.app title="Chat" active="chat">
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <h1 class="text-[26px] font-bold">Chat</h1>
            <a href="{{ route('groups.create') }}" class="btn-primary min-h-11 rounded-full px-3.5 text-sm"><x-icon name="plus" :size="18" stroke-width="2.2" /> Nouveau groupe</a>
        </div>

        <div class="flex gap-1 rounded-[10px] bg-ink-100 p-1" role="tablist">
            @foreach (['tous' => 'Tous', 'groupes' => 'Groupes', 'prives' => 'Privés'] as $key => $label)
                <a href="{{ route('chat.index', ['filtre' => $key]) }}" role="tab" aria-selected="{{ $filter === $key ? 'true' : 'false' }}"
                   @class(['flex min-h-[38px] flex-1 items-center justify-center rounded-lg text-sm', 'bg-white font-bold shadow-sm' => $filter === $key, 'font-semibold text-ink-600' => $filter !== $key])>{{ $label }}</a>
            @endforeach
        </div>

        @if ($super)
            @php($lastAnn = $super->messages()->where('topic', 'annonces')->with('user')->latest()->first())
            <a href="{{ route('chat.show', $super) }}" class="flex flex-col gap-2.5 rounded-xl bg-brand-600 p-4 text-white">
                <span class="flex items-center gap-3">
                    <x-logo :size="48" />
                    <span class="flex min-w-0 flex-1 flex-col gap-0.5">
                        <span class="text-[15.5px] font-bold">{{ $super->name }}</span>
                        <span class="text-[12.5px]">Super groupe · {{ $super->users->count() }} membres</span>
                    </span>
                    @if ($n = $super->unreadCountFor($user))
                        <span class="flex h-6 min-w-6 items-center justify-center rounded-full bg-gold-600 px-2 text-xs font-bold">{{ $n }}</span>
                    @endif
                </span>
                @if ($lastAnn)
                    <span class="rounded-[10px] bg-white/15 px-3 py-2.5 text-[13.5px] leading-snug"><b>{{ $lastAnn->user->name }} :</b> {{ \Illuminate\Support\Str::limit($lastAnn->body, 110) }}</span>
                @endif
            </a>
        @endif

        @if ($filter !== 'prives')
            <section class="flex flex-col">
                <span class="pb-1 text-xs font-bold text-ink-600">Mes groupes</span>
                @forelse ($groups as $conversation)
                    @include('app.chat._row')
                @empty
                    <p class="py-4 text-sm text-ink-600">Vous n’avez pas encore de groupe. <a href="{{ route('groups.create') }}" class="font-bold text-brand-600">Créez-en un</a>.</p>
                @endforelse
            </section>

            @if ($publicGroups->isNotEmpty())
                <section class="flex flex-col gap-2">
                    <span class="text-xs font-bold text-ink-600">Groupes publics à rejoindre</span>
                    @foreach ($publicGroups as $g)
                        <form method="POST" action="{{ route('groups.join', $g) }}" class="card flex items-center gap-3 p-3">@csrf
                            <span class="flex flex-1 flex-col"><span class="text-sm font-bold">{{ $g->name }}</span><span class="text-xs text-ink-600">{{ $g->users_count }} membres</span></span>
                            <button class="btn-secondary min-h-9 px-3 text-[13px]">Rejoindre</button>
                        </form>
                    @endforeach
                </section>
            @endif
        @endif

        @if ($filter !== 'groupes')
            <section class="flex flex-col">
                <span class="pb-1 text-xs font-bold text-ink-600">Messages privés</span>
                @forelse ($directs as $conversation)
                    @include('app.chat._row')
                @empty
                    <p class="py-4 text-sm text-ink-600">Aucune conversation privée pour l’instant.</p>
                @endforelse
            </section>
        @endif
    </div>
</x-layouts.app>
