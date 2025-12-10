<?php

declare(strict_types=1);

namespace App\DTOs;

class FormSubmissionData
{
    public function __construct(
        public readonly int $formId,
        public readonly ?int $userId,
        public readonly array $data,
        public readonly ?float $latitude,
        public readonly ?float $longitude,
    ) {
    }
}
