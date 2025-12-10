<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FormResource;
use App\Models\Form;
use App\Services\FormService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FormController extends Controller
{
    public function __construct(private readonly FormService $forms)
    {
    }

    public function index(): AnonymousResourceCollection
    {
        return FormResource::collection($this->forms->listActive());
    }

    public function show(Form $form): FormResource
    {
        $form->load('fields');

        return new FormResource($form);
    }
}
