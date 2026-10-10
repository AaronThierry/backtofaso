<x-layouts.app :title="$profile->user->name" active="talents">
    <div class="-mx-5 -mt-5 flex flex-col gap-4 sm:-mx-8 lg:mx-0 lg:mt-0">
        <x-flash />
        <div class="flex flex-col gap-4 border-b border-brand-100 bg-brand-100 px-5 pb-16 pt-[calc(env(safe-area-inset-top)+20px)] sm:px-8 lg:rounded-2xl lg:pt-6">
            <div class="flex justify-between">
                <a href="{{ route('talents.index') }}" aria-label="Retour" class="flex size-11 items-center justify-center rounded-full bg-white"><x-icon name="back" /></a>
                @if ($mine)
                    <a href="{{ route('talents.edit') }}" class="btn min-h-11 bg-white text-sm text-brand-600">Modifier</a>
                @endif
            </div>
            <div class="flex items-center gap-3.5">
                <x-avatar :user="$profile->user" :size="68" class="border-2 border-white" />
                <div class="flex flex-col gap-1">
                    <span class="text-2xl font-bold leading-tight">{{ $profile->user->name }}</span>
                    <span class="text-sm text-ink-700">{{ $profile->headline }}</span>
                </div>
            </div>
        </div>

        <div class="relative -mt-14 flex flex-col gap-3.5 px-5 sm:px-8 lg:px-0">
            <div class="card bg-white px-4 py-1.5">
                @foreach ([
                    ['Disponibilité', '<span class="'.$profile->availabilityClass().'">'.e($profile->availabilityLabel()).'</span>'],
                    ['Basé(e) à', e($profile->location ?? '—')],
                    ['Domaine', e($profile->field)],
                    ['Langues', e(implode(' · ', $profile->languages ?? []))],
                ] as [$label, $value])
                    <div class="flex min-h-[46px] items-center justify-between gap-4 border-b border-ink-100 last:border-0">
                        <span class="text-[13px] text-ink-600">{{ $label }}</span>
                        <span class="text-right text-sm font-semibold">{!! $value !!}</span>
                    </div>
                @endforeach
            </div>

            @if ($profile->bio)
                <p class="text-[15px] leading-relaxed">{{ $profile->bio }}</p>
            @endif

            @if ($profile->skills)
                <section class="flex flex-col gap-2.5">
                    <h2 class="text-lg font-semibold">Compétences</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($profile->skills as $skill)
                            <span class="rounded-[10px] border border-brand-100 bg-brand-50 px-3 py-1.5 text-[13px] font-semibold">{{ $skill }}</span>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($profile->experiences->isNotEmpty())
                <section class="flex flex-col gap-2.5">
                    <h2 class="text-lg font-semibold">Parcours</h2>
                    <div class="card flex flex-col gap-3.5 p-4">
                        @foreach ($profile->experiences as $exp)
                            <div class="flex gap-3">
                                <span @class(['mt-1.5 size-2.5 shrink-0 rounded-full', 'bg-brand-600' => $exp->kind === 'education', 'bg-gold-600' => $exp->kind !== 'education'])></span>
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-sm font-bold">{{ $exp->title }}</span>
                                    <span class="text-[12.5px] text-ink-600">{{ collect([$exp->organization, $exp->period])->filter()->join(' · ') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($profile->verified_at)
                <div class="flex items-center gap-2.5 rounded-[10px] border border-ok-700/20 bg-ok-50 px-3.5 py-3 text-[13px] font-semibold text-ok-700">
                    <x-icon name="shield" /> Profil vérifié par Travel Express
                </div>
            @elseif ($mine)
                <div class="rounded-[10px] bg-warn-50 px-3.5 py-3 text-[13px] font-semibold text-warn-700">Votre profil sera visible des recruteurs après vérification par Travel Express.</div>
            @endif

            @unless ($mine)
                <div class="flex gap-2.5 pb-4">
                    @if ($profile->cv_path)
                        <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($profile->cv_path) }}" target="_blank" class="btn-secondary flex-1">Voir le CV</a>
                    @endif
                    <form method="POST" action="{{ route('chat.direct', $profile->user) }}" class="flex-[1.4]">@csrf
                        <button class="btn-primary w-full">Contacter</button>
                    </form>
                </div>
            @endunless
        </div>
    </div>
</x-layouts.app>
