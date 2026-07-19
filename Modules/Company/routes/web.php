<?php

use Illuminate\Support\Facades\Route;
use Modules\Company\Http\Controllers\CompanyController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('companies', CompanyController::class)->names('company');
    Route::get('dataTable/companies', [CompanyController::class, 'dataTable'])->name('company.dataTable');
});
