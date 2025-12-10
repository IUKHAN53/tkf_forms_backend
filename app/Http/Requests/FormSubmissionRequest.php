<?php

namespace App\Http\Requests;

use App\Enums\FormFieldType;
use App\Models\Form;
use Illuminate\Foundation\Http\FormRequest;

class FormSubmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $form = $this->route('form');

        return $form instanceof Form ? $form->is_active : true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $form = $this->route('form');
        $rules = [
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'data' => ['required', 'array'],
        ];

        if ($form instanceof Form) {
            foreach ($form->fields as $field) {
                $fieldRules = $field->validation_rules ?? [];
                $fieldRules[] = $field->required ? 'required' : 'nullable';

                switch ($field->type) {
                    case FormFieldType::Number:
                        $fieldRules[] = 'numeric';
                        break;
                    case FormFieldType::Textarea:
                    case FormFieldType::Text:
                        $fieldRules[] = 'string';
                        break;
                    case FormFieldType::Select:
                    case FormFieldType::Radio:
                        $options = collect($field->options ?? [])->pluck('value')->filter()->all();
                        if ($options) {
                            $fieldRules[] = 'in:'.implode(',', $options);
                        }
                        break;
                    case FormFieldType::Checkbox:
                        $fieldRules[] = 'boolean';
                        break;
                    case FormFieldType::Date:
                        $fieldRules[] = 'date';
                        break;
                    case FormFieldType::Image:
                    case FormFieldType::Signature:
                        $fieldRules[] = 'file';
                        $fieldRules[] = 'nullable';
                        break;
                }

                $rules['data.'.$field->name] = $fieldRules;
            }
        }

        return $rules;
    }
}
