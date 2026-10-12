@props(['icon' => 'search', 'title', 'text' => null])
<div {{ $attributes->class('flex flex-col items-center gap-3 rounded-2xl border border-dashed border-ink-200 bg-white/60 px-6 py-10 text-center') }}>
    <span class="relative flex size-14 items-center justify-center rounded-2xl bg-linear-to-br from-brand-50 to-brand-100 text-brand-600 ring-1 ring-brand-100">
        <x-icon :name="$icon" :size="26" />
    </span>
    <span class="font-display text-[17px] font-bold text-navy-950">{{ $title }}</span>
    @if ($text)
        <p class="max-w-sm text-sm leading-relaxed text-ink-600">{{ $text }}</p>
    @endif
    @if (trim($slot))
        <div class="mt-1">{{ $slot }}</div>
    @endif
</div>
