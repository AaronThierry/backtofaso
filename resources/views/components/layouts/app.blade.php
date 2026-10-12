@props(['title' => null, 'active' => null, 'bare' => false, 'width' => 'wide'])
{{--
    width : wide (pages de contenu) · narrow (formulaires) · full (pleine largeur sur ordinateur, ex. messagerie)
    bare  : aucun cadre ni barre d'onglets (conversation ouverte)
--}}
@php
$user = auth()->user();
$unread = $user->unreadMessagesCount();
$tabs = [
    ['dashboard', 'Accueil', 'home', route('dashboard')],
    ['dossier', 'Mon dossier', 'folder', route('dossier'), $user->needsDossier()],
    ['chat', 'Messages', 'chat', route('chat.index')],
    ['talents', 'Talents', 'users', route('talents.index')],
    ['account', 'Mon compte', 'user', route('account')],
];
$tabs = array_values(array_filter($tabs, fn ($t) => $t[4] ?? true));

$mobileFrame = 'max-w-3xl px-5 pb-[calc(env(safe-area-inset-bottom)+112px)] pt-[calc(env(safe-area-inset-top)+20px)] sm:px-8';
$frame = match (true) {
    $bare => '',
    $width === 'full' => $mobileFrame.' lg:max-w-none lg:p-0',
    $width === 'narrow' => $mobileFrame.' lg:px-10 lg:pb-14 lg:pt-10',
    default => $mobileFrame.' lg:max-w-[1180px] lg:px-10 lg:pb-14 lg:pt-10',
};
@endphp
<x-layouts.base :title="$title" body-class="bg-canvas">
    <div class="lg:flex">
        {{-- Menu latéral (ordinateur) --}}
        <aside class="bg-hero sticky top-0 hidden h-dvh w-[264px] shrink-0 flex-col overflow-hidden text-white lg:flex xl:w-[280px]">
            <div class="bg-weave pointer-events-none absolute inset-x-0 bottom-0 h-40 opacity-60 [mask-image:linear-gradient(to_top,black,transparent)]"></div>

            <a href="{{ route('home') }}" class="relative flex items-center gap-3 px-6 pb-7 pt-7">
                <x-logo :size="40" />
                <span class="flex flex-col leading-tight">
                    <span class="font-display text-[18px] font-bold tracking-tight">Back to Burkina</span>
                    <span class="text-xs text-white/60">par Travel Express</span>
                </span>
            </a>

            <nav class="relative flex flex-col gap-1 px-3" aria-label="Navigation principale">
                <span class="px-3 pb-2 text-[11px] font-bold uppercase tracking-[0.16em] text-white/40">Mon espace</span>
                @foreach ($tabs as [$key, $label, $icon, $url])
                    <a href="{{ $url }}" @class([
                        'group relative flex min-h-11 items-center gap-3 rounded-xl px-3 text-[15px] transition',
                        'bg-white/[0.09] font-bold text-white shadow-[inset_0_1px_0_rgb(255_255_255/0.06)]' => $active === $key,
                        'font-semibold text-white/65 hover:bg-white/5 hover:text-white' => $active !== $key,
                    ])>
                        @if ($active === $key)
                            <span class="absolute -left-3 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r-full bg-gold-400"></span>
                        @endif
                        <span @class(['flex size-8 items-center justify-center rounded-lg transition', 'bg-linear-to-br from-brand-500 to-brand-600 text-white shadow-glow' => $active === $key, 'text-white/70 group-hover:text-white' => $active !== $key])>
                            <x-icon :name="$icon" :size="18" />
                        </span>
                        <span class="flex-1">{{ $label }}</span>
                        @if ($key === 'chat' && $unread)
                            <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-gold-400 px-1.5 text-[11px] font-extrabold text-navy-950">{{ $unread > 99 ? '99+' : $unread }}</span>
                        @endif
                    </a>
                @endforeach
            </nav>

            <div class="relative mt-auto flex flex-col gap-3 p-4">
                <a href="{{ route('chat.show', \App\Models\Conversation::superGroup()) }}" class="glass flex flex-col gap-1 rounded-2xl p-4 transition hover:bg-white/15">
                    <span class="flex items-center gap-2 text-sm font-bold"><x-icon name="help" :size="18" class="text-gold-300" /> Besoin d’aide ?</span>
                    <span class="text-[12.5px] leading-snug text-white/65">Posez votre question à la communauté ou à votre conseiller.</span>
                </a>
                <div class="flex items-center gap-1 rounded-2xl bg-white/5 p-1.5 ring-1 ring-white/10">
                    <a href="{{ route('account') }}" class="flex min-w-0 flex-1 items-center gap-3 rounded-xl p-1.5 transition hover:bg-white/5">
                        <x-avatar :user="$user" :size="38" tone="gold" />
                        <span class="flex min-w-0 flex-col">
                            <span class="truncate text-sm font-bold">{{ $user->name }}</span>
                            <span class="truncate text-xs text-white/55">{{ $user->role_label }}</span>
                        </span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button aria-label="Se déconnecter" title="Se déconnecter" class="flex size-10 items-center justify-center rounded-xl text-white/60 transition hover:bg-white/10 hover:text-white"><x-icon name="logout" :size="19" /></button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Contenu --}}
        <main class="min-w-0 flex-1">
            <div @class(['mx-auto w-full', $frame])>
                {{ $slot }}
            </div>
        </main>
    </div>

    {{-- Barre d'onglets flottante (mobile / app) --}}
    @unless ($bare)
        <nav class="fixed inset-x-3 bottom-[calc(env(safe-area-inset-bottom)+10px)] z-30 flex justify-around rounded-[22px] bg-white/90 p-1.5 shadow-lift ring-1 ring-ink-900/[0.06] backdrop-blur-xl lg:hidden" aria-label="Navigation principale">
            @foreach ($tabs as [$key, $label, $icon, $url])
                <a href="{{ $url }}" @class(['relative flex min-h-[54px] flex-1 flex-col items-center justify-center gap-0.5 rounded-2xl text-[10.5px] transition', 'bg-brand-50 font-bold text-brand-700' => $active === $key, 'font-semibold text-ink-500' => $active !== $key])>
                    <x-icon :name="$icon" :size="22" :stroke-width="$active === $key ? 2.2 : 1.8" />
                    {{ \Illuminate\Support\Str::of($label)->replace('Mon ', '')->ucfirst() }}
                    @if ($key === 'chat' && $unread)
                        <span class="absolute right-[22%] top-1.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-err-500 px-1 text-[9.5px] font-extrabold text-white ring-2 ring-white">{{ $unread > 9 ? '9+' : $unread }}</span>
                    @endif
                </a>
            @endforeach
        </nav>
    @endunless
</x-layouts.base>
