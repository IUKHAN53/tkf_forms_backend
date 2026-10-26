<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class BridgingTheGapActionPlan extends Model
{
    protected $table = 'bridging_the_gap_action_plans';

    /** Implementation statuses, in workflow order: value => label. */
    public const STATUSES = [
        'not_started' => 'Not Started',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
    ];

    public const DEFAULT_STATUS = 'not_started';

    protected $fillable = [
        'bridging_the_gap_id',
        'problem',
        'sub_cause',
        'root_cause',
        'solution',
        'action_needed',
        'who_is_responsible',
        'timeline',
        'serial_number',
        'implementation_status',
        'implementation_remarks',
        'status_updated_at',
    ];

    protected $casts = [
        'serial_number' => 'integer',
        'status_updated_at' => 'datetime',
    ];

    protected $attributes = [
        'implementation_status' => self::DEFAULT_STATUS,
    ];

    protected $appends = ['implementation_status_label'];

    public function bridgingTheGap(): BelongsTo
    {
        return $this->belongsTo(BridgingTheGap::class, 'bridging_the_gap_id');
    }

    /** Supporting activity pictures (implementation evidence). */
    public function photos(): HasMany
    {
        return $this->hasMany(BridgingTheGapActionPlanPhoto::class, 'action_plan_id')->orderBy('id');
    }

    public function getImplementationStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->implementation_status] ?? self::STATUSES[self::DEFAULT_STATUS];
    }

    /**
     * Delete action plans together with their picture files. Query-builder
     * deletes skip model events and the FK cascade only removes the photo rows,
     * so every bulk delete path must go through here or the files are orphaned.
     */
    public static function purge($query): int
    {
        $ids = (clone $query)->pluck('id');
        if ($ids->isEmpty()) {
            return 0;
        }

        $photos = BridgingTheGapActionPlanPhoto::whereIn('action_plan_id', $ids)->get(['id', 'path']);
        foreach ($photos as $photo) {
            Storage::disk(BridgingTheGapActionPlanPhoto::DISK)->delete($photo->path);
        }
        BridgingTheGapActionPlanPhoto::whereIn('action_plan_id', $ids)->delete();

        return static::whereIn('id', $ids)->delete();
    }
}
