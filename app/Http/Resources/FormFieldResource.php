<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormFieldResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'form_id' => $this->form_id,
            'label' => $this->label,
            'name' => $this->name,
            'type' => $this->type,
            'required' => $this->required,
            'options' => $this->options,
            'validation_rules' => $this->validation_rules,
            'order' => $this->order,
        ];
    }
}
