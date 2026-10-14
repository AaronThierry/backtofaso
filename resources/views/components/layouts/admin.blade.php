@props(['title' => null, 'active' => null])
@php
$user = auth()->user();
$isAdmin = $user->role === \App\Models\User::ROLE_ADMIN;
$badges = [
    'dossiers' => \App\Models\DossierDocument::where('status', \App\Models\DossierDocument::PENDING)->count(),
    'talents' => \App\Models\TalentProfile::whereNull('verified_at')->count(),
    'contacts' => \App\Models\ContactMessage::whereNull('handled_at')->count(),
];
$nav = array_values(array_filter([
    ['dashboard', 'Tableau de bord', 'home', route('admin.dashboard')],
    ['dossiers', 'Dossiers', 'folder', route('admin.dossiers.index')],
    ['talents', 'Talents', 'briefcase', route('admin.talents.index')],
    ['contacts', 'Messages de contact', 'mail', route('admin.contacts.index')],
    $isAdmin ? ['users', 'Utilisateurs', 'users', route('admin.users.index')] : null,
]));
@endphp
<x-layouts.base :title="($title ? $title.' · ' : '').'Administration'" body-class="bg-canvas">
    <div class="lg:flex">
        {{-- Menu latéral --}}
        <aside class="bg-hero sticky top-0 hidden h-dvh w-[264px] shrink-0 flex-col overflow-hidden text-white lg:flex">
            <div class="bg-weave pointer-events-none absolute inset-x-0 bottom-0 h-40 opacity-60 [mask-image:linear-gradient(to_top,black,transparent)]"></div>
            <a href="{{ route('admin.dashboard') }}" class="relative flex items-center gap-3 px-6 pb-2 pt-7">
                <x-logo :size="40" />
                <span class="flex flex-col leading-tight">
                    <span class="font-display text-[18px] font-bold">Back to Burkina</span>
                    <span class="text-xs text-white/60">Travel Express</span>
                </span>
            </a>
            <span class="relative mx-6 mb-6 mt-3 inline-flex items-center gap-1.5 self-start rounded-full bg-gold-400 px-2.5 py-1 text-[11px] font-extrabold uppercase tracking-[0.12em] text-navy-950"><x-icon name="shield" :size="12" /> Administration</span>

            <nav class="relative flex flex-col gap-1 px-3" aria-label="Administration">
                @foreach ($nav as [$key, $label, $icon, $url])
                    <a href="{{ $url }}" @class(['group relative flex min-h-11 items-center gap-3 rounded-xl px-3 text-[14.5px] transition', 'bg-white/[0.09] font-bold text-white' => $active === $key, 'font-semibold text-white/65 hover:bg-white/5 hover:text-white' => $active !== $key])>
                        @if ($active === $key)<span class="absolute -left-3 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r-full bg-gold-400"></span>@endif
                        <span @class(['flex size-8 items-center justify-center rounded-lg', 'bg-linear-to-br from-brand-500 to-brand-600 text-white shadow-glow' => $active === $key, 'text-white/70' => $active !== $key])><x-icon :name="$icon" :size="17" /></span>
                        <span class="flex-1">{{ $label }}</span>
                        @if ($badges[$key] ?? 0)
                            <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-gold-400 px-1.5 text-[11px] font-extrabold text-navy-950">{{ $badges[$key] }}</span>
                        @endif
                    </a>
                @endforeach
            </nav>

            <div class="relative mt-auto flex flex-col gap-1 p-3">
                <a href="{{ route('chat.show', \App\Models\Conversation::superGroup()) }}" class="flex min-h-10 items-center gap-3 rounded-xl px-3 text-sm font-semibold text-white/65 transition hover:bg-white/5 hover:text-white"><x-icon name="chat" :size="17" /> Communauté</a>
                <a href="{{ route('dashboard') }}" class="flex min-h-10 items-center gap-3 rounded-xl px-3 text-sm font-semibold text-white/65 transition hover:bg-white/5 hover:text-white"><x-icon name="back" :size="17" /> Mon espace</a>
                <div class="mt-2 flex items-center gap-3 rounded-2xl bg-white/5 p-2.5 ring-1 ring-white/10">
                    <x-avatar :user="$user" :size="36" tone="gold" />
                    <span class="flex min-w-0 flex-1 flex-col"><span class="truncate text-sm font-bold">{{ $user->name }}</span><span class="truncate text-xs text-white/55">{{ $user->role_label }}</span></span>
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button aria-label="Se déconnecter" title="Se déconnecter" class="flex size-9 items-center justify-center rounded-lg text-white/60 hover:bg-white/10 hover:text-white"><x-icon name="logout" :size="18" /></button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="min-w-0 flex-1">
            {{-- Mobile : en-tête et menu horizontal --}}
            <div class="bg-hero sticky top-0 z-30 text-white lg:hidden">
                <div class="flex items-center justify-between px-4 pb-2 pt-[calc(env(safe-area-inset-top)+12px)]">
                    <span class="flex items-center gap-2.5"><x-logo :size="34" /><span class="font-display font-bold">Administration</span></span>
                    <a href="{{ route('dashboard') }}" class="rounded-full bg-white/10 px-3 py-1.5 text-xs font-bold">Mon espace</a>
                </div>
                <nav class="flex gap-1.5 overflow-x-auto px-3 pb-3" style="scrollbar-width: none" aria-label="Administration">
                    @foreach ($nav as [$key, $label, $icon, $url])
                        <a href="{{ $url }}" @class(['inline-flex shrink-0 items-center gap-1.5 rounded-full px-3 py-1.5 text-[13px] font-semibold', 'bg-white text-navy-950' => $active === $key, 'bg-white/10 text-white/80' => $active !== $key])>
                            {{ $label }} @if ($badges[$key] ?? 0)<span class="rounded-full bg-gold-400 px-1.5 text-[10.5px] font-extrabold text-navy-950">{{ $badges[$key] }}</span>@endif
                        </a>
                    @endforeach
                </nav>
            </div>

            <div class="mx-auto w-full max-w-[1240px] px-4 py-6 sm:px-6 lg:px-10 lg:py-10">
                {{ $slot }}
            </div>
        </main>
    </div>
</x-layouts.base>
