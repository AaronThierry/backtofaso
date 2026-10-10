<x-layouts.base title="Bienvenue" body-class="bg-brand-600">
    <div class="mx-auto flex min-h-dvh max-w-xl flex-col bg-brand-600 text-white" x-data="{ role: '{{ array_key_first($roles) }}' }">
        <div class="flex flex-col gap-4 px-6 pb-7 pt-[calc(env(safe-area-inset-top)+56px)]">
            <x-logo :size="56" invert />
            <h1 class="text-[40px] font-bold leading-none">Back to<br>Burkina</h1>
            <p class="max-w-xs text-[15px] leading-relaxed">Les compétences acquises à l’étranger, réinvesties au pays. Dites-nous qui vous êtes.</p>
        </div>

        <form method="GET" action="{{ route('register') }}" class="flex flex-1 flex-col gap-2.5 rounded-t-2xl bg-ink-50 px-5 pb-[calc(env(safe-area-inset-bottom)+24px)] pt-6 text-ink-900">
            <span class="text-[13px] font-bold text-ink-600">Je suis…</span>
            @foreach ($roles as $key => [$title, $sub])
                <label class="flex min-h-[72px] cursor-pointer items-center gap-3.5 rounded-xl border-2 px-3.5 py-3 transition"
                       :class="role === '{{ $key }}' ? 'border-brand-600 bg-brand-100' : 'border-brand-100 bg-brand-50'">
                    <input type="radio" name="profil" value="{{ $key }}" x-model="role" class="sr-only">
                    <span class="flex size-11 shrink-0 items-center justify-center rounded-[10px] text-base font-bold"
                          :class="role === '{{ $key }}' ? 'bg-brand-600 text-white' : 'bg-white text-brand-700'">{{ $loop->iteration }}</span>
                    <span class="flex flex-col gap-0.5">
                        <span class="text-[15px] font-bold">{{ $title }}</span>
                        <span class="text-[12.5px] text-ink-600">{{ $sub }}</span>
                    </span>
                </label>
            @endforeach
            <button type="submit" class="btn-primary mt-auto min-h-[52px] text-base">Continuer</button>
            <span class="text-center text-[13px] text-ink-600">Déjà inscrit ? <a href="{{ route('login') }}" class="font-bold text-brand-600">Se connecter</a></span>
        </form>
    </div>
</x-layouts.base>
