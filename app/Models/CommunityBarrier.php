<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CommunityBarrier extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'venue',
        'uc',
        'district',
        'fix_site',
        'outreach',
        'community',
        'group_type',
        'participants_males',
        'participants_females',
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
