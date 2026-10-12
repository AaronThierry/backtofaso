<x-layouts.app title="Chat" active="chat" width="full">
    <div class="lg:grid lg:h-dvh lg:grid-cols-[minmax(320px,400px)_minmax(0,1fr)]">
        <div class="flex flex-col gap-4 lg:overflow-y-auto lg:border-r lg:border-ink-200 lg:bg-white lg:p-6">
            @include('app.chat._list', ['activeId' => null])
        </div>

        {{-- Ordinateur : aucune conversation ouverte --}}
        <div class="hidden flex-col items-center justify-center gap-3 p-10 text-center lg:flex">
            <span class="flex size-16 items-center justify-center rounded-full bg-brand-100 text-brand-600"><x-icon name="chat" :size="30" /></span>
            <h2 class="text-xl font-bold">Vos conversations</h2>
            <p class="max-w-sm text-[15px] text-ink-600">Choisissez une conversation à gauche, ou ouvrez la communauté pour suivre les annonces et l’entraide.</p>
            @if ($super)
                <a href="{{ route('chat.show', $super) }}" class="btn-primary mt-2">Ouvrir la communauté</a>
            @endif
        </div>
    </div>
</x-layouts.app>
