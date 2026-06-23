<?php

use Illuminate\Support\Facades\Route;
use Modules\Setting\Http\Controllers\FiscalYearController;
use Modules\Setting\Http\Controllers\SettingController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('settings', SettingController::class)->names('setting');

    // Fiscal Years
    Route::resource('fiscal-years', FiscalYearController::class)->names('fiscal-years');
    Route::get('/dataTable/fiscal-years', [FiscalYearController::class, 'dataTable'])->name('fiscal-years.dataTable');
});
