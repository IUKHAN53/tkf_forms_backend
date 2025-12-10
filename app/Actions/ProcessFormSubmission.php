<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\FormSubmissionData;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Services\SubmissionService;

class ProcessFormSubmission
{
    public function __construct(private readonly SubmissionService $service)
    {
    }

    public function handle(Form $form, array $payload, ?int $userId = null): FormSubmission
    {
        $dto = new FormSubmissionData(
            formId: $form->id,
            userId: $userId,
            data: $payload,
            latitude: $payload['latitude'] ?? null,
            longitude: $payload['longitude'] ?? null,
        );

        return $this->service->create($dto);
    }
}
