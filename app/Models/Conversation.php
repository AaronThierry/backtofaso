<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    public const SUPER = 'super';
    public const GROUP = 'group';
    public const DIRECT = 'direct';

    /** Thèmes du super groupe ; true = publication réservée à l'équipe */
    public const TOPICS = [
        'annonces' => ['Annonces', true],
        'entraide' => ['Entraide', false],
        'emplois' => ['Emplois', false],
        'bourses' => ['Bourses', true],
    ];

    protected $fillable = ['type', 'name', 'description', 'avatar_path', 'is_public', 'created_by', 'last_message_at'];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'last_message_at' => 'datetime',
        ];
    }

    public static function superGroup(): self
    {
        return static::firstOrCreate(
            ['type' => self::SUPER],
            ['name' => 'Communauté Back to Burkina', 'description' => 'Le groupe de tous les membres.', 'is_public' => true],
        );
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot(['role', 'last_read_at'])->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->whereHas('users', fn ($q) => $q->whereKey($user->id));
    }

    public function isSuper(): bool
    {
        return $this->type === self::SUPER;
    }

    public function hasMember(User $user): bool
    {
        return $this->users()->whereKey($user->id)->exists();
    }

    public function isAdmin(User $user): bool
    {
        return $user->isStaff()
            || $this->users()->whereKey($user->id)->wherePivot('role', 'admin')->exists();
    }

    public function canPost(User $user, ?string $topic): bool
    {
        if (! $this->isSuper()) {
            return $this->hasMember($user);
        }

        $restricted = self::TOPICS[$topic][1] ?? false;

        return $restricted ? $user->isStaff() : true;
    }

    /** Nom affiché (pour une conversation privée : le nom de l'autre personne) */
    public function displayNameFor(User $viewer): string
    {
        if ($this->type === self::DIRECT) {
            $other = $this->users->firstWhere('id', '!=', $viewer->id);

            return $other?->name ?? 'Conversation';
        }

        return $this->name ?? 'Groupe';
    }

    public function initials(User $viewer): string
    {
        $words = preg_split('/\s+/', trim($this->displayNameFor($viewer)));

        return mb_strtoupper(mb_substr($words[0] ?? '', 0, 1).mb_substr($words[1] ?? '', 0, 1));
    }

    public function unreadCountFor(User $user): int
    {
        $pivot = $this->users->firstWhere('id', $user->id)?->pivot;
        $since = $pivot?->last_read_at;

        return $this->messages()
            ->where('user_id', '!=', $user->id)
            ->when($since, fn ($q) => $q->where('created_at', '>', $since))
            ->count();
    }
}
