@if ($profile->cv_path)
    <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($profile->cv_path) }}" target="_blank" rel="noopener" class="btn-secondary flex-1"><x-icon name="file" :size="18" /> Voir le CV</a>
@endif
<form method="POST" action="{{ route('chat.direct', $profile->user) }}" class="flex-[1.4]">@csrf
    <button class="btn-primary w-full"><x-icon name="chat" :size="18" /> Contacter</button>
</form>
