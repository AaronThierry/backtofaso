@props(['title' => null, 'active' => null, 'bare' => false, 'width' => 'wide'])
{{--
    width : wide (pages de contenu) · narrow (formulaires) · full (pleine largeur sur ordinateur, ex. messagerie)
    bare  : aucun cadre ni barre d'onglets (conversation ouverte)
--}}
@php
$user = auth()->user();
$tabs = [
    ['dashboard', 'Accueil', 'home', route('dashboard')],
    ['dossier', 'Dossier', 'folder', route('dossier'), $user->needsDossier()],
    ['chat', 'Chat', 'chat', route('chat.index')],
    ['talents', 'Talents', 'users', route('talents.index')],
    ['account', 'Compte', 'user', route('account')],
];
$tabs = array_values(array_filter($tabs, fn ($t) => $t[4] ?? true));

$mobileFrame = 'max-w-3xl px-5 pb-[calc(env(safe-area-inset-bottom)+104px)] pt-[calc(env(safe-area-inset-top)+20px)] sm:px-8';
$frame = match (true) {
    $bare => '',
    $width === 'full' => $mobileFrame.' lg:max-w-none lg:p-0',
    $width === 'narrow' => $mobileFrame.' lg:px-10 lg:pb-12 lg:pt-10',
    default => $mobileFrame.' lg:max-w-6xl lg:px-10 lg:pb-12 lg:pt-10',
};
@endphp
<x-layouts.base :title="$title" body-class="bg-ink-50">
    <div class="lg:flex">
        {{-- Menu latéral (ordinateur) --}}
        <aside class="sticky top-0 hidden h-dvh w-64 shrink-0 flex-col gap-6 border-r border-ink-200 bg-white px-4 py-6 lg:flex xl:w-72">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 px-2">
                <x-logo :size="36" />
                <span class="flex flex-col leading-tight">
                    <span class="text-[17px] font-bold">Back to Burkina</span>
                    <span class="text-xs text-ink-600">par Travel Express</span>
                </span>
            </a>
            <nav class="flex flex-col gap-1" aria-label="Navigation principale">
                @foreach ($tabs as [$key, $label, $icon, $url])
                    <a href="{{ $url }}" @class(['flex min-h-11 items-center gap-3 rounded-xl px-3.5 text-[15px] transition', 'bg-brand-100 font-bold text-brand-700' => $active === $key, 'font-semibold text-ink-700 hover:bg-ink-50' => $active !== $key])>
                        <x-icon :name="$icon" /> {{ $label }}
                    </a>
                @endforeach
            </nav>
            <div class="mt-auto flex items-center gap-1 rounded-xl bg-ink-50 p-2">
                <a href="{{ route('account') }}" class="flex min-w-0 flex-1 items-center gap-3 rounded-lg p-1 hover:bg-white">
                    <x-avatar :user="$user" :size="38" />
                    <span class="flex min-w-0 flex-col">
                        <span class="truncate text-sm font-bold">{{ $user->name }}</span>
                        <span class="truncate text-xs text-ink-600">{{ $user->role_label }}</span>
                    </span>
                </a>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button aria-label="Se déconnecter" title="Se déconnecter" class="flex size-10 items-center justify-center rounded-lg text-ink-600 hover:bg-white hover:text-ink-900"><x-icon name="logout" /></button>
                </form>
            </div>
        </aside>

        {{-- Contenu --}}
        <div class="min-w-0 flex-1">
            <div @class(['mx-auto w-full', $frame])>
                {{ $slot }}
            </div>
        </div>
    </div>

    {{-- Barre d'onglets (mobile / app) --}}
    @unless ($bare)
        <nav class="fixed inset-x-0 bottom-0 z-30 flex justify-around border-t border-ink-200 bg-white/95 px-2 pb-[calc(env(safe-area-inset-bottom)+8px)] pt-2 backdrop-blur lg:hidden" aria-label="Navigation principale">
            @foreach ($tabs as [$key, $label, $icon, $url])
                <a href="{{ $url }}" @class(['flex min-h-12 min-w-15 flex-col items-center gap-1 pt-1 text-[11px]', 'font-bold text-brand-600' => $active === $key, 'font-semibold text-ink-600' => $active !== $key])>
                    <x-icon :name="$icon" :size="24" /> {{ $label }}
                </a>
            @endforeach
        </nav>
    @endunless
</x-layouts.base>
