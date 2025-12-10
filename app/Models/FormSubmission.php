<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class FormSubmission extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\FormSubmissionFactory> */
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'form_id',
        'user_id',
        'data',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'data' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
