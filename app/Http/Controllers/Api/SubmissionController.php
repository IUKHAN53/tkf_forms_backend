<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Actions\ProcessFormSubmission;
use App\Http\Requests\FormSubmissionRequest;
use App\Http\Resources\FormSubmissionResource;
use App\Models\Form;
use Illuminate\Http\JsonResponse;

class SubmissionController extends Controller
{
    public function __construct(private readonly ProcessFormSubmission $action)
    {
    }

    public function store(FormSubmissionRequest $request, Form $form): JsonResponse
    {
        $payload = $request->validated();

        $data = ($payload['data'] ?? []) + [
            'latitude' => $payload['latitude'] ?? null,
            'longitude' => $payload['longitude'] ?? null,
        ];

        $submission = $this->action->handle($form, $data, $request->user()?->id);

        return (new FormSubmissionResource($submission))->response()->setStatusCode(201);
    }
}
