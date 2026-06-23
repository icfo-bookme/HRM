<?php

use Illuminate\Support\Facades\Route;
use Modules\Designation\Http\Controllers\DesignationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('designations', DesignationController::class)->names('designation');
});
