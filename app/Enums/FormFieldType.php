<?php

declare(strict_types=1);

namespace App\Enums;

enum FormFieldType: string
{
    case Text = 'text';
    case Number = 'number';
    case Textarea = 'textarea';
    case Select = 'select';
    case Checkbox = 'checkbox';
    case Radio = 'radio';
    case Date = 'date';
    case Image = 'image';
    case Signature = 'signature';
}
