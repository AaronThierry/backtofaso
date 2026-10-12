<x-layouts.auth title="Votre profil">
    <form method="POST" action="{{ route('onboarding.profile.store') }}" class="flex flex-1 flex-col gap-[18px]"
          x-data="{ degree: @js(old('last_degree', $user->last_degree)), interests: @js(old('interests', $user->interests ?? [])) }">
        @csrf
        <div class="flex items-center gap-3">
            <span class="flex size-11 shrink-0 items-center justify-center rounded-full border border-ink-200 text-ink-300"><x-icon name="back" /></span>
            <x-progress-steps :current="\App\Services\OtpService::enabled() ? 3 : 2" :total="\App\Services\OtpService::enabled() ? 4 : 3" class="flex-1" />
        </div>

        <div class="flex flex-col gap-1.5">
            <h1 class="text-[26px] font-bold leading-tight">Parlez-nous de vous</h1>
            <p class="text-[14.5px] leading-relaxed text-ink-600">Votre conseiller s’en sert pour vous proposer les bons programmes.</p>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <label class="flex min-w-0 flex-col gap-1.5">
                <span class="field-label">Date de naissance</span>
                <input type="date" name="birth_date" value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}" class="field">
                <x-field-error name="birth_date" />
            </label>
            <label class="flex min-w-0 flex-col gap-1.5">
                <span class="field-label">Ville</span>
                <select name="city" class="field">
                    @foreach ($cities as $city)
                        <option @selected(old('city', $user->city ?? 'Bobo-Dioulasso') === $city)>{{ $city }}</option>
                    @endforeach
                </select>
            </label>
        </div>

        <fieldset class="flex flex-col gap-2">
            <legend class="field-label mb-2">Dernier diplôme obtenu</legend>
            <div class="flex flex-wrap gap-2">
                @foreach ($degrees as $d)
                    <label class="chip" :class="degree === '{{ $d }}' && 'chip-on'">
                        <input type="radio" name="last_degree" value="{{ $d }}" x-model="degree" class="sr-only">{{ $d }}
                    </label>
                @endforeach
            </div>
        </fieldset>

        <fieldset class="flex flex-col gap-2">
            <legend class="field-label mb-2">Domaines qui vous intéressent <span class="font-medium text-ink-600">(plusieurs choix)</span></legend>
            <div class="flex flex-wrap gap-2">
                @foreach ($fields as $f)
                    <label class="chip" :class="interests.includes('{{ $f }}') && 'chip-on'">
                        <input type="checkbox" name="interests[]" value="{{ $f }}" x-model="interests" class="sr-only">{{ $f }}
                    </label>
                @endforeach
            </div>
        </fieldset>

        <label class="flex flex-col gap-1.5">
            <span class="field-label">Niveau de chinois</span>
            <select name="chinese_level" class="field">
                @foreach ($levels as $key => $label)
                    <option value="{{ $key }}" @selected(old('chinese_level', $user->chinese_level) === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <div class="mt-auto flex gap-2.5 pt-4">
            <button type="submit" name="skip" value="1" formnovalidate class="btn-ghost min-h-[52px] text-ink-900">Plus tard</button>
            <button type="submit" class="btn-primary min-h-[52px] flex-1 text-base">Continuer</button>
        </div>
    </form>
</x-layouts.auth>
