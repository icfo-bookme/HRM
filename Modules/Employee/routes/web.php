<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\Http\Controllers\EmployeeController;
use Modules\Employee\Http\Controllers\EmployeeEditController;
use Modules\Employee\Http\Controllers\EmployeeLeaveBalanceController;
use Modules\Employee\Http\Controllers\SkillCategoryController;

Route::middleware(['auth', 'verified','permission:employees.create'])->group(function () {
    Route::get('employees/create/step-1', [EmployeeController::class, 'createStepOne'])->name('employee.create.step1');
    Route::post('employees/create/step-1', [EmployeeController::class, 'storeStepOne'])->name('employee.store.step1');

    Route::get('employees/create/step-2', [EmployeeController::class, 'createStepTwo'])->name('employee.create.step2');
    Route::post('employees/create/step-2', [EmployeeController::class, 'storeStepTwo'])->name('employee.store.step2');

    Route::get('employees/create/step-3', [EmployeeController::class, 'createStepThree'])->name('employee.create.step3');
    Route::post('employees/create/step-3', [EmployeeController::class, 'storeStepThree'])->name('employee.store.step3');

    Route::get('employees/create/step-4', [EmployeeController::class, 'createStepFour'])->name('employee.create.step4');
    Route::post('employees/create/step-4', [EmployeeController::class, 'storeStepFour'])->name('employee.store.step4');

    Route::get('employees/create/step-5', [EmployeeController::class, 'createStepFive'])->name('employee.create.step5');
    Route::post('employees/create/step-5', [EmployeeController::class, 'storeStepFive'])->name('employee.store.step5');

    Route::get('employees/create/step-6', [EmployeeController::class, 'createStepSix'])->name('employee.create.step6');
    Route::post('employees/create/step-6', [EmployeeController::class, 'storeStepSix'])->name('employee.store.step6');

    Route::get('employees/create/step-7', [EmployeeController::class, 'createStepSeven'])->name('employee.create.step7');
    Route::post('employees/create/step-7', [EmployeeController::class, 'storeStepSeven'])->name('employee.store.step7');

    Route::get('employees/create/step-8', [EmployeeController::class, 'createStepEight'])->name('employee.create.step8');
    Route::post('employees/create/step-8', [EmployeeController::class, 'storeStepEight'])->name('employee.store.step8');

    Route::get('employees/create/step-9', [EmployeeController::class, 'createStepNine'])->name('employee.create.step9');
    Route::post('employees/create/step-9', [EmployeeController::class, 'storeStepNine'])->name('employee.store.step9');

    Route::get('employees/create/step-10', [EmployeeController::class, 'createStepTen'])->name('employee.create.step10');
    Route::post('employees/create/step-10', [EmployeeController::class, 'storeStepTen'])->name('employee.store.step10');

    Route::get('employees/create/step-11', [EmployeeController::class, 'createStepEleven'])->name('employee.create.step11');
    Route::post('employees/create/finalize', [EmployeeController::class, 'finalize'])->name('employee.create.finalize');

    Route::get('employees/create/cancel', [EmployeeController::class, 'cancel'])->name('employee.create.cancel');

    Route::get('employees/{id}/edit', [EmployeeEditController::class, 'edit'])->name('employee.edit');

    // Employee Edit - Section-wise update routes
    Route::put('employees/{id}/basic', [EmployeeEditController::class, 'updateBasic'])->name('employee.edit.basic');
    Route::put('employees/{id}/personal', [EmployeeEditController::class, 'updatePersonal'])->name('employee.edit.personal');
    Route::put('employees/{id}/addresses', [EmployeeEditController::class, 'updateAddresses'])->name('employee.edit.addresses');
    Route::put('employees/{id}/banking', [EmployeeEditController::class, 'updateBanking'])->name('employee.edit.banking');
    Route::put('employees/{id}/documents', [EmployeeEditController::class, 'updateDocuments'])->name('employee.edit.documents');
    Route::put('employees/{id}/education', [EmployeeEditController::class, 'updateEducation'])->name('employee.edit.education');
    Route::put('employees/{id}/experience', [EmployeeEditController::class, 'updateExperience'])->name('employee.edit.experience');
    Route::put('employees/{id}/job-history', [EmployeeEditController::class, 'updateJobHistory'])->name('employee.edit.job-history');
    Route::put('employees/{id}/languages', [EmployeeEditController::class, 'updateLanguages'])->name('employee.edit.languages');
    Route::put('employees/{id}/skills', [EmployeeEditController::class, 'updateSkills'])->name('employee.edit.skills');
    Route::put('employees/{id}/dependents', [EmployeeEditController::class, 'updateDependents'])->name('employee.edit.dependents');

    Route::resource('employees', EmployeeController::class)->names('employee')->except(['create', 'store', 'edit', 'update']);
    Route::get('/dataTable/employees', [EmployeeController::class, 'getEmployeesDataTable'])->name('employee.dataTable');

    // Skill Categories Management
    Route::resource('skill-categories', SkillCategoryController::class)->names('skill-categories');
    Route::get('/dataTable/skill-categories', [SkillCategoryController::class, 'dataTable'])->name('skill-categories.dataTable');

    // Employee Leave Balance Management
    Route::resource('employee-leave-balances', EmployeeLeaveBalanceController::class)->names('employee-leave-balances');
    Route::get('/dataTable/employee-leave-balances', [EmployeeLeaveBalanceController::class, 'dataTable'])->name('employee-leave-balances.dataTable');

    // Employee Weekends
    Route::get('employee-weekends', [\Modules\Employee\Http\Controllers\EmployeeWeekendController::class, 'index'])->name('employee.weekends.index');
    Route::post('employee-weekends', [\Modules\Employee\Http\Controllers\EmployeeWeekendController::class, 'store'])->name('employee.weekends.store');
    Route::get('employee-weekends/{employeeId}', [\Modules\Employee\Http\Controllers\EmployeeWeekendController::class, 'show'])->name('employee.weekends.show');

    // Employee Attendance Rules
    Route::get('employee-attendance-rules', [\Modules\Employee\Http\Controllers\EmployeeAttendanceRuleController::class, 'index'])->name('employee.attendance-rules.index');
    Route::post('employee-attendance-rules', [\Modules\Employee\Http\Controllers\EmployeeAttendanceRuleController::class, 'store'])->name('employee.attendance-rules.store');
    Route::get('employee-attendance-rules/{employeeId}', [\Modules\Employee\Http\Controllers\EmployeeAttendanceRuleController::class, 'show'])->name('employee.attendance-rules.show');

    });

