<x-layouts.app title="Mon profil talent" active="talents" width="narrow">
    <form method="POST" action="{{ route('talents.update') }}" enctype="multipart/form-data" class="flex flex-col gap-6"
          x-data="{ isPublic: {{ old('is_public', $profile->is_public) ? 'true' : 'false' }}, cvName: '', bio: @js(old('bio', $profile->bio) ?? ''), saving: false }" @submit="saving = true">
        @csrf @method('PUT')

        <x-page-header title="Mon profil talent" :back="route('talents.show', $profile)" eyebrow="Vivier de talents" subtitle="Ce que les recruteurs verront de vous. Soignez le titre et la présentation." />

        @if ($errors->any())
            <div class="flex items-center gap-3 rounded-2xl bg-err-50 px-4 py-3.5 text-sm font-semibold text-err-700 ring-1 ring-err-700/15"><x-icon name="help" :size="18" /> {{ $errors->first() }}</div>
        @endif

        {{-- Identité professionnelle --}}
        <section class="card flex flex-col gap-4 p-5 sm:p-6">
            <h2 class="flex items-center gap-2.5 text-[17px] font-bold text-navy-950"><span class="flex size-8 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><x-icon name="briefcase" :size="17" /></span> Identité professionnelle</h2>
            <label class="flex flex-col gap-1.5"><span class="field-label">Titre</span>
                <input name="headline" value="{{ old('headline', $profile->headline) }}" placeholder="Ingénieure en génie logiciel" required class="field"></label>
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="flex flex-col gap-1.5"><span class="field-label">Domaine</span>
                    <select name="field" class="field">@foreach ($fields as $f)<option @selected(old('field', $profile->field) === $f)>{{ $f }}</option>@endforeach</select></label>
                <label class="flex flex-col gap-1.5"><span class="field-label">Ville</span>
                    <input name="location" value="{{ old('location', $profile->location) }}" placeholder="Ouagadougou" class="field"></label>
            </div>
        </section>

        {{-- Formation --}}
        <section class="card flex flex-col gap-4 p-5 sm:p-6">
            <h2 class="flex items-center gap-2.5 text-[17px] font-bold text-navy-950"><span class="flex size-8 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><x-icon name="graduation" :size="17" /></span> Formation à l’étranger</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="flex flex-col gap-1.5"><span class="field-label">Pays des études</span>
                    <select name="study_country" class="field">
                        <option value="">Choisir un pays</option>
                        @foreach ($countries as $key => $name)
                            <option value="{{ $key }}" @selected(old('study_country', $profile->study_country) === $key)>{{ $name }}</option>
                        @endforeach
                    </select>
                    <x-field-error name="study_country" /></label>
                <label class="flex flex-col gap-1.5"><span class="field-label">Université ou école</span>
                    <input name="school" value="{{ old('school', $profile->school) }}" class="field"></label>
                <label class="flex flex-col gap-1.5"><span class="field-label">Ville</span>
                    <input name="school_city" value="{{ old('school_city', $profile->school_city) }}" placeholder="Montréal, Lyon, Wuhan…" class="field"></label>
                <label class="flex flex-col gap-1.5"><span class="field-label">Année du diplôme</span>
                    <input name="graduation_year" type="number" value="{{ old('graduation_year', $profile->graduation_year) }}" placeholder="{{ date('Y') }}" class="field"></label>
                <label class="flex flex-col gap-1.5 sm:col-span-2"><span class="field-label">Certification de langue <span class="font-medium text-ink-500">(facultatif)</span></span>
                    <input name="language_cert" value="{{ old('language_cert', $profile->language_cert) }}" maxlength="60" placeholder="HSK 5, TOEFL 95, DELF B2, TestDaF…" class="field"></label>
            </div>
        </section>

        {{-- Compétences --}}
        <section class="card flex flex-col gap-4 p-5 sm:p-6">
            <h2 class="flex items-center gap-2.5 text-[17px] font-bold text-navy-950"><span class="flex size-8 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><x-icon name="spark" :size="17" /></span> Compétences et langues</h2>
            <label class="flex flex-col gap-1.5"><span class="field-label">Compétences</span>
                <input name="skills" value="{{ old('skills', implode(', ', $profile->skills ?? [])) }}" placeholder="Laravel, gestion de projet, AutoCAD" class="field">
                <span class="text-[12.5px] text-ink-500">Séparez-les par des virgules.</span></label>
            <label class="flex flex-col gap-1.5"><span class="field-label">Langues</span>
                <input name="languages" value="{{ old('languages', implode(', ', $profile->languages ?? [])) }}" placeholder="Français, Anglais, Mooré" class="field">
                <span class="text-[12.5px] text-ink-500">Séparez-les par des virgules.</span></label>
        </section>

        {{-- Disponibilité et présentation --}}
        <section class="card flex flex-col gap-4 p-5 sm:p-6">
            <h2 class="flex items-center gap-2.5 text-[17px] font-bold text-navy-950"><span class="flex size-8 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><x-icon name="calendar" :size="17" /></span> Disponibilité et présentation</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="flex flex-col gap-1.5"><span class="field-label">Disponibilité</span>
                    <select name="availability" class="field">
                        @foreach (\App\Models\TalentProfile::AVAILABILITY as $key => [$label])
                            <option value="{{ $key }}" @selected(old('availability', $profile->availability) === $key)>{{ $label }}</option>
                        @endforeach
                    </select></label>
                <label class="flex flex-col gap-1.5"><span class="field-label">Disponible à partir de</span>
                    <input name="available_from" value="{{ old('available_from', $profile->available_from) }}" placeholder="janvier 2027" class="field"></label>
            </div>
            <label class="flex flex-col gap-1.5">
                <span class="flex items-baseline justify-between"><span class="field-label">Présentation</span><span class="text-xs text-ink-500" x-text="bio.length + ' caractères'"></span></span>
                <textarea name="bio" rows="5" x-model="bio" placeholder="Votre parcours, ce que vous savez faire, ce que vous cherchez." class="field py-3 leading-relaxed"></textarea>
            </label>

            <label class="flex cursor-pointer items-center gap-4 rounded-2xl border-2 border-dashed border-ink-200 p-4 transition hover:border-brand-300 hover:bg-brand-50/40">
                <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-err-50 text-err-700"><x-icon name="file" :size="20" /></span>
                <span class="flex min-w-0 flex-1 flex-col gap-0.5">
                    <span class="truncate text-[14.5px] font-bold text-navy-950" x-text="cvName || @js($profile->cv_path ? 'CV déjà envoyé, cliquez pour le remplacer' : 'Ajouter mon CV')">{{ $profile->cv_path ? 'CV déjà envoyé, cliquez pour le remplacer' : 'Ajouter mon CV' }}</span>
                    <span class="text-[12.5px] text-ink-500">PDF uniquement</span>
                </span>
                <x-icon name="upload" class="text-ink-400" />
                <input type="file" name="cv" accept="application/pdf" class="sr-only" @change="cvName = $event.target.files[0]?.name ?? ''">
            </label>
        </section>

        {{-- Visibilité --}}
        <label class="card flex cursor-pointer items-center gap-4 p-5 sm:p-6">
            <span class="flex size-11 shrink-0 items-center justify-center rounded-xl transition" :class="isPublic ? 'bg-ok-50 text-ok-700' : 'bg-ink-50 text-ink-500'"><x-icon name="eye" /></span>
            <span class="flex flex-1 flex-col gap-0.5">
                <span class="text-[15px] font-bold text-navy-950">Visible des recruteurs</span>
                <span class="text-[13px] text-ink-600">Votre profil apparaît dans le vivier une fois vérifié par Travel Express.</span>
            </span>
            <input type="checkbox" name="is_public" value="1" x-model="isPublic" class="peer sr-only">
            <span class="flex h-[30px] w-[50px] shrink-0 rounded-full p-[3px] transition-colors peer-focus-visible:ring-4 peer-focus-visible:ring-brand-200" :class="isPublic ? 'bg-ok-500' : 'bg-ink-200'" aria-hidden="true">
                <span class="size-6 rounded-full bg-white shadow-md transition-transform" :class="isPublic && 'translate-x-5'"></span>
            </span>
        </label>

        {{-- Barre d'enregistrement --}}
        <div class="sticky bottom-[calc(env(safe-area-inset-bottom)+92px)] z-20 flex items-center justify-end gap-2.5 rounded-2xl bg-white/90 p-2.5 shadow-lift ring-1 ring-ink-900/[0.06] backdrop-blur-lg lg:bottom-6">
            <a href="{{ route('talents.show', $profile) }}" class="btn-ghost min-h-12 text-ink-700">Annuler</a>
            <button class="btn-primary min-h-12 flex-1 sm:flex-none sm:px-8" :disabled="saving">
                <span x-show="!saving">Enregistrer</span>
                <span x-show="saving" x-cloak class="flex items-center gap-2"><span class="size-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span> Enregistrement…</span>
            </button>
        </div>
    </form>
</x-layouts.app>
