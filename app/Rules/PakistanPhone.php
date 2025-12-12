<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PakistanPhone implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Pakistan phone format: 03XXXXXXXXX (11 digits starting with 03)
        if (!preg_match('/^03\d{9}$/', $value)) {
            $fail("The {$attribute} must be a valid Pakistani phone number (03XXXXXXXXX).");
        }
    }
}
