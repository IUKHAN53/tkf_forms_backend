<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormSubmissionResource extends JsonResource
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
            'user_id' => $this->user_id,
            'data' => $this->data,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'media' => $this->whenLoaded('media', fn () => $this->media->map->only(['id', 'collection_name', 'file_name', 'original_url'])),
            'created_at' => $this->created_at,
        ];
    }
}
