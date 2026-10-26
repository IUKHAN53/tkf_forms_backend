<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A supporting activity picture attached to an action-plan item as evidence of
 * implementation. Stored on the private disk; served via
 * admin.bridging-the-gap.action-plan-photos.show (auth required).
 */
class BridgingTheGapActionPlanPhoto extends Model
{
    public const DISK = 'local';

    protected $table = 'bridging_the_gap_action_plan_photos';

    protected $fillable = [
        'action_plan_id',
        'path',
        'original_name',
        'mime_type',
        'size',
        'uploaded_by',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    protected $hidden = ['path'];

    protected $appends = ['url'];

    public function actionPlan(): BelongsTo
    {
        return $this->belongsTo(BridgingTheGapActionPlan::class, 'action_plan_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUrlAttribute(): string
    {
        return route('admin.bridging-the-gap.action-plan-photos.show', $this->id);
    }
}
