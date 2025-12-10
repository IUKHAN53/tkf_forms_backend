<?php

namespace App\Models;

use App\Enums\FormFieldType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormField extends Model
{
    /** @use HasFactory<\Database\Factories\FormFieldFactory> */
    use HasFactory;

    protected $fillable = [
        'form_id',
        'label',
        'name',
        'type',
        'required',
        'options',
        'validation_rules',
        'order',
    ];

    protected $casts = [
        'required' => 'bool',
        'options' => 'array',
        'validation_rules' => 'array',
        'type' => FormFieldType::class,
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}
