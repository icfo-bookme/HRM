<?php

use Illuminate\Support\Facades\Route;
use Modules\SalaryGrade\Http\Controllers\SalaryGradeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('salarygrades', SalaryGradeController::class)->names('salarygrade');
});
