<x-layouts.app title="Mon compte" active="account">
    <div class="flex flex-col gap-5 lg:gap-7">
        <h1 class="text-[26px] font-bold lg:text-[28px]">Mon compte</h1>
        <x-flash />

        <div class="grid gap-5 lg:grid-cols-[minmax(0,4fr)_minmax(0,7fr)] lg:items-start lg:gap-8">
            <div class="flex flex-col gap-5">
                <div class="card flex items-center gap-4 p-4 lg:flex-col lg:gap-3 lg:p-8 lg:text-center">
                    <span class="flex lg:hidden"><x-avatar :user="$user" :size="64" /></span>
                    <span class="hidden lg:flex"><x-avatar :user="$user" :size="96" /></span>
                    <div class="flex flex-col gap-0.5">
                        <span class="text-lg font-bold lg:text-xl">{{ $user->name }}</span>
                        <span class="text-sm text-ink-600">{{ $user->role_label }}</span>
                        <span class="text-sm text-ink-600">{{ $phone }}</span>
                        @if ($user->email)
                            <span class="hidden text-sm text-ink-600 lg:block">{{ $user->email }}</span>
                        @endif
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="hidden lg:block">@csrf
                    <button class="btn-secondary w-full"><x-icon name="logout" /> Se déconnecter</button>
                </form>
            </div>

            <section class="flex flex-col gap-2.5">
                <h2 class="hidden text-[19px] font-semibold lg:block">Paramètres</h2>
                <div class="card divide-y divide-brand-100 lg:divide-ink-100 lg:border-ink-200 lg:bg-white">
                    @foreach (array_filter([
                        [route('onboarding.profile'), 'user', 'Mes informations', 'Ville, diplôme, centres d’intérêt'],
                        [route('onboarding.preferences'), 'bell', 'Notifications et photo', 'SMS, WhatsApp, e-mail'],
                        $user->talentProfile ? [route('talents.edit'), 'users', 'Mon profil talent', 'Visible des recruteurs après vérification'] : null,
                        $user->needsDossier() ? [route('dossier'), 'folder', 'Mon dossier', 'Étapes et documents'] : null,
                    ]) as [$url, $icon, $t, $d])
                        <a href="{{ $url }}" class="flex min-h-[60px] items-center gap-3 px-4 py-2 transition hover:bg-ink-50 lg:min-h-[72px] lg:px-5">
                            <span class="hidden size-10 shrink-0 items-center justify-center rounded-[10px] bg-brand-50 text-brand-600 lg:flex"><x-icon :name="$icon" /></span>
                            <span class="flex flex-1 flex-col"><span class="text-[15px] font-semibold">{{ $t }}</span><span class="text-[12.5px] text-ink-600">{{ $d }}</span></span>
                            <x-icon name="chevron" :size="18" class="text-ink-600" />
                        </a>
                    @endforeach
                </div>
            </section>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="lg:hidden">@csrf
            <button class="btn-secondary w-full"><x-icon name="logout" /> Se déconnecter</button>
        </form>
    </div>
</x-layouts.app>
