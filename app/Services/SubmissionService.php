<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\FormSubmissionData;
use App\Enums\FormFieldType;
use App\Models\FormSubmission;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

class SubmissionService
{
    public function create(FormSubmissionData $dto): FormSubmission
    {
        $submission = FormSubmission::create([
            'form_id' => $dto->formId,
            'user_id' => $dto->userId,
            'data' => $this->extractPlainData($dto->data),
            'latitude' => $dto->latitude,
            'longitude' => $dto->longitude,
        ]);

        $this->attachMedia($submission, $dto->data);

        return $submission->load('form');
    }

    private function extractPlainData(array $input): array
    {
        // Strip UploadedFile instances from stored data; media is stored separately
        return collect($input)
            ->map(fn ($value) => $value instanceof UploadedFile ? null : $value)
            ->all();
    }

    private function attachMedia(FormSubmission $submission, array $data): void
    {
        foreach ($data as $key => $value) {
            if ($value instanceof UploadedFile) {
                $submission
                    ->addMedia($value)
                    ->usingName((string) $key)
                    ->usingFileName($value->getClientOriginalName())
                    ->toMediaCollection($key);
            }
        }
    }
}
