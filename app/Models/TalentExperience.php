<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TalentExperience extends Model
{
    protected $fillable = ['talent_profile_id', 'title', 'organization', 'period', 'kind', 'position'];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(TalentProfile::class, 'talent_profile_id');
    }
}
