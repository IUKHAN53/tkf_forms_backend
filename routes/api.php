<?php

use App\Http\Controllers\Api\AreaMappingController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BridgingTheGapController;
use App\Http\Controllers\Api\CommunityBarrierController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DraftListController;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\FormIdController;
use App\Http\Controllers\Api\HealthcareBarrierController;
use App\Http\Controllers\Api\LogController;
use App\Http\Controllers\Api\OutreachSiteController;
use App\Http\Controllers\Api\ReligiousLeaderController;
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

        // Core Forms - Area Mapping
        Route::get('/area-mappings', [AreaMappingController::class, 'index']);
        Route::post('/area-mappings', [AreaMappingController::class, 'store']);
        Route::get('/area-mappings/{areaMapping}', [AreaMappingController::class, 'show']);

        // Core Forms - Draft List
        Route::get('/draft-lists', [DraftListController::class, 'index']);
        Route::post('/draft-lists', [DraftListController::class, 'store']);
        Route::get('/draft-lists/{draftList}', [DraftListController::class, 'show']);

        // Core Forms - Religious Leaders
        Route::get('/religious-leaders', [ReligiousLeaderController::class, 'index']);
        Route::post('/religious-leaders', [ReligiousLeaderController::class, 'store']);
        Route::get('/religious-leaders/{religiousLeader}', [ReligiousLeaderController::class, 'show']);

        // Core Forms - Community Barriers
        Route::get('/community-barriers', [CommunityBarrierController::class, 'index']);
        Route::post('/community-barriers', [CommunityBarrierController::class, 'store']);
        Route::get('/community-barriers/{communityBarrier}', [CommunityBarrierController::class, 'show']);

        // Core Forms - Healthcare Barriers
        Route::get('/healthcare-barriers', [HealthcareBarrierController::class, 'index']);
        Route::post('/healthcare-barriers', [HealthcareBarrierController::class, 'store']);
        Route::get('/healthcare-barriers/{healthcareBarrier}', [HealthcareBarrierController::class, 'show']);

        // Core Forms - Bridging The Gap
        Route::get('/bridging-the-gap', [BridgingTheGapController::class, 'index']);
        Route::post('/bridging-the-gap', [BridgingTheGapController::class, 'store']);
        Route::get('/bridging-the-gap/search-participants', [BridgingTheGapController::class, 'searchParticipants']);
        Route::get('/bridging-the-gap/{bridgingTheGap}', [BridgingTheGapController::class, 'show']);
    });
});
