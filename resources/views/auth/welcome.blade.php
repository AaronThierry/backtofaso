<x-layouts.base title="Bienvenue" body-class="bg-navy-950 lg:bg-canvas">
    <div class="flex min-h-dvh lg:items-center lg:justify-center lg:p-10">
        <div class="mx-auto flex min-h-dvh w-full max-w-xl flex-col lg:grid lg:min-h-[640px] lg:max-w-[1080px] lg:grid-cols-[minmax(0,5fr)_minmax(0,6fr)] lg:overflow-hidden lg:rounded-[32px] lg:shadow-[0_40px_80px_-30px_rgb(6_18_44/0.45)]"
             x-data="{ role: '{{ array_key_first($roles) }}' }">
            {{-- Présentation --}}
            <div class="bg-hero relative flex flex-col gap-5 overflow-hidden px-6 pb-10 pt-[calc(env(safe-area-inset-top)+40px)] text-white lg:gap-7 lg:p-12">
                <div class="bg-weave pointer-events-none absolute inset-y-0 right-0 w-20 opacity-70 [mask-image:linear-gradient(to_left,black,transparent)]"></div>
                <a href="{{ route('home') }}" class="relative self-start" aria-label="Accueil"><x-logo :size="52" /></a>
                <div class="relative flex flex-col gap-3">
                    <span class="eyebrow text-gold-300">Bienvenue</span>
                    <h1 class="text-[42px] font-extrabold leading-[0.98] lg:text-[54px]">Back to<br><span class="bg-linear-to-r from-gold-200 to-gold-400 bg-clip-text text-transparent">Burkina</span></h1>
                    <p class="max-w-sm text-[15.5px] leading-relaxed text-white/70">Les compétences acquises à l’étranger, réinvesties au pays. Dites-nous qui vous êtes, nous adaptons votre espace.</p>
                </div>
                <ul class="relative mt-auto hidden flex-col gap-3.5 pt-6 text-[14.5px] lg:flex">
                    @foreach ([['folder', 'Suivi du dossier en temps réel'], ['chat', 'Un conseiller qui vous répond'], ['users', 'La communauté Back to Burkina']] as [$icon, $item])
                        <li class="flex items-center gap-3"><span class="glass flex size-9 items-center justify-center rounded-xl text-gold-300"><x-icon :name="$icon" :size="17" /></span>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>

            {{-- Choix du profil --}}
            <form method="GET" action="{{ route('register') }}" class="relative -mt-5 flex flex-1 flex-col gap-3 rounded-t-[28px] bg-canvas px-5 pb-[calc(env(safe-area-inset-bottom)+24px)] pt-7 text-ink-900 lg:mt-0 lg:justify-center lg:rounded-none lg:bg-white lg:p-12">
                @if (filter_var(request('email'), FILTER_VALIDATE_EMAIL))
                    {{-- Inscription depuis une invitation : l'adresse suit jusqu'au formulaire --}}
                    <input type="hidden" name="email" value="{{ request('email') }}">
                @endif
                <div class="flex flex-col gap-1 pb-2">
                    <h2 class="text-[24px] font-bold text-navy-950 lg:text-[28px]">Je suis…</h2>
                    <p class="text-sm text-ink-600">Choisissez le profil qui vous correspond.</p>
                </div>
                @foreach ($roles as $key => [$title, $sub])
                    <label class="group relative flex min-h-[76px] cursor-pointer items-center gap-4 rounded-2xl bg-white px-4 py-3.5 ring-1 transition duration-200"
                           :class="role === '{{ $key }}' ? 'ring-2 ring-brand-500 shadow-lift' : 'ring-ink-200 shadow-card hover:ring-brand-300 hover:-translate-y-px'">
                        <input type="radio" name="profil" value="{{ $key }}" x-model="role" class="sr-only">
                        <span class="flex size-12 shrink-0 items-center justify-center rounded-xl transition"
                              :class="role === '{{ $key }}' ? 'bg-linear-to-br from-brand-500 to-brand-600 text-white shadow-glow' : 'bg-brand-50 text-brand-600'">
                            <x-icon :name="\App\Models\User::ROLE_ICONS[$key] ?? 'user'" :size="22" />
                        </span>
                        <span class="flex min-w-0 flex-1 flex-col gap-0.5">
                            <span class="text-[15.5px] font-bold text-navy-950">{{ $title }}</span>
                            <span class="text-[13px] text-ink-600">{{ $sub }}</span>
                        </span>
                        <span class="flex size-6 shrink-0 items-center justify-center rounded-full ring-2 transition"
                              :class="role === '{{ $key }}' ? 'bg-brand-600 ring-brand-600 text-white' : 'ring-ink-200 text-transparent'">
                            <x-icon name="check" :size="13" stroke-width="3.2" />
                        </span>
                    </label>
                @endforeach
                <button type="submit" class="btn-primary mt-auto min-h-[54px] text-base lg:mt-4">Continuer <x-icon name="arrow" :size="18" /></button>
                <span class="text-center text-[13.5px] text-ink-600">Déjà inscrit ? <a href="{{ route('login') }}" class="font-bold text-brand-600 hover:underline">Se connecter</a></span>
            </form>
        </div>
    </div>
</x-layouts.base>
