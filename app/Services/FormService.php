<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Form;
use Illuminate\Database\Eloquent\Collection;

class FormService
{
    public function listActive(): Collection
    {
        return Form::query()
            ->active()
            ->with(['fields'])
            ->orderBy('name')
            ->get();
    }

    public function findWithFields(int $id): ?Form
    {
        return Form::query()
            ->with(['fields'])
            ->find($id);
    }
}
