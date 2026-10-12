@php($last = $conversation->latestMessage)
@php($unread = $conversation->unreadCountFor($user))
<a href="{{ route('chat.show', $conversation) }}" @class(['flex min-h-16 items-center gap-3 border-b border-ink-100 py-2 lg:-mx-2 lg:rounded-lg lg:border-transparent lg:px-2', 'lg:bg-brand-50' => $active ?? false, 'lg:hover:bg-ink-50' => ! ($active ?? false)])>
    <x-avatar :initials="$conversation->initials($user)" :size="46" :tone="$conversation->type === 'direct' ? 'green' : 'ink'" :square="$conversation->type !== 'direct'" />
    <span class="flex min-w-0 flex-1 flex-col gap-0.5">
        <span class="flex justify-between gap-2">
            <span class="truncate text-[14.5px] font-bold">{{ $conversation->displayNameFor($user) }}</span>
            <span class="shrink-0 text-xs text-ink-600">{{ $last?->created_at?->isToday() ? $last->created_at->format('H:i') : $last?->created_at?->translatedFormat('j M') }}</span>
        </span>
        <span class="flex items-center gap-2">
            <span class="truncate text-[13px] text-ink-600">
                @if ($last)
                    {{ $last->user_id === $user->id ? 'Vous' : $last->user->first_name }} : {{ $last->attachment_name ? 'Fichier partagé : '.$last->attachment_name : $last->body }}
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
