<?php

use Illuminate\Support\Facades\Route;
use Modules\Holidays\Http\Controllers\HolidaysController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Holidays CRUD
    Route::resource('holidays', HolidaysController::class)->names('holidays');
    Route::get('/dataTable/holidays', [HolidaysController::class, 'dataTable'])->name('holidays.dataTable');

    // Holiday Calendar
    Route::get('/holiday-calendar', [HolidaysController::class, 'calendar'])->name('holidays.calendar');
    Route::get('/holiday-calendar/data', [HolidaysController::class, 'calendarData'])->name('holidays.calendar.data');
    Route::post('/holiday-calendar/store', [HolidaysController::class, 'calendarStore'])->name('holidays.calendar.store');

    // Holiday Assignments (separate prefix to avoid resource route conflict)
    Route::get('/holiday-assignments', [HolidaysController::class, 'assignIndex'])->name('holidays.assign.index');
    Route::get('/dataTable/holiday-assignments', [HolidaysController::class, 'assignDataTable'])->name('holidays.assign.dataTable');
    Route::post('/holiday-assignments', [HolidaysController::class, 'assignStore'])->name('holidays.assign.store');
    Route::get('/holiday-assignments/{id}', [HolidaysController::class, 'assignShow'])->name('holidays.assign.show');
    Route::put('/holiday-assignments/{id}', [HolidaysController::class, 'assignUpdate'])->name('holidays.assign.update');
    Route::delete('/holiday-assignments/{id}', [HolidaysController::class, 'assignDestroy'])->name('holidays.assign.destroy');
});
