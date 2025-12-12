<?php

namespace App\Models;

use App\Traits\HasUniqueFormId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ReligiousLeader extends Model
{
    use HasUniqueFormId;

    protected $fillable = [
        'unique_id',
        'user_id',
        'date',
        'attached_hf',
        'uc',
        'district',
        'outreach',
        'group_type',
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
