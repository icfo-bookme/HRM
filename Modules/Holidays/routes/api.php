<?php

use Illuminate\Support\Facades\Route;
use Modules\Holidays\Http\Controllers\HolidaysController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('holidays', HolidaysController::class)->names('holidays');
});
