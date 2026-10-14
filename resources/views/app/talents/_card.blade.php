<a href="{{ route('talents.show', $profile) }}" class="card card-hover group flex flex-col gap-4 p-5">
    <span class="flex items-start gap-3.5">
        <span class="relative">
            <x-avatar :user="$profile->user" :size="54" />
            @if ($profile->verified_at)
                <span class="absolute -bottom-0.5 -right-0.5 flex size-5 items-center justify-center rounded-full bg-brand-600 text-white ring-2 ring-white" title="Profil vérifié"><x-icon name="check" :size="11" stroke-width="3.4" /></span>
            @endif
        </span>
        <span class="flex min-w-0 flex-1 flex-col gap-1">
            <span class="truncate font-display text-[17px] font-bold text-navy-950 group-hover:text-brand-700">{{ $profile->user->name }}</span>
            <span class="line-clamp-2 text-[13.5px] leading-snug text-ink-700">{{ $profile->headline }}</span>
        </span>
        <span class="{{ $profile->availabilityClass() }} shrink-0">{{ $profile->availabilityLabel() }}</span>
    </span>

    <span class="flex flex-col gap-1.5 text-[13px] text-ink-600">
        @if ($profile->countryName())
            <span class="flex items-center gap-2"><x-icon name="globe" :size="16" class="shrink-0 text-ink-400" /><span class="truncate">Formé(e) en <b class="font-semibold text-ink-800">{{ $profile->countryName() }}</b></span></span>
        @endif
        @if ($profile->school || $profile->school_city)
            <span class="flex items-center gap-2"><x-icon name="graduation" :size="16" class="shrink-0 text-ink-400" /><span class="truncate">{{ collect([$profile->school, $profile->school_city, $profile->graduation_year])->filter()->join(' · ') }}</span></span>
        @endif
        @if ($profile->location)
            <span class="flex items-center gap-2"><x-icon name="map" :size="16" class="shrink-0 text-ink-400" /><span class="truncate">{{ $profile->location }}</span></span>
        @endif
    </span>

    @if ($profile->languages || $profile->language_cert)
        <span class="mt-auto flex flex-wrap gap-1.5 border-t border-ink-100 pt-3.5">
            @if ($profile->language_cert)
                <span class="rounded-lg bg-gold-50 px-2 py-1 text-[11.5px] font-bold text-gold-700 ring-1 ring-gold-200">{{ $profile->language_cert }}</span>
            @endif
            @foreach (array_slice($profile->languages ?? [], 0, 3) as $lang)
                <span class="rounded-lg bg-ink-50 px-2 py-1 text-[11.5px] font-semibold text-ink-700 ring-1 ring-ink-100">{{ $lang }}</span>
            @endforeach
        </span>
    @endif
</a>
