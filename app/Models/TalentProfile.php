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
