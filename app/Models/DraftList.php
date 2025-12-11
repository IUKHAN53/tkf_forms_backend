<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DraftList extends Model
{
    protected $fillable = [
        'user_id',
        'division',
        'district',
        'town',
        'uc',
        'outreach',
        'child_name',
        'father_name',
        'gender',
        'date_of_birth',
        'age_in_months',
        'father_cnic',
        'house_number',
        'address',
        'guardian_phone',
        'type',
        'missed_vaccines',
        'reasons_of_missing',
        'plan_for_coverage',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'age_in_months' => 'integer',
        'missed_vaccines' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
