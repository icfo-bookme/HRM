<?php

use Illuminate\Support\Facades\Route;
use Modules\Department\Http\Controllers\DepartmentController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('departments', DepartmentController::class)->names('departments');
    Route::get('/dataTable/departments', [DepartmentController::class, 'dataTable'])->name('departments.dataTable');
});
