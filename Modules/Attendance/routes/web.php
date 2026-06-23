<?php

use Illuminate\Support\Facades\Route;
use Modules\Attendance\Http\Controllers\AttendanceController;
use Modules\Attendance\Http\Controllers\AttendanceDeviceController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('attendances', AttendanceController::class)->names('attendance');

    // DataTable Routes
    Route::get('/dataTable/attendances', [AttendanceController::class, 'dataTable'])->name('attendance.dataTable');

    // Attendance Devices Routes
    Route::resource('attendance/devices', AttendanceDeviceController::class)->names('attendance.devices');
    Route::get('/dataTable/attendance-devices', [AttendanceDeviceController::class, 'dataTable'])->name('attendance.devices.dataTable');
});
