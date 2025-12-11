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
        'coordinates',
        'comments',
    ];
}
