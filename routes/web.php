<?php

use App\Http\Controllers\Dashboard\PermissionController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/dashboard/attendance-chart-data', [DashboardController::class, 'attendanceChartData'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.attendanceChartData');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notification routes
    Route::post('/notifications/mark-all-read', function () {
        request()->user()->unreadNotifications->markAsRead();

        return redirect()->back();
    })->name('notifications.markAllRead');

    Route::post('/notifications/{id}/mark-read', function ($id) {
        $notification = request()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->back();
    })->name('notifications.markRead');

    // User Management Routes (Admin only) - AJAX + Drawer pattern
    Route::middleware(['permission:settings.users'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::match(['put', 'patch'], '/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/dataTable/users', [UserController::class, 'dataTable'])->name('users.dataTable');
    });

    // Permission Management Routes (Admin only) - AJAX + Drawer pattern
    Route::middleware(['permission:settings.roles'])->group(function () {
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
        Route::get('/permissions/{permission}', [PermissionController::class, 'show'])->name('permissions.show');
        Route::match(['put', 'patch'], '/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
        Route::get('/dataTable/permissions', [PermissionController::class, 'dataTable'])->name('permissions.dataTable');
    });

    // Role Management Routes (Admin only) - AJAX + Drawer pattern
    Route::middleware(['permission:settings.roles'])->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
        Route::match(['put', 'patch'], '/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::get('/dataTable/roles', [RoleController::class, 'dataTable'])->name('roles.dataTable');
    });

});

require __DIR__.'/auth.php';
