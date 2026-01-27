<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BridgingTheGapActionPlan extends Model
{
    protected $table = 'bridging_the_gap_action_plans';

    protected $fillable = [
        'bridging_the_gap_id',
        'problem',
        'solution',
        'action_needed',
        'who_is_responsible',
        'timeline',
        'serial_number',
    ];

    protected $casts = [
        'serial_number' => 'integer',
    ];

    public function bridgingTheGap(): BelongsTo
    {
        return $this->belongsTo(BridgingTheGap::class, 'bridging_the_gap_id');
    }
}
