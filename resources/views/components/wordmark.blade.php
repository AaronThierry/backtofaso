@props(['size' => 40, 'dark' => false, 'caption' => null])
{{-- Logo complet Back to Faso : symbole + nom, « Faso » en vert du drapeau --}}
<span {{ $attributes->class(['inline-flex min-w-0 items-center']) }} style="gap: {{ round($size * 0.22) }}px">
    <img src="/images/brand/backtofaso-symbole.svg" alt="" width="{{ $size }}" height="{{ $size }}" class="shrink-0 scale-[1.25]" style="width: {{ $size }}px; height: {{ $size }}px">
    <span class="flex min-w-0 flex-col leading-none">
        <span @class(['whitespace-nowrap font-display font-extrabold tracking-[-0.035em]', 'text-white' => $dark, 'text-ink-900' => ! $dark]) style="font-size: {{ round($size * 0.5) }}px">
            Back to <span @class(['text-faso-green' => ! $dark, 'text-[#2BD078]' => $dark])>Faso</span>
        </span>
        @if ($caption)
            <span @class(['mt-1 truncate font-medium', 'text-white/55' => $dark, 'text-ink-500' => ! $dark]) style="font-size: {{ max(11, round($size * 0.29)) }}px">{{ $caption }}</span>
        @endif
    </span>
</span>
