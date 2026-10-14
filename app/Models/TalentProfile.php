<?php

namespace App\Models;

use App\Support\Destinations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TalentProfile extends Model
{
    public const AVAILABILITY = [
        'available' => ['Disponible', 'badge-ok'],
        'soon' => ['Bientôt', 'badge-warn'],
        'employed' => ['En poste', 'badge'],
    ];

    protected $fillable = [
        'user_id', 'headline', 'field', 'school', 'school_city', 'study_country', 'graduation_year', 'skills', 'languages',
        'language_cert', 'availability', 'available_from', 'location', 'bio', 'cv_path', 'verified_at', 'is_public',
    ];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
            'languages' => 'array',
            'verified_at' => 'datetime',
            'is_public' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(TalentExperience::class)->orderBy('position');
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_public', true)->whereNotNull('verified_at');
    }

    /** Titre posé automatiquement à la création du profil, en attendant que le diplômé le personnalise */
    public const DEFAULT_HEADLINE = 'Diplômé(e) de retour au Burkina';

    /** Titre à afficher, ou null tant que le diplômé a gardé le titre par défaut */
    public function customHeadline(): ?string
    {
        return $this->headline && $this->headline !== self::DEFAULT_HEADLINE ? $this->headline : null;
    }

    /**
     * Points à remplir pour un profil attractif.
     * @return array{percent: int, items: array<int, array{label: string, hint: string, done: bool}>}
     */
    public function completion(): array
    {
        $items = [
            ['Photo de profil', 'Les recruteurs regardent d’abord la photo', (bool) $this->user?->avatar_path],
            ['Titre professionnel', 'Ex. « Ingénieure en génie logiciel »', (bool) $this->customHeadline()],
            ['Pays et école', 'Où vous avez obtenu votre diplôme', $this->study_country && $this->school],
            ['Compétences', 'Au moins trois compétences', count($this->skills ?? []) >= 3],
            ['Langues', 'Les langues que vous parlez', (bool) $this->languages],
            ['Présentation', 'Quelques lignes sur votre parcours', mb_strlen((string) $this->bio) >= 60],
            ['CV', 'Au format PDF', (bool) $this->cv_path],
            ['Visible des recruteurs', 'Activez la visibilité du profil', (bool) $this->is_public],
        ];

        $done = count(array_filter($items, fn ($i) => $i[2]));

        return [
            'percent' => (int) round($done / count($items) * 100),
            'items' => array_map(fn ($i) => ['label' => $i[0], 'hint' => $i[1], 'done' => (bool) $i[2]], $items),
        ];
    }

    /** Nom du pays où le diplôme a été obtenu */
    public function countryName(): ?string
    {
        return Destinations::name($this->study_country);
    }

    public function availabilityLabel(): string
    {
        if ($this->availability === 'soon' && $this->available_from) {
            return 'Dès '.$this->available_from;
        }

        return self::AVAILABILITY[$this->availability][0] ?? $this->availability;
    }

    public function availabilityClass(): string
    {
        return self::AVAILABILITY[$this->availability][1] ?? 'badge';
    }
}
