<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;

abstract class Controller
{
    /**
     * The record a mobile submit is re-sending, if we already stored it.
     *
     * Submits carry a client-generated unique_id and the app retries when a
     * response is lost, so a repeat of an id we already hold is the same
     * submission, not a new one. Without this the retry hits the unique index
     * and the app gets a 500 for a record that actually saved.
     */
    protected function alreadySubmitted(string $model, ?string $uniqueId, array $with = []): ?Model
    {
        if (blank($uniqueId)) {
            return null;
        }

        return $model::with($with)->where('unique_id', $uniqueId)->first();
    }
}
