<?php

namespace App\Models;

use App\Traits\HasUniqueFormId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class BridgingTheGap extends Model
{
    use HasUniqueFormId;

    protected $table = 'bridging_the_gaps';

    protected $fillable = [
        'unique_id',
        'user_id',
        'date',
        'venue',
        'district',
        'uc',
        'fix_site',
        'participants_males',
        'participants_females',
        'latitude',
        'longitude',
        'ip_address',
        'device_info',
        'started_at',
        'submitted_at',
        'action_plan_file',
    ];

    protected $casts = [
        'date' => 'datetime',
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

    /**
     * Attendance tab participants (directly added to this form)
     */
    public function participants(): MorphMany
    {
        return $this->morphMany(Participant::class, 'participantable');
    }

    /**
     * IIT Team Members (references to participants from other forms)
     */
    public function teamMembers(): HasMany
    {
        return $this->hasMany(BridgingTheGapTeamMember::class);
    }

    /**
     * Action Plans uploaded from Excel
     */
    public function actionPlans(): HasMany
    {
        return $this->hasMany(BridgingTheGapActionPlan::class);
    }
}
