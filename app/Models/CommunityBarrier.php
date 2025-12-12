<?php

namespace App\Models;

use App\Traits\HasUniqueFormId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CommunityBarrier extends Model
{
    use HasUniqueFormId;

    protected $fillable = [
        'unique_id',
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
        'ip_address',
        'device_info',
        'started_at',
        'submitted_at',
    ];

    protected $casts = [
        'date' => 'datetime',
        'community' => 'array',
        'group_type' => 'array',
        'participants_males' => 'integer',
        'participants_females' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'device_info' => 'array',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
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
