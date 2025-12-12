<?php

namespace App\Models;

use App\Traits\HasUniqueFormId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AreaMapping extends Model
{
    use HasUniqueFormId;

    protected $fillable = [
        'unique_id',
        'user_id',
        'district',
        'town',
        'uc_name',
        'fix_site',
        'outreach_name',
        'outreach_coordinates',
        'area_name',
        'assigned_aic',
        'aic_contact',
        'assigned_cm',
        'cm_contact',
        'total_population',
        'total_under_2_years',
        'total_zero_dose',
        'total_defaulter',
        'total_refusal',
        'total_boys_under_2',
        'total_girls_under_2',
        'major_ethnicity',
        'major_languages',
        'existing_committees',
        'nearest_phf',
        'hf_incharge_name',
        'latitude',
        'longitude',
        'ip_address',
        'device_info',
        'started_at',
        'submitted_at',
    ];

    protected $casts = [
        'total_population' => 'integer',
        'total_under_2_years' => 'integer',
        'total_zero_dose' => 'integer',
        'total_defaulter' => 'integer',
        'total_refusal' => 'integer',
        'total_boys_under_2' => 'integer',
        'total_girls_under_2' => 'integer',
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
