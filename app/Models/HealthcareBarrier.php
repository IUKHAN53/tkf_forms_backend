<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class HealthcareBarrier extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'hfs',
        'address',
        'uc',
        'participants_males',
        'participants_females',
        'group_type',
        'facilitator_tkf',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'date' => 'date',
        'participants_males' => 'integer',
        'participants_females' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function participants(): MorphMany
    {
        return $this->morphMany(Participant::class, 'participantable');
    }
}
