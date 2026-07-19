<?php

use Illuminate\Support\Facades\Route;
use Modules\Attendance\Http\Controllers\AttendanceController;
use Modules\Attendance\Http\Controllers\AttendanceDeviceController;
use Modules\Attendance\Http\Controllers\AttendanceReportController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('attendances', AttendanceController::class)->names('attendance');

    // DataTable Routes
    Route::get('/dataTable/attendances', [AttendanceController::class, 'dataTable'])->name('attendance.dataTable');

    // Approval Routes
    Route::post('/attendances/{id}/approve', [AttendanceController::class, 'approve'])->name('attendance.approve');
    Route::post('/attendances/{id}/disapprove', [AttendanceController::class, 'disapprove'])->name('attendance.disapprove');

    // Attendance Devices Routes
    Route::resource('attendance/devices', AttendanceDeviceController::class)->names('attendance.devices');
    Route::get('/dataTable/attendance-devices', [AttendanceDeviceController::class, 'dataTable'])->name('attendance.devices.dataTable');

    // Attendance Report Routes
    Route::get('/attendance/report', [AttendanceReportController::class, 'index'])->name('attendance.report');
    Route::get('/dataTable/attendance-report', [AttendanceReportController::class, 'dataTable'])->name('attendance.report.dataTable');
    
    // Overtime Report Routes
    Route::get('/attendance/overtime-report', [AttendanceReportController::class, 'overtimeIndex'])->name('attendance.overtime');
    Route::get('/dataTable/attendance-overtime', [AttendanceReportController::class, 'overtimeDataTable'])->name('attendance.overtime.dataTable');
});
