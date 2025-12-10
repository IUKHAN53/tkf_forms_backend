<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FormController as AdminFormController;
use App\Http\Controllers\Admin\SubmissionController as AdminSubmissionController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ActivityLogController;

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
});
