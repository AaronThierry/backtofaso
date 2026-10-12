<x-layouts.app title="Nouveau groupe" active="chat" width="narrow">
    <form method="POST" action="{{ route('groups.store') }}" enctype="multipart/form-data" class="flex flex-col gap-[18px] lg:rounded-2xl lg:border lg:border-ink-200 lg:bg-white lg:p-8"
          x-data="{ isPublic: '{{ old('is_public', '0') }}', members: @js(array_map('intval', old('members', []))), preview: null }">
        @csrf
        <div class="flex items-center gap-3">
            <a href="{{ route('chat.index') }}" aria-label="Retour" class="flex size-11 items-center justify-center rounded-full border border-ink-200 bg-white"><x-icon name="back" /></a>
            <h1 class="text-2xl font-bold">Nouveau groupe</h1>
        </div>
        @if ($errors->any())
            <div class="rounded-[10px] bg-err-50 px-4 py-3 text-sm font-semibold text-err-700">{{ $errors->first() }}</div>
        @endif

        <div class="flex items-center gap-3.5">
            <label class="flex size-16 shrink-0 cursor-pointer items-center justify-center overflow-hidden rounded-[20px] border-2 border-dashed border-ink-500 bg-white text-ink-600" aria-label="Photo du groupe">
                <img x-show="preview" x-cloak :src="preview" alt="" class="size-full object-cover">
                <x-icon name="camera" :size="24" x-show="!preview" />
                <input type="file" name="avatar" accept="image/*" class="sr-only" @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
            </label>
            <label class="flex flex-1 flex-col gap-1.5"><span class="field-label">Nom du groupe</span>
                <input name="name" value="{{ old('name') }}" required placeholder="Étudiants à Wuhan" class="field"></label>
        </div>

        <fieldset class="flex flex-col gap-2">
            <legend class="field-label mb-2">Type de groupe</legend>
            <div class="grid grid-cols-2 gap-2.5">
                @foreach ([['1', 'Public', 'Tout membre peut le trouver et le rejoindre'], ['0', 'Privé', 'Sur invitation uniquement']] as [$v, $t, $d])
                    <label class="flex min-h-[76px] cursor-pointer flex-col gap-1 rounded-xl border-2 p-3"
                           :class="isPublic === '{{ $v }}' ? 'border-brand-600 bg-brand-100' : 'border-brand-100 bg-brand-50'">
                        <input type="radio" name="is_public" value="{{ $v }}" x-model="isPublic" class="sr-only">
                        <span class="text-[14.5px] font-bold">{{ $t }}</span>
                        <span class="text-xs leading-snug text-ink-600">{{ $d }}</span>
                    </label>
                @endforeach
            </div>
        </fieldset>

        <fieldset class="flex flex-col sm:grid sm:grid-cols-2 sm:gap-x-6">
            <legend class="mb-2 flex w-full justify-between sm:col-span-2"><span class="field-label">Ajouter des membres</span><span class="text-[13px] font-bold text-brand-600" x-text="members.length + ' sélectionné(s)'"></span></legend>
            @foreach ($people as $p)
                <label class="flex min-h-[58px] cursor-pointer items-center gap-3 border-b border-ink-100">
                    <x-avatar :user="$p" :size="42" />
                    <span class="flex flex-1 flex-col"><span class="text-[14.5px] font-bold">{{ $p->name }}</span><span class="text-[12.5px] text-ink-600">{{ $p->role_label }}{{ $p->city ? ' · '.$p->city : '' }}</span></span>
                    <input type="checkbox" name="members[]" value="{{ $p->id }}" x-model.number="members" class="size-5 accent-brand-600">
                </label>
            @endforeach
        </fieldset>

        <button class="btn-primary sticky bottom-[calc(env(safe-area-inset-bottom)+92px)] min-h-[52px] lg:static lg:self-end lg:px-8">Créer le groupe</button>
    </form>
</x-layouts.app>
