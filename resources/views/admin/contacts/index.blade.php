<x-layouts.admin title="Messages de contact" active="contacts">
    <div class="flex flex-col gap-6">
        <x-page-header eyebrow="Site public" title="Messages de contact" subtitle="Les messages envoyés depuis le formulaire de contact du site." />
        <x-flash />

        <div class="grid grid-cols-2 gap-1 self-start rounded-xl bg-ink-100/80 p-1">
            @foreach (['a-traiter' => 'À traiter', 'traites' => 'Traités'] as $key => $label)
                <a href="{{ route('admin.contacts.index', ['filtre' => $key]) }}" @class(['flex min-h-9 items-center justify-center gap-1.5 rounded-lg px-4 text-[13px] transition', 'bg-white font-bold text-navy-950 shadow-sm' => $filter === $key, 'font-semibold text-ink-600 hover:text-ink-900' => $filter !== $key])>{{ $label }} <span class="text-[11px] text-ink-400">{{ $counts[$key] }}</span></a>
            @endforeach
        </div>

        <div class="flex flex-col gap-4">
            @forelse ($messages as $m)
                <article id="message-{{ $m->id }}" class="card flex scroll-mt-6 flex-col">
                    <header class="flex flex-wrap items-start gap-3 border-b border-ink-100 p-5">
                        <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-brand-50 font-bold text-brand-700">{{ mb_strtoupper(mb_substr($m->name, 0, 1)) }}</span>
                        <span class="flex min-w-0 flex-1 flex-col gap-0.5">
                            <span class="flex flex-wrap items-center gap-2"><span class="font-display text-[16px] font-bold text-navy-950">{{ $m->name }}</span><span class="rounded-full bg-brand-50 px-2.5 py-0.5 text-[11.5px] font-bold text-brand-700">{{ $m->subjectLabel() }}</span></span>
                            <span class="flex flex-wrap gap-x-4 gap-y-0.5 text-[13px] text-ink-500">
                                <a href="mailto:{{ $m->email }}" class="hover:text-brand-600">{{ $m->email }}</a>
                                @if ($m->phone)<a href="tel:{{ str_replace(' ', '', $m->phone) }}" class="hover:text-brand-600">{{ $m->phone }}</a>@endif
                                <span>{{ $m->created_at->translatedFormat('j F Y à H:i') }}</span>
                            </span>
                        </span>
                        @if ($m->handled_at)<span class="badge-ok">Traité {{ $m->handled_at->diffForHumans() }}</span>@endif
                    </header>
                    <p class="whitespace-pre-line p-5 text-[15px] leading-relaxed text-ink-800">{{ $m->message }}</p>
                    <div class="flex flex-wrap gap-2 border-t border-ink-100 bg-ink-50/40 px-5 py-3.5">
                        <a href="mailto:{{ $m->email }}?subject={{ rawurlencode('Re : '.$m->subjectLabel().' · Back to Burkina') }}" class="btn-primary min-h-10 px-4 text-sm"><x-icon name="mail" :size="16" /> Répondre par e-mail</a>
                        <form method="POST" action="{{ route('admin.contacts.toggle', $m) }}">@csrf
                            <button class="btn-secondary min-h-10 px-4 text-sm">{{ $m->handled_at ? 'Remettre à traiter' : 'Marquer comme traité' }}</button>
                        </form>
                    </div>
                </article>
            @empty
                <x-empty icon="mail" title="Aucun message" :text="$filter === 'a-traiter' ? 'Tous les messages ont été traités.' : 'Aucun message traité pour le moment.'" />
            @endforelse
        </div>

        {{ $messages->links() }}
    </div>
</x-layouts.admin>
