<x-layouts.auth title="Votre profil">
    <form method="POST" action="{{ route('onboarding.profile.store') }}" class="flex flex-1 flex-col gap-6"
          x-data="{ degree: @js(old('last_degree', $user->last_degree)), interests: @js(old('interests', $user->interests ?? [])) }">
        @csrf
        <x-progress-steps :current="3" :total="4" />

        <div class="flex flex-col gap-2">
            <span class="eyebrow">Bienvenue, {{ $user->first_name }}</span>
            <h1 class="text-[30px] font-extrabold leading-[1.05] text-navy-950 lg:text-[36px]">Parlez-nous de vous</h1>
            <p class="text-[15px] leading-relaxed text-ink-600">Votre conseiller s’en sert pour vous proposer les bons programmes. Cela prend moins d’une minute.</p>
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

        <fieldset class="flex flex-col gap-2.5">
            <legend class="field-label mb-2.5">Dernier diplôme obtenu</legend>
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                @foreach ($degrees as $d)
                    <label class="chip justify-center" :class="degree === '{{ $d }}' && 'chip-on'">
                        <input type="radio" name="last_degree" value="{{ $d }}" x-model="degree" class="sr-only">{{ $d }}
                    </label>
                @endforeach
            </div>
        </fieldset>

        <fieldset class="flex flex-col gap-2.5">
            <legend class="mb-2.5 flex w-full items-baseline justify-between">
                <span class="field-label">Domaines qui vous intéressent</span>
                <span class="text-xs font-semibold text-ink-500" x-text="interests.length ? interests.length + ' choisi(s)' : 'Plusieurs choix possibles'"></span>
            </legend>
            <div class="flex flex-wrap gap-2">
                @foreach ($fields as $f)
                    <label class="chip" :class="interests.includes('{{ $f }}') && 'chip-on'">
                        <input type="checkbox" name="interests[]" value="{{ $f }}" x-model="interests" class="sr-only">
                        <x-icon name="check" :size="14" stroke-width="3" x-show="interests.includes('{{ $f }}')" x-cloak />{{ $f }}
                    </label>
                @endforeach
            </div>
        </fieldset>

        <fieldset class="flex flex-col gap-2.5" x-data="{ destination: @js(old('destination', $user->destination)) }">
            <legend class="mb-2.5 flex w-full items-baseline justify-between">
                <span class="field-label">{{ match ($user->role) { \App\Models\User::ROLE_STUDENT_ABROAD => 'Pays où vous étudiez', \App\Models\User::ROLE_GRADUATE => 'Pays où vous avez étudié', default => 'Où souhaitez-vous étudier ?' } }}</span>
                <span class="text-xs font-semibold text-ink-500">Vous pourrez en changer</span>
            </legend>
            <div class="flex flex-wrap gap-2">
                @foreach ($destinations as $key => $name)
                    <label class="chip" :class="destination === '{{ $key }}' && 'chip-on'">
                        <input type="radio" name="destination" value="{{ $key }}" x-model="destination" class="sr-only">{{ $name }}
                    </label>
                @endforeach
            </div>
            <x-field-error name="destination" />
        </fieldset>

        <label class="flex flex-col gap-1.5">
            <span class="field-label">Niveau dans la langue d’enseignement visée</span>
            <select name="language_level" class="field">
                <option value="">Je ne sais pas encore</option>
                @foreach ($levels as $key => $label)
                    <option value="{{ $key }}" @selected(old('language_level', $user->language_level) === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <span class="text-[12.5px] text-ink-500">Français, anglais, chinois… selon le pays et le programme choisis.</span>
        </label>

        <div class="mt-auto flex gap-2.5 pt-2">
            <button type="submit" name="skip" value="1" formnovalidate class="btn-ghost min-h-[54px] text-ink-700">Plus tard</button>
            <button type="submit" class="btn-primary min-h-[54px] flex-1 text-base">Continuer <x-icon name="arrow" :size="18" /></button>
        </div>
    </form>
</x-layouts.auth>
