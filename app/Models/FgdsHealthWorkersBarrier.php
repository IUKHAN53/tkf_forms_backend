<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FgdsHealthWorkersBarrier extends Model
{
    protected $table = 'fgds_health_workers_barriers';

    protected $fillable = [
        'fgds_health_workers_id',
        'barrier_category_id',
        'barrier_text',
        'serial_number',
    ];

    protected $casts = [
        'serial_number' => 'integer',
    ];

    public function fgdsHealthWorkers(): BelongsTo
    {
        return $this->belongsTo(FgdsHealthWorkers::class, 'fgds_health_workers_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BarrierCategory::class, 'barrier_category_id');
    }
}
