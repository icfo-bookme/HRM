<?php

use Illuminate\Support\Facades\Route;
use Modules\Leave\Http\Controllers\LeaveController;
use Modules\Leave\Http\Controllers\LeaveTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('leaves', LeaveController::class)->names('leave');

    // Leave Types
    Route::get('leave-types/active-list', [LeaveTypeController::class, 'activeList'])->name('leave-types.active-list');
    Route::post('leave-types/{id}/restore', [LeaveTypeController::class, 'restore'])->name('leave-types.restore');
    Route::delete('leave-types/{id}/force-delete', [LeaveTypeController::class, 'forceDelete'])->name('leave-types.force-delete');
    Route::apiResource('leave-types', LeaveTypeController::class)->names('leave-types');
});
