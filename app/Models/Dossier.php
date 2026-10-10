<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dossier extends Model
{
    public const STEPS = [
        1 => ['Inscription', 'Compte créé, profil complété'],
        2 => ['Documents', 'Pièces du dossier'],
        3 => ['Admission', 'Dossier transmis à l’université'],
        4 => ['Formulaire JW202', 'Délivré après admission'],
        5 => ['Visa étudiant (X1)', 'Rendez-vous à l’ambassade'],
        6 => ['Départ', 'Billet et accueil à l’arrivée'],
    ];

    /** Pièces demandées par défaut à chaque nouveau dossier */
    public const DEFAULT_DOCUMENTS = [
        'passport' => 'Passeport',
        'diploma' => 'Diplôme du baccalauréat',
        'transcripts' => 'Relevés de notes',
        'medical' => 'Certificat médical',
        'photo' => 'Photo d’identité',
    ];

    protected $fillable = ['user_id', 'advisor_id', 'program', 'university', 'city', 'intake_year', 'current_step', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function advisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(DossierDocument::class);
    }

    public static function openFor(User $user): self
    {
        $dossier = static::create([
            'user_id' => $user->id,
            'current_step' => 2,
            'intake_year' => now()->month >= 9 ? now()->year + 1 : now()->year,
        ]);

        foreach (self::DEFAULT_DOCUMENTS as $type => $label) {
            $dossier->documents()->create(['type' => $type, 'label' => $label, 'status' => DossierDocument::MISSING]);
        }

        return $dossier;
    }

    public function stepLabel(): string
    {
        return self::STEPS[$this->current_step][0] ?? '';
    }

    public function progressPercent(): int
    {
        return (int) round(($this->current_step - 1) / (count(self::STEPS) - 1) * 100);
    }

    public function stepsWithState(): array
    {
        $out = [];
        foreach (self::STEPS as $n => [$title, $sub]) {
            $out[] = [
                'n' => $n,
                'title' => $title,
                'sub' => $sub,
                'state' => $n < $this->current_step ? 'done' : ($n === $this->current_step ? 'now' : 'todo'),
            ];
        }

        return $out;
    }
}
