<?php

use Illuminate\Support\Facades\Route;
use Modules\Kpi\Http\Controllers\KpiController;

/*
|--------------------------------------------------------------------------
| KPI Module API Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->prefix('kpi')->group(function () {
    Route::get('daily/{employee}', [KpiController::class, 'apiDaily']);
    Route::get('monthly/{employee}/{year}/{month}', [KpiController::class, 'apiMonthly']);
});
