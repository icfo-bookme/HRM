<?php

use Illuminate\Support\Facades\Route;
use Modules\Loan\Http\Controllers\LoanController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Admin routes
    Route::get('loans', [LoanController::class, 'index'])->name('loan.index');
    Route::get('loans/dataTable', [LoanController::class, 'dataTable'])->name('loan.dataTable');
    Route::get('loans/{id}/show', [LoanController::class, 'show'])->name('loan.show');
    Route::get('loans/{id}/edit', [LoanController::class, 'edit'])->name('loan.edit');
    Route::put('loans/{id}', [LoanController::class, 'update'])->name('loan.update');
    Route::delete('loans/{id}', [LoanController::class, 'destroy'])->name('loan.destroy');

    // Approval workflow
    Route::post('loans/{id}/approve', [LoanController::class, 'approve'])->name('loan.approve');
    Route::post('loans/{id}/reject', [LoanController::class, 'reject'])->name('loan.reject');
    Route::post('loans/{id}/disburse', [LoanController::class, 'disburse'])->name('loan.disburse');

    // Employee routes
    Route::get('my-loans', [LoanController::class, 'myLoans'])->name('loan.my');
    Route::get('my-loans/dataTable', [LoanController::class, 'myLoansDataTable'])->name('loan.my.dataTable');
    Route::get('loans/apply', [LoanController::class, 'create'])->name('loan.create');
    Route::post('loans', [LoanController::class, 'store'])->name('loan.store');

    // AJAX
    Route::post('loans/calculate', [LoanController::class, 'calculate'])->name('loan.calculate');
});