// Employee Report Routes (separate middleware group for wider access)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('employee-report', [\Modules\Employee\Http\Controllers\EmployeeReportController::class, 'index'])->name('employee.report');
    Route::get('employee-report/search', [\Modules\Employee\Http\Controllers\EmployeeReportController::class, 'searchEmployee'])->name('employee.report.search');
    Route::get('employee-report/attendance', [\Modules\Employee\Http\Controllers\EmployeeReportController::class, 'attendanceData'])->name('employee.report.attendance');
    Route::get('employee-report/overtime', [\Modules\Employee\Http\Controllers\EmployeeReportController::class, 'overtimeData'])->name('employee.report.overtime');
    Route::get('employee-report/salary', [\Modules\Employee\Http\Controllers\EmployeeReportController::class, 'salaryData'])->name('employee.report.salary');
    Route::get('employee-report/kpi', [\Modules\Employee\Http\Controllers\EmployeeReportController::class, 'kpiData'])->name('employee.report.kpi');
    Route::get('employee-report/loan', [\Modules\Employee\Http\Controllers\EmployeeReportController::class, 'loanData'])->name('employee.report.loan');
    Route::get('employee-report/kpi-monthly', [\Modules\Employee\Http\Controllers\EmployeeReportController::class, 'monthlyKpiHistory'])->name('employee.report.kpi-monthly');
    Route::get('employee-report/salary-monthly', [\Modules\Employee\Http\Controllers\EmployeeReportController::class, 'monthlySalaryData'])->name('employee.report.salary-monthly');
});
