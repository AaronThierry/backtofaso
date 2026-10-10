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

    public function toChatArray(User $viewer): array
    {
        return [
            'id' => $this->id,
            'mine' => $this->user_id === $viewer->id,
            'author' => $this->user->name,
            'initials' => $this->user->initials,
            'official' => $this->user->isStaff(),
            'body' => $this->body,
            'topic' => $this->topic,
            'file' => $this->attachment_path ? [
                'name' => $this->attachment_name,
                'url' => $this->attachmentUrl(),
                'size' => $this->attachment_size ? number_format($this->attachment_size / 1024, 0, ',', ' ').' Ko' : null,
            ] : null,
            'time' => $this->created_at->format('H:i'),
            'date' => $this->created_at->isToday() ? 'Aujourd’hui' : $this->created_at->translatedFormat('j F'),
        ];
    }
}
