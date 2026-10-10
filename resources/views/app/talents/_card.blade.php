<a href="{{ route('talents.show', $profile) }}" class="card flex gap-3 p-3.5">
    <x-avatar :user="$profile->user" :size="48" />
    <span class="flex min-w-0 flex-1 flex-col gap-1">
        <span class="flex items-center justify-between gap-2">
            <span class="truncate text-[15px] font-bold">{{ $profile->user->name }}</span>
            <span class="{{ $profile->availabilityClass() }}">{{ $profile->availabilityLabel() }}</span>
        </span>
        <span class="text-[13px]">{{ $profile->headline }}</span>
        <span class="text-[12.5px] text-ink-600">{{ collect([$profile->school, $profile->school_city, $profile->graduation_year])->filter()->join(' · ') }}</span>
        @if ($profile->languages)
            <span class="mt-1 flex flex-wrap gap-1.5">
                @foreach ($profile->languages as $lang)
                    <span class="rounded-lg bg-white px-2 py-0.5 text-[11.5px] font-semibold text-ink-700">{{ $lang }}</span>
                @endforeach
            </span>
        @endif
    </span>
</a>
