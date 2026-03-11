<?php

namespace App\Models;

use App\Traits\HasUniqueFormId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VaccinationRecord extends Model
{
    use HasUniqueFormId;

    protected $table = 'vaccination_records';

    protected $fillable = [
        'unique_id',
        'user_id',
        'community_member_id',
        'fix_site',
        'uc',
        'district',
        'serial_number',
        'child_name',
        'father_name',
        'age',
        'address',
        'contact_number',
        'category',
        'vaccinated',
        'date_of_vaccination',
        'community_member_name',
        'community_member_contact',
        'gps_coordinates',
        'latitude',
        'longitude',
        'ip_address',
        'device_info',
        'started_at',
        'submitted_at',
    ];

    protected $casts = [
        'serial_number' => 'integer',
        'date_of_vaccination' => 'date',
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

    public function communityMember(): BelongsTo
    {
        return $this->belongsTo(CommunityMember::class);
    }
}
