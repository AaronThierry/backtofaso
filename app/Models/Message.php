<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Message extends Model
{
    protected $fillable = ['conversation_id', 'user_id', 'topic', 'body', 'attachment_path', 'attachment_name', 'attachment_size', 'is_pinned'];

    protected function casts(): array
    {
        return ['is_pinned' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::created(function (Message $message) {
            $message->conversation()->update(['last_message_at' => $message->created_at]);
        });
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachmentUrl(): ?string
    {
        return $this->attachment_path ? Storage::disk('public')->url($this->attachment_path) : null;
    }

    public const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public function isImage(): bool
    {
        return in_array(strtolower(pathinfo((string) $this->attachment_name, PATHINFO_EXTENSION)), self::IMAGE_EXTENSIONS, true);
    }

    /** « Aujourd'hui », « Hier », « 12 octobre » ou « 12 octobre 2025 » */
    public function dayLabel(): string
    {
        return match (true) {
            $this->created_at->isToday() => 'Aujourd’hui',
            $this->created_at->isYesterday() => 'Hier',
            $this->created_at->isCurrentYear() => $this->created_at->translatedFormat('l j F'),
            default => $this->created_at->translatedFormat('j F Y'),
        };
    }

    public function toChatArray(User $viewer): array
    {
        $size = $this->attachment_size;

        return [
            'id' => $this->id,
            'mine' => $this->user_id === $viewer->id,
            'userId' => $this->user_id,
            'author' => $this->user->name,
            'initials' => $this->user->initials,
            'avatar' => $this->user->avatar_url,
            'official' => $this->user->isStaff(),
            'body' => $this->body,
            'topic' => $this->topic,
            'file' => $this->attachment_path ? [
                'name' => $this->attachment_name,
                'url' => $this->attachmentUrl(),
                'image' => $this->isImage(),
                'size' => $size ? ($size >= 1048576 ? number_format($size / 1048576, 1, ',', ' ').' Mo' : number_format($size / 1024, 0, ',', ' ').' Ko') : null,
            ] : null,
            'time' => $this->created_at->format('H:i'),
            'ts' => $this->created_at->getTimestamp(),
            'day' => $this->created_at->format('Y-m-d'),
            'dayLabel' => $this->dayLabel(),
            'canDelete' => $this->user_id === $viewer->id || $viewer->isStaff(),
        ];
    }
}
