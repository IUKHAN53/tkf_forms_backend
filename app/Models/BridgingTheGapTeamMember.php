<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BridgingTheGapTeamMember extends Model
{
    protected $fillable = [
        'bridging_the_gap_id',
        'participant_id',
        'source_type',
        'source_id',
    ];

    public function bridgingTheGap(): BelongsTo
    {
        return $this->belongsTo(BridgingTheGap::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    /**
     * Get the source form (Community Barrier or Healthcare Barrier)
     */
    public function source(): BelongsTo
    {
        return $this->morphTo('source', 'source_type', 'source_id');
    }
}
