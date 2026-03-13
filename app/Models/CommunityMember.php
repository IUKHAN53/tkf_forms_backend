<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class CommunityMember extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'participant_id',
        'name',
        'phone',
        'password',
        'district',
        'uc',
        'fix_site',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function vaccinationRecords()
    {
        return $this->hasMany(VaccinationRecord::class);
    }
}
