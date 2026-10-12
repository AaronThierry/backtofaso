<x-layouts.app title="Mon compte" active="account">
    <div class="flex flex-col gap-6 lg:gap-8">
        <x-page-header title="Mon compte" subtitle="Votre profil et vos préférences." />
        <x-flash />

        <div class="grid gap-6 lg:grid-cols-[minmax(0,4fr)_minmax(0,7fr)] lg:items-start lg:gap-8">
            {{-- Carte profil --}}
            <div class="flex flex-col gap-4 lg:sticky lg:top-10">
                <div class="card overflow-hidden">
                    <div class="bg-hero relative h-24">
                        <div class="bg-weave absolute inset-0 opacity-60"></div>
                    </div>
                    <div class="-mt-12 flex flex-col items-center gap-3 px-5 pb-6 text-center">
                        <x-avatar :user="$user" :size="96" tone="gold" class="ring-4 ring-white shadow-lg" />
                        <div class="flex flex-col items-center gap-1.5">
                            <span class="font-display text-[22px] font-bold leading-tight text-navy-950">{{ $user->name }}</span>
                            <span class="inline-flex items-center rounded-full bg-brand-50 px-3 py-1 text-xs font-bold text-brand-700 ring-1 ring-brand-100">{{ $user->role_label }}</span>
                        </div>
                    </div>
                    <dl class="flex flex-col gap-0.5 border-t border-ink-100 p-2">
                        @foreach (array_filter([
                            ['phone', 'Téléphone', $phone],
                            $user->email ? ['mail', 'E-mail', $user->email] : null,
                            $user->city ? ['map', 'Ville', $user->city] : null,
                        ]) as [$icon, $label, $value])
                            <div class="flex items-center gap-3 rounded-xl px-3 py-2.5">
                                <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-ink-50 text-ink-600"><x-icon :name="$icon" :size="17" /></span>
                                <span class="flex min-w-0 flex-col">
                                    <dt class="text-xs text-ink-500">{{ $label }}</dt>
                                    <dd class="truncate text-sm font-semibold text-navy-950">{{ $value }}</dd>
                                </span>
                            </div>
                        @endforeach
                    </dl>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="hidden lg:block">@csrf
                    <button class="btn-secondary w-full text-err-700 hover:bg-err-50 hover:ring-err-700/20"><x-icon name="logout" /> Se déconnecter</button>
                </form>
            </div>

            {{-- Paramètres --}}
            <div class="flex flex-col gap-6">
                <section class="flex flex-col gap-3.5">
                    <h2 class="text-[20px] font-bold text-navy-950">Paramètres</h2>
                    <div class="card flex flex-col p-1.5">
                        @foreach (array_filter([
                            [route('onboarding.profile'), 'user', 'Mes informations', 'Ville, diplôme, centres d’intérêt'],
                            [route('onboarding.preferences'), 'bell', 'Notifications et photo', 'SMS, WhatsApp, e-mail'],
                            $user->talentProfile ? [route('talents.edit'), 'briefcase', 'Mon profil talent', 'Visible des recruteurs après vérification'] : null,
                            $user->needsDossier() ? [route('dossier'), 'folder', 'Mon dossier', 'Étapes et documents'] : null,
                        ]) as [$url, $icon, $t, $d])
                            <a href="{{ $url }}" class="group flex min-h-[68px] items-center gap-4 rounded-xl px-3.5 py-2.5 transition hover:bg-ink-50">
                                <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600 transition group-hover:bg-brand-600 group-hover:text-white"><x-icon :name="$icon" /></span>
                                <span class="flex flex-1 flex-col gap-0.5"><span class="text-[15px] font-bold text-navy-950">{{ $t }}</span><span class="text-[13px] text-ink-600">{{ $d }}</span></span>
                                <x-icon name="chevron" :size="18" class="text-ink-300 transition group-hover:translate-x-0.5 group-hover:text-brand-600" />
                            </a>
                        @endforeach
                    </div>
                </section>

                <section class="bg-hero relative flex flex-col gap-4 overflow-hidden rounded-2xl p-6 text-white sm:flex-row sm:items-center">
                    <div class="bg-weave pointer-events-none absolute inset-y-0 right-0 w-1/3 opacity-50 [mask-image:linear-gradient(to_left,black,transparent)]"></div>
                    <span class="glass relative flex size-12 shrink-0 items-center justify-center rounded-2xl text-gold-300"><x-icon name="help" :size="24" /></span>
                    <span class="relative flex flex-1 flex-col gap-1">
                        <span class="font-display text-lg font-bold">Une question sur votre compte ?</span>
                        <span class="text-[13.5px] text-white/70">L’équipe Travel Express répond dans la communauté.</span>
                    </span>
                    <a href="{{ route('chat.show', \App\Models\Conversation::superGroup()) }}" class="btn-light relative min-h-11 text-sm">Poser une question</a>
                </section>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="lg:hidden">@csrf
            <button class="btn-secondary w-full text-err-700"><x-icon name="logout" /> Se déconnecter</button>
        </form>
    </div>
</x-layouts.app>
