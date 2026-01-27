<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BarrierCategory extends Model
{
    protected $table = 'barrier_categories';

    protected $fillable = [
        'name',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function communityBarriers(): HasMany
    {
        return $this->hasMany(FgdsCommunityBarrier::class, 'barrier_category_id');
    }

    /**
     * Get categories ordered by sort_order
     */
    public static function ordered()
    {
        return static::orderBy('sort_order')->get();
    }
}
