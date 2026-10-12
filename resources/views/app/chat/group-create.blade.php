<x-layouts.app title="Nouveau groupe" active="chat" width="narrow">
    <form method="POST" action="{{ route('groups.store') }}" enctype="multipart/form-data" class="flex flex-col gap-6"
          x-data="{ isPublic: '{{ old('is_public', '0') }}', members: @js(array_map('intval', old('members', []))), preview: null, name: @js(old('name', '')), q: '', saving: false }"
          @submit="saving = true">
        @csrf
        <x-page-header title="Nouveau groupe" :back="route('chat.index')" eyebrow="Messages" subtitle="Réunissez des membres par ville, par promo ou autour d’un projet." />

        @if ($errors->any())
            <div class="flex items-center gap-3 rounded-2xl bg-err-50 px-4 py-3.5 text-sm font-semibold text-err-700 ring-1 ring-err-700/15"><x-icon name="help" :size="18" /> {{ $errors->first() }}</div>
        @endif

        {{-- Identité du groupe --}}
        <section class="card flex flex-col gap-5 p-5 sm:p-6">
            <div class="flex items-center gap-4">
                <label class="group relative flex size-20 shrink-0 cursor-pointer items-center justify-center overflow-hidden rounded-[26px] bg-linear-to-br from-brand-50 to-brand-100 text-brand-600 ring-1 ring-brand-100 transition hover:ring-brand-300" aria-label="Photo du groupe">
                    <img x-show="preview" x-cloak :src="preview" alt="" class="size-full object-cover">
                    <span x-show="!preview" class="font-display text-2xl font-bold" x-text="name.trim() ? name.trim().slice(0, 2).toUpperCase() : ''"></span>
                    <x-icon name="camera" :size="26" x-show="!preview && !name.trim()" />
                    <span class="absolute bottom-1 right-1 flex size-7 items-center justify-center rounded-full bg-white text-ink-700 shadow-md ring-1 ring-ink-100"><x-icon name="camera" :size="14" /></span>
                    <input type="file" name="avatar" accept="image/*" class="sr-only" @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                </label>
                <label class="flex flex-1 flex-col gap-1.5"><span class="field-label">Nom du groupe</span>
                    <input name="name" x-model="name" required maxlength="80" placeholder="Étudiants à Wuhan" class="field"></label>
            </div>

            <fieldset class="flex flex-col gap-2.5">
                <legend class="field-label mb-2.5">Qui peut rejoindre ?</legend>
                <div class="grid gap-2.5 sm:grid-cols-2">
                    @foreach ([['1', 'globe', 'Public', 'Tout membre peut le trouver et le rejoindre'], ['0', 'lock', 'Privé', 'Sur invitation uniquement']] as [$v, $icon, $t, $d])
                        <label class="flex cursor-pointer items-start gap-3 rounded-2xl p-4 ring-1 transition"
                               :class="isPublic === '{{ $v }}' ? 'bg-brand-50 ring-2 ring-brand-500' : 'bg-white ring-ink-200 hover:ring-brand-300'">
                            <input type="radio" name="is_public" value="{{ $v }}" x-model="isPublic" class="sr-only">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl transition" :class="isPublic === '{{ $v }}' ? 'bg-brand-600 text-white' : 'bg-ink-50 text-ink-600'"><x-icon :name="$icon" :size="19" /></span>
                            <span class="flex flex-col gap-0.5"><span class="text-[15px] font-bold text-navy-950">{{ $t }}</span><span class="text-[12.5px] leading-snug text-ink-600">{{ $d }}</span></span>
                        </label>
                    @endforeach
                </div>
            </fieldset>
        </section>

        {{-- Membres --}}
        <section class="card flex flex-col gap-4 p-5 sm:p-6">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-[17px] font-bold text-navy-950">Ajouter des membres</h2>
                <span class="rounded-full bg-brand-50 px-3 py-1 text-[12.5px] font-bold text-brand-700" x-text="members.length ? members.length + ' sélectionné' + (members.length > 1 ? 's' : '') : 'Facultatif'"></span>
            </div>
            <label class="field-group min-h-11 bg-ink-50/70">
                <x-icon name="search" :size="18" class="ml-3.5 text-ink-400" />
                <span class="sr-only">Rechercher un membre</span>
                <input x-model="q" type="search" placeholder="Rechercher un membre" class="min-w-0 flex-1 bg-transparent px-2.5 text-sm outline-none placeholder:text-ink-400">
            </label>
            <div class="scrollbar-thin -mx-2 grid max-h-[420px] gap-1 overflow-y-auto px-2 sm:grid-cols-2">
                @forelse ($people as $p)
                    <label class="flex min-h-[60px] cursor-pointer items-center gap-3 rounded-xl px-2.5 transition"
                           :class="members.includes({{ $p->id }}) ? 'bg-brand-50 ring-1 ring-brand-100' : 'hover:bg-ink-50'"
                           x-show="!q || @js(mb_strtolower($p->name.' '.$p->city)).includes(q.toLowerCase())">
                        <x-avatar :user="$p" :size="40" />
                        <span class="flex min-w-0 flex-1 flex-col"><span class="truncate text-[14px] font-bold text-navy-950">{{ $p->name }}</span><span class="truncate text-[12px] text-ink-500">{{ $p->role_label }}{{ $p->city ? ' · '.$p->city : '' }}</span></span>
                        <input type="checkbox" name="members[]" value="{{ $p->id }}" x-model.number="members" class="peer sr-only">
                        <span class="flex size-6 shrink-0 items-center justify-center rounded-full ring-2 transition peer-focus-visible:ring-4" :class="members.includes({{ $p->id }}) ? 'bg-brand-600 text-white ring-brand-600' : 'text-transparent ring-ink-200'">
                            <x-icon name="check" :size="13" stroke-width="3.2" />
                        </span>
                    </label>
                @empty
                    <p class="text-sm text-ink-600 sm:col-span-2">Aucun autre membre pour le moment.</p>
                @endforelse
            </div>
        </section>

        <div class="sticky bottom-[calc(env(safe-area-inset-bottom)+92px)] z-20 flex items-center justify-end gap-2.5 rounded-2xl bg-white/90 p-2.5 shadow-lift ring-1 ring-ink-900/[0.06] backdrop-blur-lg lg:bottom-6">
            <a href="{{ route('chat.index') }}" class="btn-ghost min-h-12 text-ink-700">Annuler</a>
            <button class="btn-primary min-h-12 flex-1 sm:flex-none sm:px-8" :disabled="saving || !name.trim()">
                <span x-show="!saving" class="flex items-center gap-2"><x-icon name="plus" :size="18" stroke-width="2.4" /> Créer le groupe</span>
                <span x-show="saving" x-cloak class="flex items-center gap-2"><span class="size-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span> Création…</span>
            </button>
        </div>
    </form>
</x-layouts.app>
