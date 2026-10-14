<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_CANDIDATE = 'candidate';
    public const ROLE_STUDENT_ABROAD = 'student_abroad';
    public const ROLE_GRADUATE = 'graduate';
    public const ROLE_RECRUITER = 'recruiter';
    public const ROLE_AGENT = 'agent';
    public const ROLE_ADMIN = 'admin';

    /** Profils proposés à l'inscription */
    public const PUBLIC_ROLES = [
        self::ROLE_CANDIDATE => ['Futur étudiant', 'Je veux partir étudier à l’étranger'],
        self::ROLE_STUDENT_ABROAD => ['Étudiant à l’étranger', 'J’étudie déjà hors du Burkina'],
        self::ROLE_GRADUATE => ['Diplômé de retour', 'Je cherche un emploi ou je lance un projet'],
        self::ROLE_RECRUITER => ['Recruteur / entreprise', 'Je cherche des profils formés à l’étranger'],
    ];

    /** Icône de chaque profil public (composant x-icon) */
    public const ROLE_ICONS = [
        self::ROLE_CANDIDATE => 'plane',
        self::ROLE_STUDENT_ABROAD => 'graduation',
        self::ROLE_GRADUATE => 'briefcase',
        self::ROLE_RECRUITER => 'users',
    ];

    public const DEGREES =['Bac', 'Licence', 'Master', 'Autre'];

    public const FIELDS = ['Informatique', 'Génie civil', 'Santé', 'Commerce', 'Économie et gestion', 'Énergie et mines', 'Agronomie', 'Langues et traduction', 'Autre'];

    /** Niveau dans la langue d’enseignement du pays visé */
    public const LANGUAGE_LEVELS = [
        'beginner' => 'Débutant',
        'intermediate' => 'Intermédiaire',
        'advanced' => 'Avancé',
        'fluent' => 'Courant ou langue maternelle',
    ];

    protected $fillable = [
        'first_name', 'last_name', 'phone', 'email', 'password', 'role',
        'birth_date', 'city', 'last_degree', 'interests', 'destination', 'language_level', 'avatar_path',
        'notify_push', 'notify_sms', 'notify_whatsapp', 'notify_email',
        'phone_verified_at', 'email_verified_at', 'onboarded_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'phone_verified_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'onboarded_at' => 'datetime',
            'birth_date' => 'date',
            'interests' => 'array',
            'notify_push' => 'boolean',
            'notify_sms' => 'boolean',
            'notify_whatsapp' => 'boolean',
            'notify_email' => 'boolean',
            'password' => 'hashed',
        ];
    }

    // ---------- Attributs calculés ----------

    protected function name(): Attribute
    {
        return Attribute::get(fn () => trim($this->first_name.' '.$this->last_name));
    }

    protected function initials(): Attribute
    {
        return Attribute::get(fn () => mb_strtoupper(mb_substr($this->first_name, 0, 1).mb_substr($this->last_name, 0, 1)));
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::get(fn () => $this->avatar_path ? Storage::disk('public')->url($this->avatar_path) : null);
    }

    protected function roleLabel(): Attribute
    {
        return Attribute::get(fn () => match ($this->role) {
            self::ROLE_AGENT => 'Équipe Travel Express',
            self::ROLE_ADMIN => 'Administrateur',
            default => self::PUBLIC_ROLES[$this->role][0] ?? $this->role,
        });
    }

    public function isStaff(): bool
    {
        return in_array($this->role, [self::ROLE_AGENT, self::ROLE_ADMIN], true);
    }

    public function isRecruiter(): bool
    {
        return $this->role === self::ROLE_RECRUITER;
    }

    public function needsDossier(): bool
    {
        return in_array($this->role, [self::ROLE_CANDIDATE, self::ROLE_STUDENT_ABROAD], true);
    }

    // ---------- Relations ----------

    public function dossier(): HasOne
    {
        return $this->hasOne(Dossier::class)->latestOfMany();
    }

    public function dossiers(): HasMany
    {
        return $this->hasMany(Dossier::class);
    }

    public function talentProfile(): HasOne
    {
        return $this->hasOne(TalentProfile::class);
    }

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class)
            ->withPivot(['role', 'last_read_at'])
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /** Messages non lus, toutes conversations confondues (pastille du menu Chat) */
    public function unreadMessagesCount(): int
    {
        return Message::query()
            ->join('conversation_user as cu', 'cu.conversation_id', '=', 'messages.conversation_id')
            ->where('cu.user_id', $this->id)
            ->where('messages.user_id', '!=', $this->id)
            ->where(fn ($q) => $q->whereNull('cu.last_read_at')->orWhereColumn('messages.created_at', '>', 'cu.last_read_at'))
            ->count();
    }
}
