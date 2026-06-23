<?php

use Illuminate\Support\Facades\Route;
use Modules\Notice\Http\Controllers\NoticeController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Public notice board (detailed card list view)
    Route::get('/notices', [NoticeController::class, 'index'])->name('notice.list');

    // Single notice detail
    Route::get('/notices/{id}/detail', [NoticeController::class, 'detail'])->name('notice.detail');

    // Management routes (CRUD + DataTable)
    Route::get('/notices/manage', [NoticeController::class, 'manage'])->name('notice.manage');
    Route::get('/dataTable/notices', [NoticeController::class, 'dataTable'])->name('notice.dataTable');
    Route::post('/notices', [NoticeController::class, 'store'])->name('notice.store');
    Route::get('/notices/{id}', [NoticeController::class, 'show'])->name('notice.show');
    Route::put('/notices/{id}', [NoticeController::class, 'update'])->name('notice.update');
    Route::delete('/notices/{id}', [NoticeController::class, 'destroy'])->name('notice.destroy');
});