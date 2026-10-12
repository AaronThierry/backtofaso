<x-layouts.app title="Messages" active="chat" width="full">
    <div class="lg:grid lg:h-dvh lg:grid-cols-[minmax(320px,390px)_minmax(0,1fr)]">
        <div class="scrollbar-thin lg:overflow-y-auto lg:border-r lg:border-ink-200/70 lg:bg-white lg:p-5">
            @include('app.chat._list', ['activeId' => null])
        </div>

        {{-- Ordinateur : aucune conversation ouverte --}}
        <div class="bg-dots relative hidden flex-col items-center justify-center gap-4 p-10 text-center lg:flex">
            <span class="relative flex size-24 items-center justify-center">
                <span class="absolute inset-0 rotate-6 rounded-[28px] bg-brand-100"></span>
                <span class="relative flex size-24 items-center justify-center rounded-[28px] bg-linear-to-br from-brand-500 to-brand-600 text-white shadow-glow"><x-icon name="chat" :size="40" /></span>
            </span>
            <h2 class="mt-2 text-2xl font-bold text-navy-950">Vos conversations</h2>
            <p class="max-w-sm text-[15px] leading-relaxed text-ink-600">Choisissez une conversation à gauche, ou ouvrez la communauté pour suivre les annonces, l’entraide, les emplois et les bourses.</p>
            <div class="mt-2 flex gap-2.5">
                @if ($super)
                    <a href="{{ route('chat.show', $super) }}" class="btn-primary">Ouvrir la communauté</a>
                @endif
                <a href="{{ route('groups.create') }}" class="btn-secondary">Créer un groupe</a>
            </div>
        </div>
    </div>
</x-layouts.app>
