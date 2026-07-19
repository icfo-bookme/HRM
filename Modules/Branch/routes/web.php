<?php

use Illuminate\Support\Facades\Route;
use Modules\Branch\Http\Controllers\BranchController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('branches', BranchController::class)->names('branches');
    Route::get('/dataTable/branches', [BranchController::class, 'dataTable'])->name('branches.dataTable');
});
