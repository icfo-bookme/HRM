<?php

use Illuminate\Support\Facades\Route;
use Modules\Kpi\Http\Controllers\KpiController;
use Modules\Kpi\Http\Controllers\KpiTaskController;
use Modules\Kpi\Http\Controllers\KpiReviewController;

/*
|--------------------------------------------------------------------------
| KPI Module Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('kpi')->name('kpi.')->group(function () {

    // Dashboard
    Route::get('/', [KpiController::class, 'dashboard'])->name('dashboard');

    // Daily Performance
    Route::get('/daily', [KpiController::class, 'dailyPerformance'])->name('daily');

    // Monthly Performance
    Route::get('/monthly', [KpiController::class, 'monthlyPerformance'])->name('monthly');
    Route::get('/monthly/{employee}/{year}/{month}', [KpiController::class, 'monthlyDetail'])->name('monthly.detail');

    // Task Management
    Route::get('/tasks', [KpiTaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/create', [KpiTaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [KpiTaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{id}', [KpiTaskController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/{id}/edit', [KpiTaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{id}', [KpiTaskController::class, 'update'])->name('tasks.update');
    Route::put('/tasks/{id}/complete', [KpiTaskController::class, 'complete'])->name('tasks.complete');
    Route::delete('/tasks/{id}', [KpiTaskController::class, 'destroy'])->name('tasks.destroy');

    // Monthly Reviews (Behavior, Bonus, Penalty)
    Route::get('/reviews', [KpiReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/create/{employee?}', [KpiReviewController::class, 'create'])->name('reviews.create');
    Route::post('/reviews', [KpiReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/{id}', [KpiReviewController::class, 'show'])->name('reviews.show');
    Route::get('/reviews/{id}/edit', [KpiReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{id}', [KpiReviewController::class, 'update'])->name('reviews.update');
    Route::put('/reviews/{id}/submit', [KpiReviewController::class, 'submit'])->name('reviews.submit');
    Route::put('/reviews/{id}/approve', [KpiReviewController::class, 'approve'])->name('reviews.approve');

    // Settings (Admin)
    Route::get('/settings', [KpiController::class, 'settings'])->name('settings');
    Route::post('/settings/categories', [KpiController::class, 'updateCategories'])->name('settings.categories');
    Route::post('/settings/indicators', [KpiController::class, 'updateIndicators'])->name('settings.indicators');
});
