<?php

namespace App\Models;

use App\Traits\HasUniqueFormId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChildLineList extends Model
{
    use HasUniqueFormId;

    protected $table = 'child_line_lists';

    protected $fillable = [
        'unique_id',
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
        'ip_address',
        'device_info',
        'started_at',
        'submitted_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'age_in_months' => 'integer',
        'missed_vaccines' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
        'device_info' => 'array',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
