<?php

use Illuminate\Support\Facades\Route;
use Modules\Leave\Http\Controllers\LeaveApplicationController;
use Modules\Leave\Http\Controllers\LeaveEncashmentController;
use Modules\Leave\Http\Controllers\LeaveTypeController;
Route::middleware(['auth', 'verified'])->group(function () {

    // Leave Types
    Route::controller(LeaveTypeController::class)->prefix('leave-types')->name('leave-types.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/dataTable', 'dataTable')->name('dataTable');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{leavetype}/edit', 'edit')->name('edit');
        Route::put('/{leavetype}', 'update')->name('update');
        Route::delete('/{leavetype}', 'destroy')->name('destroy');
    });

    // Leave Applications
    Route::resource('leave-applications', LeaveApplicationController::class)->names('leave-applications');
    Route::get('/dataTable/leave-applications', [LeaveApplicationController::class, 'dataTable'])->name('leave-applications.dataTable');
    Route::get('/my-leave-applications', [LeaveApplicationController::class, 'my'])->name('leave-applications.my');
    Route::get('/dataTable/my-leave-applications', [LeaveApplicationController::class, 'myDataTable'])->name('leave-applications.myDataTable');
    Route::get('/leave-applications/{leave_application}/check-balance', [LeaveApplicationController::class, 'checkApprovalBalance'])->name('leave-applications.check-balance');
    Route::post('/leave-applications/{leave_application}/approve', [LeaveApplicationController::class, 'approve'])->name('leave-applications.approve');
    Route::post('/leave-applications/{leave_application}/disapprove', [LeaveApplicationController::class, 'disapprove'])->name('leave-applications.disapprove');
    Route::post('/leave-applications/{leave_application}/reject', [LeaveApplicationController::class, 'reject'])->name('leave-applications.reject');

    // Leave Encashment
    Route::resource('leave-encashment', LeaveEncashmentController::class)->names('leave-encashment');
    Route::get('/dataTable/leave-encashment', [LeaveEncashmentController::class, 'dataTable'])->name('leave-encashment.dataTable');
    Route::post('/leave-encashment/{leave_encashment}/approve', [LeaveEncashmentController::class, 'approve'])->name('leave-encashment.approve');
    Route::get('/leave-encashment/balance', [LeaveEncashmentController::class, 'getBalance'])->name('leave-encashment.balance');
});
