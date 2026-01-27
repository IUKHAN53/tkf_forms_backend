<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FgdsCommunityBarrier extends Model
{
    protected $table = 'fgds_community_barriers';

    protected $fillable = [
        'fgds_community_id',
        'barrier_category_id',
        'barrier_text',
        'serial_number',
    ];

    protected $casts = [
        'serial_number' => 'integer',
    ];

    public function fgdsCommunity(): BelongsTo
    {
        return $this->belongsTo(FgdsCommunity::class, 'fgds_community_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BarrierCategory::class, 'barrier_category_id');
    }
}
