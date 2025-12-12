<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUniqueFormId
{
    /**
     * Boot the trait and register creating event.
     */
    protected static function bootHasUniqueFormId(): void
    {
        static::creating(function ($model) {
            if (empty($model->unique_id)) {
                $model->unique_id = $model->generateUniqueFormId();
            }
        });
    }

    /**
     * Generate a unique form ID based on the model type.
     * Format: {prefix}-{short-uuid}
     * Example: AM-A1B2C3D4, DL-E5F6G7H8
     */
    public function generateUniqueFormId(): string
    {
        $prefix = $this->getFormIdPrefix();
        $uuid = strtoupper(Str::random(8));
        
        return "{$prefix}-{$uuid}";
    }

    /**
     * Get the prefix for the form ID based on model class.
     */
    protected function getFormIdPrefix(): string
    {
        $prefixes = [
            'AreaMapping' => 'AM',
            'DraftList' => 'DL',
            'ReligiousLeader' => 'RL',
            'CommunityBarrier' => 'CB',
            'HealthcareBarrier' => 'HB',
        ];

        $className = class_basename($this);
        
        return $prefixes[$className] ?? 'FM';
    }
}
