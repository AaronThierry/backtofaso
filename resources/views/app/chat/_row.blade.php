@php($last = $conversation->latestMessage)
@php($unread = $conversation->unreadCountFor($user))
@php($name = $conversation->displayNameFor($user))
<a href="{{ route('chat.show', $conversation) }}" x-show="!q || @js(mb_strtolower($name)).includes(q.toLowerCase())"
   @class(['relative flex min-h-[68px] items-center gap-3 rounded-xl px-2.5 py-2 transition', 'bg-brand-50 ring-1 ring-brand-100' => $active ?? false, 'hover:bg-ink-50' => ! ($active ?? false)])>
    @if ($active ?? false)
        <span class="absolute -left-px top-1/2 h-7 w-1 -translate-y-1/2 rounded-r-full bg-brand-600"></span>
    @endif
    <span class="relative">
        @php($other = $conversation->type === 'direct' ? $conversation->users->firstWhere('id', '!=', $user->id) : null)
        @if ($other)
            <x-avatar :user="$other" :size="46" />
        @elseif ($conversation->avatarUrl())
            <img src="{{ $conversation->avatarUrl() }}" alt="" class="size-[46px] rounded-[30%] object-cover ring-1 ring-ink-900/5">
        @else
            <x-avatar :initials="$conversation->initials($user)" :size="46" :tone="$conversation->type === 'direct' ? 'brand' : 'ink'" :square="$conversation->type !== 'direct'" />
        @endif
        @if ($unread)
            <span class="absolute -right-0.5 -top-0.5 size-3 rounded-full bg-brand-600 ring-2 ring-white"></span>
        @endif
    </span>
    <span class="flex min-w-0 flex-1 flex-col gap-0.5">
        <span class="flex items-baseline justify-between gap-2">
            <span @class(['truncate text-[14.5px] text-navy-950', 'font-extrabold' => $unread, 'font-bold' => ! $unread])>{{ $name }}</span>
            <span @class(['shrink-0 text-[11.5px]', 'font-bold text-brand-600' => $unread, 'text-ink-500' => ! $unread])>{{ $last?->created_at?->isToday() ? $last->created_at->format('H:i') : $last?->created_at?->translatedFormat('j M') }}</span>
        </span>
        <span class="flex items-center gap-2">
            <span @class(['truncate text-[13px]', 'font-semibold text-ink-800' => $unread, 'text-ink-500' => ! $unread])>
                @if ($last)
                    {{ $last->user_id === $user->id ? 'Vous' : $last->user->first_name }} : {{ $last->attachment_name ? ($last->isImage() ? 'Photo' : 'Fichier : '.$last->attachment_name) : $last->body }}
                @else
                    Aucun message
                @endif
            </span>
            @if ($unread)
                <span class="ml-auto flex h-5 min-w-5 shrink-0 items-center justify-center rounded-full bg-brand-600 px-1.5 text-[11px] font-bold text-white">{{ $unread }}</span>
            @endif
        </span>
    </span>
</a>
