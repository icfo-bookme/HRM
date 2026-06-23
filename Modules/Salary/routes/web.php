<?php

use Illuminate\Support\Facades\Route;
use Modules\Salary\Http\Controllers\SalaryController;
use Modules\Salary\Http\Controllers\SalaryComponentController;
use Modules\Salary\Http\Controllers\EmployeeSalaryStructureController;
use Modules\Salary\Http\Controllers\PayrollRunController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('salaries', SalaryController::class)->names('salary');

    // Salary Components Routes
    Route::resource('salary-components', SalaryComponentController::class)->names('salary-components');
    Route::get('/dataTable/salary-components', [SalaryComponentController::class, 'dataTable'])->name('salary-components.dataTable');

    // Employee Salary Structure Routes
    Route::resource('employee-salary-structure', EmployeeSalaryStructureController::class)->names('employee-salary-structure');
    Route::get('/dataTable/employee-salary-structure', [EmployeeSalaryStructureController::class, 'dataTable'])->name('employee-salary-structure.dataTable');

    // Payroll Runs Routes
    Route::resource('payroll-runs', PayrollRunController::class)->names('payroll-runs')->except(['update', 'edit', 'show']);
    Route::get('/dataTable/payroll-runs', [PayrollRunController::class, 'dataTable'])->name('payroll-runs.dataTable');
    Route::get('/payroll-runs/generate/new', [PayrollRunController::class, 'generate'])->name('payroll-runs.generate');
    Route::get('/payroll-runs/preview', [PayrollRunController::class, 'preview'])->name('payroll-runs.preview');
    Route::get('/payroll-runs/{id}/show-generated', [PayrollRunController::class, 'showGenerated'])->name('payroll-runs.show-generated');
    Route::post('/payroll-runs/{id}/recalculate', [PayrollRunController::class, 'recalculate'])->name('payroll-runs.recalculate');
    Route::post('/payroll-runs/{id}/approve', [PayrollRunController::class, 'approve'])->name('payroll-runs.approve');
    Route::post('/payroll-runs/{id}/lock', [PayrollRunController::class, 'lock'])->name('payroll-runs.lock');
});