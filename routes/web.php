<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FormController as AdminFormController;
use App\Http\Controllers\Admin\SubmissionController as AdminSubmissionController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\BridgingTheGapController;
use App\Http\Controllers\Admin\FgdsCommunityController;
use App\Http\Controllers\Admin\FgdsHealthWorkersController;
use App\Http\Controllers\Admin\ChildLineListController;
use App\Http\Controllers\Admin\UcController;
use App\Http\Controllers\Admin\DebugController;

Route::get('/', function () {
    return redirect('/login');
});

// Authentication routes
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'activity.log'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chartData');

    // UC Detail Pages
    Route::get('/uc/{slug}', [UcController::class, 'show'])->name('uc.show');
    Route::get('/uc/{slug}/data', [UcController::class, 'getData'])->name('uc.data');

    Route::resource('forms', AdminFormController::class);
    Route::resource('submissions', AdminSubmissionController::class)->only(['index', 'show', 'destroy']);
    Route::resource('users', AdminUserController::class)->except(['show']);
    Route::get('logs', [ActivityLogController::class, 'index'])->name('logs.index');

    // Core Forms - Child Line List
    Route::get('child-line-list/export', [ChildLineListController::class, 'export'])->name('child-line-list.export');
    Route::get('child-line-list/template', [ChildLineListController::class, 'template'])->name('child-line-list.template');
    Route::post('child-line-list/import', [ChildLineListController::class, 'import'])->name('child-line-list.import');
    Route::resource('child-line-list', ChildLineListController::class);

    // Core Forms - FGDs-Community
    Route::get('fgds-community/export', [FgdsCommunityController::class, 'export'])->name('fgds-community.export');
    Route::get('fgds-community/template', [FgdsCommunityController::class, 'template'])->name('fgds-community.template');
    Route::get('fgds-community/barriers-sample', [FgdsCommunityController::class, 'barriersSample'])->name('fgds-community.barriers-sample');
    Route::post('fgds-community/import', [FgdsCommunityController::class, 'import'])->name('fgds-community.import');
    Route::post('fgds-community/{id}/barriers', [FgdsCommunityController::class, 'uploadBarriers'])->name('fgds-community.upload-barriers');
    Route::resource('fgds-community', FgdsCommunityController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);

    // Core Forms - FGDs-Health Workers
    Route::get('fgds-health-workers/export', [FgdsHealthWorkersController::class, 'export'])->name('fgds-health-workers.export');
    Route::get('fgds-health-workers/template', [FgdsHealthWorkersController::class, 'template'])->name('fgds-health-workers.template');
    Route::get('fgds-health-workers/barriers-sample', [FgdsHealthWorkersController::class, 'barriersSample'])->name('fgds-health-workers.barriers-sample');
    Route::post('fgds-health-workers/import', [FgdsHealthWorkersController::class, 'import'])->name('fgds-health-workers.import');
    Route::post('fgds-health-workers/{id}/barriers', [FgdsHealthWorkersController::class, 'uploadBarriers'])->name('fgds-health-workers.upload-barriers');
    Route::resource('fgds-health-workers', FgdsHealthWorkersController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);

    // Core Forms - Bridging The Gap
    Route::get('bridging-the-gap/export', [BridgingTheGapController::class, 'export'])->name('bridging-the-gap.export');
    Route::get('bridging-the-gap/template', [BridgingTheGapController::class, 'template'])->name('bridging-the-gap.template');
    Route::get('bridging-the-gap/action-plan-sample', [BridgingTheGapController::class, 'actionPlanSample'])->name('bridging-the-gap.action-plan-sample');
    Route::post('bridging-the-gap/import', [BridgingTheGapController::class, 'import'])->name('bridging-the-gap.import');
    Route::post('bridging-the-gap/{id}/action-plan', [BridgingTheGapController::class, 'uploadActionPlan'])->name('bridging-the-gap.upload-action-plan');
    Route::resource('bridging-the-gap', BridgingTheGapController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);

    // Outreach Sites Management
    Route::get('outreach-sites/export', [\App\Http\Controllers\Admin\OutreachSiteController::class, 'export'])->name('outreach-sites.export');
    Route::get('outreach-sites/template', [\App\Http\Controllers\Admin\OutreachSiteController::class, 'template'])->name('outreach-sites.template');
    Route::post('outreach-sites/import', [\App\Http\Controllers\Admin\OutreachSiteController::class, 'import'])->name('outreach-sites.import');
    Route::resource('outreach-sites', \App\Http\Controllers\Admin\OutreachSiteController::class);
});

Route::middleware(['auth', 'activity.log'])->group(function () {
    Route::get('/debug', [DebugController::class, 'index'])->name('debug.index');
    Route::post('/debug', [DebugController::class, 'store'])->name('debug.store');
});
