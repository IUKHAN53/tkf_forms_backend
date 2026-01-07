<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BridgingTheGapController;
use App\Http\Controllers\Api\ChildLineListController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FgdsCommunityController;
use App\Http\Controllers\Api\FgdsHealthWorkersController;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\FormIdController;
use App\Http\Controllers\Api\LogController;
use App\Http\Controllers\Api\OutreachSiteController;
use App\Http\Controllers\Api\SubmissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/login', [AuthController::class, 'login']);

    // Form Builder Forms (public listing)
    Route::get('/forms', [FormController::class, 'index']);
    Route::get('/forms/{form}', [FormController::class, 'show']);

    // Outreach Sites (public for dropdowns)
    Route::get('/outreach-sites', [OutreachSiteController::class, 'index']);
    Route::get('/outreach-sites/districts', [OutreachSiteController::class, 'districts']);
    Route::get('/outreach-sites/union-councils', [OutreachSiteController::class, 'unionCouncils']);
    Route::get('/outreach-sites/fix-sites', [OutreachSiteController::class, 'fixSites']);
    Route::get('/outreach-sites/outreach', [OutreachSiteController::class, 'outreachSites']);

    Route::middleware('auth:sanctum')->group(function (): void {
        // Dashboard Statistics
        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

        // Generate unique form ID
        Route::post('/form-id/generate', [FormIdController::class, 'generate']);

        // Profile & Password
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);

        // App logs
        Route::post('/logs', [LogController::class, 'store']);

        // Form Builder submissions
        Route::post('/forms/{form}/submit', [SubmissionController::class, 'store']);

        // Outreach Sites - create new
        Route::post('/outreach-sites', [OutreachSiteController::class, 'store']);

        // Core Forms - Child Line List (formerly Draft Lists)
        Route::get('/child-line-lists', [ChildLineListController::class, 'index']);
        Route::post('/child-line-lists', [ChildLineListController::class, 'store']);
        Route::get('/child-line-lists/{childLineList}', [ChildLineListController::class, 'show']);

        // Core Forms - FGDs-Community (formerly Community Barriers)
        Route::get('/fgds-community', [FgdsCommunityController::class, 'index']);
        Route::post('/fgds-community', [FgdsCommunityController::class, 'store']);
        Route::get('/fgds-community/{fgdsCommunity}', [FgdsCommunityController::class, 'show']);

        // Core Forms - FGDs-Health Workers (formerly Healthcare Barriers)
        Route::get('/fgds-health-workers', [FgdsHealthWorkersController::class, 'index']);
        Route::post('/fgds-health-workers', [FgdsHealthWorkersController::class, 'store']);
        Route::get('/fgds-health-workers/{fgdsHealthWorker}', [FgdsHealthWorkersController::class, 'show']);

        // Core Forms - Bridging The Gap
        Route::get('/bridging-the-gap', [BridgingTheGapController::class, 'index']);
        Route::post('/bridging-the-gap', [BridgingTheGapController::class, 'store']);
        Route::get('/bridging-the-gap/search-participants', [BridgingTheGapController::class, 'searchParticipants']);
        Route::get('/bridging-the-gap/{bridgingTheGap}', [BridgingTheGapController::class, 'show']);
    });
});
