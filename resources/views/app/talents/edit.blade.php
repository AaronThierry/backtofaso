<x-layouts.app title="Mon profil talent" active="talents" width="narrow">
    <form method="POST" action="{{ route('talents.update') }}" enctype="multipart/form-data" class="flex flex-col gap-4 lg:rounded-2xl lg:border lg:border-ink-200 lg:bg-white lg:p-8">
        @csrf @method('PUT')
        <div class="flex items-center gap-3">
            <a href="{{ route('talents.show', $profile) }}" aria-label="Retour" class="flex size-11 items-center justify-center rounded-full border border-ink-200 bg-white"><x-icon name="back" /></a>
            <h1 class="text-2xl font-bold">Mon profil talent</h1>
        </div>
        @if ($errors->any())
            <div class="rounded-[10px] bg-err-50 px-4 py-3 text-sm font-semibold text-err-700">{{ $errors->first() }}</div>
        @endif

        <label class="flex flex-col gap-1.5"><span class="field-label">Titre</span>
            <input name="headline" value="{{ old('headline', $profile->headline) }}" placeholder="Ingénieure en génie logiciel" required class="field"></label>

        <div class="grid gap-3 sm:grid-cols-2">
            <label class="flex flex-col gap-1.5"><span class="field-label">Domaine</span>
                <select name="field" class="field">@foreach ($fields as $f)<option @selected(old('field', $profile->field) === $f)>{{ $f }}</option>@endforeach</select></label>
            <label class="flex flex-col gap-1.5"><span class="field-label">Ville</span>
                <input name="location" value="{{ old('location', $profile->location) }}" class="field"></label>
            <label class="flex flex-col gap-1.5"><span class="field-label">Université en Chine</span>
                <input name="school" value="{{ old('school', $profile->school) }}" class="field"></label>
            <label class="flex flex-col gap-1.5"><span class="field-label">Ville de l’université</span>
                <input name="school_city" value="{{ old('school_city', $profile->school_city) }}" class="field"></label>
            <label class="flex flex-col gap-1.5"><span class="field-label">Année du diplôme</span>
                <input name="graduation_year" type="number" value="{{ old('graduation_year', $profile->graduation_year) }}" class="field"></label>
            <label class="flex flex-col gap-1.5"><span class="field-label">Niveau HSK</span>
                <input name="hsk_level" value="{{ old('hsk_level', $profile->hsk_level) }}" placeholder="HSK 5" class="field"></label>
        </div>

        <label class="flex flex-col gap-1.5"><span class="field-label">Compétences <span class="font-medium text-ink-600">(séparées par des virgules)</span></span>
            <input name="skills" value="{{ old('skills', implode(', ', $profile->skills ?? [])) }}" class="field"></label>
        <label class="flex flex-col gap-1.5"><span class="field-label">Langues <span class="font-medium text-ink-600">(séparées par des virgules)</span></span>
            <input name="languages" value="{{ old('languages', implode(', ', $profile->languages ?? [])) }}" class="field"></label>

        <div class="grid gap-3 sm:grid-cols-2">
            <label class="flex flex-col gap-1.5"><span class="field-label">Disponibilité</span>
                <select name="availability" class="field">
                    @foreach (\App\Models\TalentProfile::AVAILABILITY as $key => [$label])
                        <option value="{{ $key }}" @selected(old('availability', $profile->availability) === $key)>{{ $label }}</option>
                    @endforeach
                </select></label>
            <label class="flex flex-col gap-1.5"><span class="field-label">Disponible à partir de</span>
                <input name="available_from" value="{{ old('available_from', $profile->available_from) }}" placeholder="janvier 2027" class="field"></label>
        </div>

        <label class="flex flex-col gap-1.5"><span class="field-label">Présentation</span>
            <textarea name="bio" rows="4" class="field py-3">{{ old('bio', $profile->bio) }}</textarea></label>

        <label class="flex flex-col gap-1.5"><span class="field-label">CV (PDF)</span>
            <input type="file" name="cv" accept="application/pdf" class="text-sm"></label>

        <label class="flex items-center gap-3"><input type="checkbox" name="is_public" value="1" @checked(old('is_public', $profile->is_public)) class="size-5 accent-brand-600">
            <span class="text-sm">Rendre mon profil visible des recruteurs</span></label>

        <div class="flex flex-col gap-2.5 sm:flex-row-reverse sm:justify-start">
            <button class="btn-primary min-h-[52px] sm:px-8">Enregistrer</button>
            <a href="{{ route('talents.show', $profile) }}" class="btn-ghost min-h-[52px] text-ink-900">Annuler</a>
        </div>
    </form>
</x-layouts.app>
