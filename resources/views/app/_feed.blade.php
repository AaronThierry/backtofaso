{{-- Derniers messages d'un thème de la communauté. Variables : $title, $icon, $messages, $topic, $super, $empty --}}
<section class="card flex flex-col">
    <header class="flex items-center justify-between gap-3 border-b border-ink-100 px-5 py-4">
        <h2 class="flex items-center gap-2.5 text-[16px] font-bold text-navy-950">
            <span class="flex size-8 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><x-icon :name="$icon" :size="16" /></span>
            {{ $title }}
        </h2>
        <a href="{{ route('chat.show', [$super, 'theme' => $topic]) }}" class="text-[13px] font-bold text-brand-600 hover:underline">Tout voir</a>
    </header>
    @forelse ($messages as $m)
        <a href="{{ route('chat.show', [$super, 'theme' => $topic]) }}" class="flex gap-3 border-b border-ink-100 px-5 py-4 transition last:border-0 hover:bg-ink-50/60">
            <x-avatar :user="$m->user" :size="36" :tone="$m->user->isStaff() ? 'gold' : 'brand'" />
            <span class="flex min-w-0 flex-1 flex-col gap-1">
                <span class="flex flex-wrap items-center gap-x-2 gap-y-0.5 text-[12.5px]">
                    <span class="font-bold text-navy-950">{{ $m->user->isStaff() ? 'Travel Express' : $m->user->name }}</span>
                    @if ($m->user->isStaff())<span class="badge-gold px-1.5 py-0 text-[10px]">Officiel</span>@endif
                    <span class="text-ink-400">{{ $m->created_at->diffForHumans() }}</span>
                </span>
                <span class="line-clamp-2 text-[14px] leading-snug text-ink-700">{{ $m->body ?? 'Fichier partagé : '.$m->attachment_name }}</span>
            </span>
        </a>
    @empty
        <p class="px-5 py-6 text-center text-sm text-ink-500">{{ $empty }}</p>
    @endforelse
</section>
