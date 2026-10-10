<x-layouts.app title="Mon compte" active="account">
    <div class="flex flex-col gap-5">
        <h1 class="text-[26px] font-bold">Mon compte</h1>
        <div class="card flex items-center gap-4 p-4">
            <x-avatar :user="$user" :size="64" />
            <div class="flex flex-col gap-0.5">
                <span class="text-lg font-bold">{{ $user->name }}</span>
                <span class="text-sm text-ink-600">{{ $user->role_label }}</span>
                <span class="text-sm text-ink-600">{{ $phone }}</span>
            </div>
        </div>

        <div class="card divide-y divide-brand-100">
            @foreach (array_filter([
                [route('onboarding.profile'), 'Mes informations', 'Ville, diplôme, centres d’intérêt'],
                [route('onboarding.preferences'), 'Notifications et photo', 'SMS, WhatsApp, e-mail'],
                $user->talentProfile ? [route('talents.edit'), 'Mon profil talent', 'Visible des recruteurs après vérification'] : null,
                $user->needsDossier() ? [route('dossier'), 'Mon dossier', 'Étapes et documents'] : null,
            ]) as [$url, $t, $d])
                <a href="{{ $url }}" class="flex min-h-[60px] items-center gap-3 px-4 py-2">
                    <span class="flex flex-1 flex-col"><span class="text-[15px] font-semibold">{{ $t }}</span><span class="text-[12.5px] text-ink-600">{{ $d }}</span></span>
                    <x-icon name="chevron" :size="18" class="text-ink-600" />
                </a>
            @endforeach
        </div>

        <form method="POST" action="{{ route('logout') }}">@csrf
            <button class="btn-secondary w-full"><x-icon name="logout" /> Se déconnecter</button>
        </form>
    </div>
</x-layouts.app>
