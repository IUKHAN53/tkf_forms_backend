<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\FormSubmissionData;
use App\Enums\FormFieldType;
use App\Models\FormSubmission;
use App\Models\FormSubmissionParticipant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

class SubmissionService
{
    public function create(FormSubmissionData $dto): FormSubmission
    {
        // Extract participants from data if present
        $participants = $dto->data['participants'] ?? [];
        $dataWithoutParticipants = Arr::except($dto->data, ['participants']);

        $submission = FormSubmission::create([
            'form_id' => $dto->formId,
            'user_id' => $dto->userId,
            'data' => $this->extractPlainData($dataWithoutParticipants),
            'latitude' => $dto->latitude,
            'longitude' => $dto->longitude,
        ]);

        $this->attachMedia($submission, $dataWithoutParticipants);
        $this->createParticipants($submission, $participants);

        return $submission->load('form', 'participants');
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

    private function createParticipants(FormSubmission $submission, array $participants): void
    {
        foreach ($participants as $index => $participant) {
            FormSubmissionParticipant::create([
                'form_submission_id' => $submission->id,
                'sr_no' => $participant['sr_no'] ?? ($index + 1),
                'name' => $participant['name'] ?? '',
                'title_designation' => $participant['title_designation'] ?? $participant['designation'] ?? null,
                'occupation' => $participant['occupation'] ?? null,
                'address' => $participant['address'] ?? null,
                'contact_no' => $participant['contact_no'] ?? $participant['contact_number'] ?? null,
                'cnic' => $participant['cnic'] ?? null,
                'gender' => $participant['gender'] ?? null,
            ]);
        }
    }
}
