<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    public const SUBJECTS = [
        'candidature' => 'Je veux étudier à l’étranger',
        'dossier' => 'Mon dossier en cours',
        'recrutement' => 'Recruter des talents',
        'partenariat' => 'Partenariat',
        'autre' => 'Autre question',
    ];

    protected $fillable = ['name', 'email', 'phone', 'subject', 'message', 'ip'];

    protected function casts(): array
    {
        return ['handled_at' => 'datetime'];
    }

    public function subjectLabel(): string
    {
        return self::SUBJECTS[$this->subject] ?? $this->subject;
    }
}
