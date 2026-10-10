<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DossierDocument extends Model
{
    public const MISSING = 'missing';
    public const PENDING = 'pending';
    public const TO_COMPLETE = 'to_complete';
    public const VALIDATED = 'validated';

    public const STATUS_LABELS = [
        self::MISSING => ['Manquant', 'badge-err'],
        self::PENDING => ['En vérification', 'badge-warn'],
        self::TO_COMPLETE => ['À compléter', 'badge-warn'],
        self::VALIDATED => ['Validé', 'badge-ok'],
    ];

    protected $fillable = ['dossier_id', 'type', 'label', 'status', 'path', 'original_name', 'note', 'submitted_at'];

    protected function casts(): array
    {
        return ['submitted_at' => 'datetime'];
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status][0] ?? $this->status;
    }

    public function statusClass(): string
    {
        return self::STATUS_LABELS[$this->status][1] ?? 'badge';
    }

    public function canUpload(): bool
    {
        return in_array($this->status, [self::MISSING, self::TO_COMPLETE], true);
    }
}
