<?php

use Illuminate\Support\Facades\Route;
use Modules\SalaryGrade\Http\Controllers\SalaryGradeController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('salarygrades', SalaryGradeController::class)->names('salarygrade');
    Route::get('dataTable/salarygrades', [SalaryGradeController::class, 'dataTable'])->name('salarygrade.dataTable');
});
