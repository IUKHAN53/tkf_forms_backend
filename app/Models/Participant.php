<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Participant extends Model
{
    protected $fillable = [
        'participantable_id',
        'participantable_type',
        'sr_no',
        'name',
        'title_designation',
        'designation',
        'occupation',
        'address',
        'contact_no',
        'cnic',
        'gender',
    ];

    protected $casts = [
        'sr_no' => 'integer',
    ];

    public function participantable(): MorphTo
    {
        return $this->morphTo();
    }
}
