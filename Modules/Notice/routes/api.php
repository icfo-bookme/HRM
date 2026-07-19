<?php

use Illuminate\Support\Facades\Route;
use Modules\Notice\Http\Controllers\NoticeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('notices', NoticeController::class)->names('notice');
});
