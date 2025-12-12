<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FormController as AdminFormController;
use App\Http\Controllers\Admin\SubmissionController as AdminSubmissionController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AreaMappingController;
use App\Http\Controllers\Admin\DraftListController;
use App\Http\Controllers\Admin\ReligiousLeaderController;
use App\Http\Controllers\Admin\CommunityBarrierController;
use App\Http\Controllers\Admin\HealthcareBarrierController;

Route::get('/', function () {
    return redirect('/admin');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::prefix('admin')->name('admin.')->middleware('activity.log')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('forms', AdminFormController::class);
    Route::resource('submissions', AdminSubmissionController::class)->only(['index', 'show', 'destroy']);
    Route::resource('users', AdminUserController::class)->except(['show']);
    Route::get('logs', [ActivityLogController::class, 'index'])->name('logs.index');

    // Core Forms - Area Mappings
    Route::get('area-mappings/export', [AreaMappingController::class, 'export'])->name('area-mappings.export');
    Route::get('area-mappings/template', [AreaMappingController::class, 'template'])->name('area-mappings.template');
    Route::post('area-mappings/import', [AreaMappingController::class, 'import'])->name('area-mappings.import');
    Route::resource('area-mappings', AreaMappingController::class)->only(['index', 'show', 'destroy']);

    // Core Forms - Draft Lists
    Route::get('draft-lists/export', [DraftListController::class, 'export'])->name('draft-lists.export');
    Route::get('draft-lists/template', [DraftListController::class, 'template'])->name('draft-lists.template');
    Route::post('draft-lists/import', [DraftListController::class, 'import'])->name('draft-lists.import');
    Route::resource('draft-lists', DraftListController::class);

    // Core Forms - Religious Leaders
    Route::get('religious-leaders/export', [ReligiousLeaderController::class, 'export'])->name('religious-leaders.export');
    Route::get('religious-leaders/template', [ReligiousLeaderController::class, 'template'])->name('religious-leaders.template');
    Route::post('religious-leaders/import', [ReligiousLeaderController::class, 'import'])->name('religious-leaders.import');
    Route::resource('religious-leaders', ReligiousLeaderController::class)->only(['index', 'show', 'destroy']);

    // Core Forms - Community Barriers
    Route::get('community-barriers/export', [CommunityBarrierController::class, 'export'])->name('community-barriers.export');
    Route::get('community-barriers/template', [CommunityBarrierController::class, 'template'])->name('community-barriers.template');
    Route::post('community-barriers/import', [CommunityBarrierController::class, 'import'])->name('community-barriers.import');
    Route::resource('community-barriers', CommunityBarrierController::class)->only(['index', 'show', 'destroy']);

    // Core Forms - Healthcare Barriers
    Route::get('healthcare-barriers/export', [HealthcareBarrierController::class, 'export'])->name('healthcare-barriers.export');
    Route::get('healthcare-barriers/template', [HealthcareBarrierController::class, 'template'])->name('healthcare-barriers.template');
    Route::post('healthcare-barriers/import', [HealthcareBarrierController::class, 'import'])->name('healthcare-barriers.import');
    Route::resource('healthcare-barriers', HealthcareBarrierController::class)->only(['index', 'show', 'destroy']);

    // Outreach Sites Management
    Route::get('outreach-sites/export', [\App\Http\Controllers\Admin\OutreachSiteController::class, 'export'])->name('outreach-sites.export');
    Route::get('outreach-sites/template', [\App\Http\Controllers\Admin\OutreachSiteController::class, 'template'])->name('outreach-sites.template');
    Route::post('outreach-sites/import', [\App\Http\Controllers\Admin\OutreachSiteController::class, 'import'])->name('outreach-sites.import');
    Route::resource('outreach-sites', \App\Http\Controllers\Admin\OutreachSiteController::class);
});
