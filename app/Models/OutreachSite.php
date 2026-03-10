<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutreachSite extends Model
{
    protected $fillable = [
        'district',
        'union_council',
        'fix_site',
        'outreach_site',
        'location_hash',
        'coordinates',
        'comments',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->location_hash = $model->generateLocationHash();
        });

        static::updating(function ($model) {
            $model->location_hash = $model->generateLocationHash();
        });
    }

    /**
     * Generate a unique hash from the location fields
     */
    public function generateLocationHash(): string
    {
        return md5(
            strtolower(trim($this->district ?? '')) . '|' .
            strtolower(trim($this->union_council ?? '')) . '|' .
            strtolower(trim($this->fix_site ?? '')) . '|' .
            strtolower(trim($this->outreach_site ?? ''))
        );
    }
}
