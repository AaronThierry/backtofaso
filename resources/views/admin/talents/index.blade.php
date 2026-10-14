<x-layouts.admin title="Talents" active="talents">
    <div class="flex flex-col gap-6">
        <x-page-header eyebrow="Vivier" title="Vérification des talents" subtitle="Un profil vérifié devient visible des recruteurs (s’il est rendu public par le diplômé)." />
        <x-flash />

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="grid grid-cols-3 gap-1 rounded-xl bg-ink-100/80 p-1 sm:w-[420px]">
                @foreach (['en-attente' => 'En attente', 'verifies' => 'Vérifiés', 'tous' => 'Tous'] as $key => $label)
                    <a href="{{ route('admin.talents.index', array_filter(['filtre' => $key, 'q' => $q])) }}" @class(['flex min-h-9 items-center justify-center gap-1.5 rounded-lg text-[13px] transition', 'bg-white font-bold text-navy-950 shadow-sm' => $filter === $key, 'font-semibold text-ink-600 hover:text-ink-900' => $filter !== $key])>{{ $label }} <span class="text-[11px] text-ink-400">{{ $counts[$key] }}</span></a>
                @endforeach
            </div>
            <form method="GET" class="sm:w-80">
                <input type="hidden" name="filtre" value="{{ $filter }}">
                <label class="field-group min-h-11 bg-white"><x-icon name="search" :size="17" class="ml-3 text-ink-400" /><input name="q" value="{{ $q }}" placeholder="Nom, e-mail, titre…" class="min-w-0 flex-1 bg-transparent px-2.5 text-sm outline-none"></label>
            </form>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($profiles as $p)
                @php($completion = $p->completion())
                <article class="card flex flex-col overflow-hidden">
                    <div class="flex items-center gap-3.5 p-5">
                        <x-avatar :user="$p->user" :size="52" />
                        <span class="flex min-w-0 flex-1 flex-col">
                            <span class="truncate font-display text-[16px] font-bold text-navy-950">{{ $p->user->name }}</span>
                            <span class="truncate text-[13px] text-ink-600">{{ $p->customHeadline() ?? $p->field }}</span>
                            <span class="truncate text-[12px] text-ink-500">{{ $p->user->email }}</span>
                        </span>
                    </div>
                    <div class="flex flex-col gap-2 px-5 text-[13px] text-ink-600">
                        <span class="flex items-center gap-2"><x-icon name="globe" :size="15" class="text-ink-400" /> {{ $p->countryName() ? 'Formé(e) en '.$p->countryName() : 'Pays non précisé' }}{{ $p->graduation_year ? ' · '.$p->graduation_year : '' }}</span>
                        <span class="flex items-center gap-2"><x-icon name="graduation" :size="15" class="text-ink-400" /> {{ $p->school ?: 'École non précisée' }}</span>
                        <span class="flex items-center gap-2"><x-icon name="file" :size="15" class="text-ink-400" /> @if ($p->cv_path)<a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($p->cv_path) }}" target="_blank" rel="noopener" class="font-semibold text-brand-600 hover:underline">Voir le CV</a>@else Pas de CV @endif</span>
                        <span class="flex items-center gap-2.5 pt-1"><span class="h-1.5 flex-1 overflow-hidden rounded-full bg-ink-100"><span class="block h-full rounded-full bg-linear-to-r from-brand-500 to-gold-400" style="width: {{ $completion['percent'] }}%"></span></span><span class="text-xs font-bold text-ink-700">{{ $completion['percent'] }} %</span></span>
                        <span class="flex flex-wrap gap-1.5">
                            <span class="{{ $p->verified_at ? 'badge-ok' : 'badge-warn' }}">{{ $p->verified_at ? 'Vérifié' : 'En attente' }}</span>
                            <span class="{{ $p->is_public ? 'badge' : 'badge-err' }}">{{ $p->is_public ? 'Public' : 'Masqué par le diplômé' }}</span>
                        </span>
                    </div>
                    <div class="mt-auto flex gap-2 border-t border-ink-100 p-4 pt-4">
                        <a href="{{ route('talents.show', $p) }}" class="btn-secondary min-h-10 flex-1 px-3 text-sm"><x-icon name="eye" :size="16" /> Profil</a>
                        <form method="POST" action="{{ route('admin.talents.verify', $p) }}" class="flex-1">@csrf
                            <input type="hidden" name="verify" value="{{ $p->verified_at ? 0 : 1 }}">
                            @if ($p->verified_at)
                                <button class="btn min-h-10 w-full bg-ink-50 px-3 text-sm text-ink-700 hover:bg-err-50 hover:text-err-700">Retirer</button>
                            @else
                                <button class="btn min-h-10 w-full bg-ok-500 px-3 text-sm text-white hover:brightness-95"><x-icon name="check" :size="16" stroke-width="2.6" /> Vérifier</button>
                            @endif
                        </form>
                    </div>
                </article>
            @empty
                <x-empty icon="briefcase" title="Aucun profil" text="Aucun profil talent dans cette catégorie." class="md:col-span-2 xl:col-span-3" />
            @endforelse
        </div>

        {{ $profiles->links() }}
    </div>
</x-layouts.admin>
