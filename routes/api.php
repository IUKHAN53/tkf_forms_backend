<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\SubmissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/forms', [FormController::class, 'index']);
    Route::get('/forms/{form}', [FormController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/forms/{form}/submit', [SubmissionController::class, 'store']);
    });
});
