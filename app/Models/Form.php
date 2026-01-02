<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Form extends Model
{
    /** @use HasFactory<\Database\Factories\FormFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'version',
        'is_active',
        'has_participants',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'has_participants' => 'bool',
    ];

    /**
     * The "booted" method of the model - cascade delete related records
     */
    protected static function booted(): void
    {
        static::deleting(function (Form $form) {
            // Delete all related submissions (this will also cascade to participants)
            $form->submissions()->delete();
            // Delete all related form fields
            $form->fields()->delete();
        });
    }

    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class)->orderBy('order');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
