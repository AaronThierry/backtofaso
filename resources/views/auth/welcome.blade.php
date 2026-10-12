<x-layouts.base title="Bienvenue" body-class="bg-brand-600 lg:bg-ink-50">
    <div class="flex min-h-dvh lg:items-center lg:justify-center lg:p-10">
        <div class="mx-auto flex min-h-dvh w-full max-w-xl flex-col bg-brand-600 text-white lg:grid lg:min-h-0 lg:max-w-5xl lg:grid-cols-[minmax(0,5fr)_minmax(0,6fr)] lg:overflow-hidden lg:rounded-3xl lg:shadow-xl"
             x-data="{ role: '{{ array_key_first($roles) }}' }">
            <div class="flex flex-col gap-4 px-6 pb-7 pt-[calc(env(safe-area-inset-top)+56px)] lg:gap-6 lg:p-12">
                <a href="{{ route('home') }}" class="self-start" aria-label="Accueil"><x-logo :size="56" invert /></a>
                <h1 class="text-[40px] font-bold leading-none lg:text-5xl">Back to<br>Burkina</h1>
                <p class="max-w-xs text-[15px] leading-relaxed lg:max-w-sm lg:text-base">Les compétences acquises à l’étranger, réinvesties au pays. Dites-nous qui vous êtes.</p>
                <ul class="mt-auto hidden flex-col gap-3 pt-6 text-sm lg:flex">
                    @foreach (['Suivi du dossier en temps réel', 'Un conseiller dédié', 'La communauté Back to Burkina'] as $item)
                        <li class="flex items-center gap-2.5"><span class="flex size-6 items-center justify-center rounded-full bg-white text-brand-600"><x-icon name="check" :size="13" stroke-width="3" /></span>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>

            <form method="GET" action="{{ route('register') }}" class="flex flex-1 flex-col gap-2.5 rounded-t-2xl bg-ink-50 px-5 pb-[calc(env(safe-area-inset-bottom)+24px)] pt-6 text-ink-900 lg:rounded-none lg:bg-white lg:p-12">
                <span class="text-[13px] font-bold text-ink-600 lg:text-[15px]">Je suis…</span>
                @foreach ($roles as $key => [$title, $sub])
                    <label class="flex min-h-[72px] cursor-pointer items-center gap-3.5 rounded-xl border-2 px-3.5 py-3 transition"
                           :class="role === '{{ $key }}' ? 'border-brand-600 bg-brand-100' : 'border-brand-100 bg-brand-50 hover:border-brand-300'">
                        <input type="radio" name="profil" value="{{ $key }}" x-model="role" class="sr-only">
                        <span class="flex size-11 shrink-0 items-center justify-center rounded-[10px] text-base font-bold"
                              :class="role === '{{ $key }}' ? 'bg-brand-600 text-white' : 'bg-white text-brand-700'">{{ $loop->iteration }}</span>
                        <span class="flex flex-col gap-0.5">
                            <span class="text-[15px] font-bold">{{ $title }}</span>
                            <span class="text-[12.5px] text-ink-600">{{ $sub }}</span>
                        </span>
                    </label>
                @endforeach
                <button type="submit" class="btn-primary mt-auto min-h-[52px] text-base lg:mt-4">Continuer</button>
                <span class="text-center text-[13px] text-ink-600">Déjà inscrit ? <a href="{{ route('login') }}" class="font-bold text-brand-600">Se connecter</a></span>
            </form>
        </div>
    </div>
</x-layouts.base>
