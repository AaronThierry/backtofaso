<x-layouts.app :title="$profile->user->name" active="talents">
    <div class="flex flex-col gap-6 lg:gap-8">
        <a href="{{ route('talents.index') }}" class="inline-flex items-center gap-1.5 self-start text-sm font-bold text-ink-600 transition hover:text-brand-600"><x-icon name="back" :size="18" /> Vivier de talents</a>
        <x-flash />

        {{-- Carte d'identité --}}
        <section class="card animate-rise overflow-hidden">
            <div class="bg-hero relative h-28 sm:h-36">
                <div class="bg-weave absolute inset-0 opacity-60"></div>
                @if ($mine)
                    <a href="{{ route('talents.edit') }}" class="btn-light absolute right-4 top-4 min-h-10 px-3.5 text-sm"><x-icon name="user" :size="16" /> Modifier</a>
                @endif
            </div>
            <div class="flex flex-col gap-5 px-5 pb-6 sm:px-8 lg:flex-row lg:items-end lg:justify-between">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                    <span class="relative -mt-12 self-start sm:-mt-14">
                        <x-avatar :user="$profile->user" :size="112" class="ring-4 ring-white shadow-lg" />
                        @if ($profile->verified_at)
                            <span class="absolute bottom-1.5 right-1.5 flex size-7 items-center justify-center rounded-full bg-brand-600 text-white ring-[3px] ring-white" title="Profil vérifié"><x-icon name="check" :size="14" stroke-width="3.2" /></span>
                        @endif
                    </span>
                    <div class="flex flex-col gap-1.5 sm:pb-1">
                        <h1 class="text-[28px] font-bold leading-tight text-navy-950 lg:text-[32px]">{{ $profile->user->name }}</h1>
                        <p class="text-[15.5px] text-ink-700">{{ $profile->headline }}</p>
                        <div class="flex flex-wrap items-center gap-2 pt-1">
                            <span class="{{ $profile->availabilityClass() }}">{{ $profile->availabilityLabel() }}{{ $profile->available_from && $profile->availability !== 'available' ? ' · '.$profile->available_from : '' }}</span>
                            @if ($profile->location)
                                <span class="inline-flex items-center gap-1 text-[13px] text-ink-600"><x-icon name="map" :size="15" /> {{ $profile->location }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @unless ($mine)
                    <div class="flex gap-2.5 lg:w-[320px]">
                        @include('app.talents._actions')
                    </div>
                @endunless
            </div>
        </section>

        @if (! $profile->verified_at && $mine)
            <div class="flex items-start gap-3 rounded-2xl bg-warn-50 px-4 py-3.5 text-[13.5px] font-semibold text-warn-700 ring-1 ring-warn-700/15">
                <x-icon name="clock" :size="18" class="mt-px shrink-0" /> Votre profil sera visible des recruteurs après vérification par Travel Express.
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px] lg:items-start lg:gap-8">
            {{-- Présentation --}}
            <div class="flex flex-col gap-6">
                @if ($profile->bio)
                    <section class="card flex flex-col gap-3 p-6">
                        <h2 class="text-lg font-bold text-navy-950">Présentation</h2>
                        <p class="whitespace-pre-line text-[15.5px] leading-relaxed text-ink-800">{{ $profile->bio }}</p>
                    </section>
                @endif

                @if ($profile->skills)
                    <section class="card flex flex-col gap-3.5 p-6">
                        <h2 class="text-lg font-bold text-navy-950">Compétences</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($profile->skills as $skill)
                                <span class="rounded-xl bg-brand-50 px-3.5 py-2 text-[13.5px] font-semibold text-brand-800 ring-1 ring-brand-100">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($profile->experiences->isNotEmpty())
                    <section class="card flex flex-col gap-4 p-6">
                        <h2 class="text-lg font-bold text-navy-950">Parcours</h2>
                        <ol class="flex flex-col">
                            @foreach ($profile->experiences as $exp)
                                <li class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <span @class(['flex size-10 shrink-0 items-center justify-center rounded-xl', 'bg-brand-50 text-brand-600' => $exp->kind === 'education', 'bg-gold-50 text-gold-700' => $exp->kind !== 'education'])>
                                            <x-icon :name="$exp->kind === 'education' ? 'graduation' : 'briefcase'" :size="19" />
                                        </span>
                                        @unless ($loop->last)<span class="my-1 w-0.5 flex-1 rounded-full bg-ink-100"></span>@endunless
                                    </div>
                                    <div class="flex flex-col gap-0.5 pb-5 pt-1.5">
                                        <span class="text-[15px] font-bold text-navy-950">{{ $exp->title }}</span>
                                        <span class="text-[13px] text-ink-600">{{ collect([$exp->organization, $exp->period])->filter()->join(' · ') }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    </section>
                @endif

                @if (! $profile->bio && ! $profile->skills && $profile->experiences->isEmpty())
                    <x-empty icon="user" :title="$mine ? 'Votre profil est encore vide' : 'Pas encore de présentation'"
                             :text="$mine ? 'Ajoutez une présentation, vos compétences et votre parcours pour vous démarquer auprès des recruteurs.' : 'Ce profil n’a pas encore été complété.'">
                        @if ($mine)
                            <a href="{{ route('talents.edit') }}" class="btn-primary min-h-11 text-sm">Compléter mon profil</a>
                        @endif
                    </x-empty>
                @endif
            </div>

            {{-- Informations --}}
            <aside class="flex flex-col gap-4 lg:sticky lg:top-10">
                <section class="card flex flex-col p-2">
                    @foreach ([
                        ['briefcase', 'Domaine', $profile->field],
                        ['graduation', 'Formation', collect([$profile->school, $profile->school_city])->filter()->join(', ')],
                        ['calendar', 'Diplômé(e) en', $profile->graduation_year],
                        ['language', 'Langues', implode(' · ', $profile->languages ?? [])],
                        ['globe', 'Niveau de chinois', $profile->hsk_level],
                    ] as [$icon, $label, $value])
                        @if ($value)
                            <div class="flex items-center gap-3 rounded-xl px-3 py-3">
                                <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-ink-50 text-ink-600"><x-icon :name="$icon" :size="17" /></span>
                                <span class="flex min-w-0 flex-col">
                                    <span class="text-xs text-ink-500">{{ $label }}</span>
                                    <span class="text-sm font-semibold text-navy-950">{{ $value }}</span>
                                </span>
                            </div>
                        @endif
                    @endforeach
                </section>

                @if ($profile->verified_at)
                    <div class="flex items-center gap-3 rounded-2xl bg-ok-50 px-4 py-3.5 text-[13.5px] font-semibold text-ok-700 ring-1 ring-ok-700/15">
                        <x-icon name="shield" class="shrink-0" /> Profil vérifié par Travel Express
                    </div>
                @endif
            </aside>
        </div>
    </div>
</x-layouts.app>
