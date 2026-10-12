@props(['title' => null])
{{-- Écrans d'inscription / connexion : plein écran sur mobile, panneau + formulaire sur ordinateur --}}
<x-layouts.base :title="$title">
    <div class="flex min-h-dvh">
        <aside class="bg-hero relative hidden w-[44%] max-w-[640px] flex-col overflow-hidden p-12 text-white lg:flex xl:p-16">
            <div class="bg-weave pointer-events-none absolute inset-y-0 right-0 w-24 opacity-70 [mask-image:linear-gradient(to_left,black,transparent)]"></div>

            <a href="{{ route('home') }}" class="relative flex items-center gap-3">
                <x-logo :size="44" />
                <span class="flex flex-col leading-tight">
                    <span class="font-display text-xl font-bold">Back to Burkina</span>
                    <span class="text-[13px] text-white/60">par Travel Express</span>
                </span>
            </a>

            <div class="relative my-auto flex flex-col gap-10 py-12">
                <div class="flex flex-col gap-4">
                    <span class="eyebrow text-gold-300">Du départ au retour</span>
                    <h2 class="text-[44px] font-extrabold leading-[1.02] xl:text-[52px]">Un compte pour tout<br>votre <span class="bg-linear-to-r from-gold-200 to-gold-400 bg-clip-text text-transparent">parcours</span>.</h2>
                    <p class="max-w-md text-[16px] leading-relaxed text-white/70">De la candidature au retour à l’emploi, tout se passe au même endroit, sur le web comme sur l’application.</p>
                </div>

                {{-- Aperçu de l'espace membre --}}
                <div class="relative max-w-md">
                    <div class="glass flex animate-float flex-col gap-4 rounded-3xl p-5 shadow-2xl shadow-navy-950/40">
                        <div class="flex items-center gap-4">
                            <x-progress-ring :percent="50" :size="64" :stroke="7" dark />
                            <span class="flex flex-col gap-1">
                                <span class="font-bold">Mon dossier</span>
                                <span class="text-[13px] text-white/65">Étape 3 sur 6 · Admission en cours</span>
                            </span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-[12px]">
                            @foreach (['Passeport', 'Diplôme', 'Relevés'] as $i => $doc)
                                <span class="flex items-center gap-1.5 rounded-xl bg-white/10 px-2.5 py-2 font-semibold">
                                    <span @class(['size-1.5 rounded-full', 'bg-ok-500' => $i < 2, 'bg-gold-400' => $i === 2])></span>{{ $doc }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <div class="glass absolute -bottom-7 -right-4 flex items-center gap-2.5 rounded-2xl px-3.5 py-2.5 text-[13px] shadow-xl shadow-navy-950/30 xl:-right-12">
                        <span class="flex size-7 items-center justify-center rounded-full bg-gold-400 text-navy-950"><x-icon name="chat" :size="15" stroke-width="2.2" /></span>
                        <span><b>Votre conseiller</b> vous a répondu</span>
                    </div>
                </div>
            </div>

            <ul class="relative flex flex-wrap gap-x-6 gap-y-2 text-[13px] text-white/70">
                @foreach (['Suivi en temps réel', 'Conseiller dédié', 'Communauté active'] as $item)
                    <li class="flex items-center gap-2"><x-icon name="check" :size="15" stroke-width="2.6" class="text-gold-300" />{{ $item }}</li>
                @endforeach
            </ul>
        </aside>

        <main class="bg-dots relative flex min-h-dvh flex-1 flex-col bg-white">
            <div class="pointer-events-none absolute inset-x-0 top-0 h-56 bg-linear-to-b from-brand-50/80 to-transparent lg:hidden"></div>
            <div class="relative mx-auto flex w-full max-w-[460px] flex-1 flex-col px-5 pb-[calc(env(safe-area-inset-bottom)+24px)] pt-[calc(env(safe-area-inset-top)+20px)] sm:px-8 lg:max-w-[480px] lg:justify-center lg:py-14">
                <div class="flex animate-rise flex-1 flex-col lg:flex-none">
                    {{ $slot }}
                </div>
            </div>
            <p class="relative hidden pb-6 text-center text-xs text-ink-500 lg:block">© {{ date('Y') }} Travel Express · Bobo-Dioulasso</p>
        </main>
    </div>
</x-layouts.base>
