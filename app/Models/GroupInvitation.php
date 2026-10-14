<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Invitation personnelle à un groupe : le lien n'est valable que pour le compte
 * dont l'adresse e-mail correspond, une seule fois, pendant VALID_DAYS jours.
 * Seule l'empreinte du jeton est enregistrée.
 */
class GroupInvitation extends Model
{
    public const VALID_DAYS = 7;

    protected $fillable = ['conversation_id', 'invited_by', 'email', 'token_hash', 'expires_at', 'accepted_at', 'accepted_by'];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime', 'accepted_at' => 'datetime'];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('accepted_at')->where('expires_at', '>', now());
    }

    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    public static function findByToken(string $token): ?self
    {
        return static::where('token_hash', self::hashToken($token))->first();
    }

    /**
     * Crée (ou renouvelle) l'invitation d'une adresse pour un groupe.
     * Un renouvellement change le jeton : l'ancien lien ne fonctionne plus.
     * @return array{0: self, 1: string}  l'invitation et le jeton en clair (à mettre dans le lien)
     */
    public static function issue(Conversation $group, User $inviter, string $email): array
    {
        $token = Str::random(48);

        $invitation = static::updateOrCreate(
            ['conversation_id' => $group->id, 'email' => mb_strtolower(trim($email)), 'accepted_at' => null],
            ['invited_by' => $inviter->id, 'token_hash' => self::hashToken($token), 'expires_at' => now()->addDays(self::VALID_DAYS)],
        );

        return [$invitation, $token];
    }

    public function isUsable(): bool
    {
        return ! $this->accepted_at && $this->expires_at->isFuture();
    }

    public function isFor(User $user): bool
    {
        return mb_strtolower((string) $user->email) === $this->email;
    }

    /** Fait entrer la personne dans le groupe et marque l'invitation comme utilisée */
    public function accept(User $user): void
    {
        $group = $this->conversation;

        if (! $group->hasMember($user)) {
            $group->users()->attach($user->id, ['role' => 'member']);
            $group->messages()->create(['user_id' => $user->id, 'body' => $user->first_name.' a rejoint le groupe sur invitation.']);
        }

        $this->update(['accepted_at' => now(), 'accepted_by' => $user->id]);
    }

    /** À la création d'un compte (adresse vérifiée par code) : accepter les invitations en attente */
    public static function acceptPendingFor(User $user): int
    {
        $invitations = static::pending()->where('email', mb_strtolower((string) $user->email))->with('conversation')->get();
        $invitations->each(fn (self $i) => $i->accept($user));

        return $invitations->count();
    }
}
