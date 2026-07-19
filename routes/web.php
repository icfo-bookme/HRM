<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

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
        Route::get('/users', [\App\Http\Controllers\Dashboard\UserController::class, 'index'])->name('users.index');
        Route::post('/users', [\App\Http\Controllers\Dashboard\UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [\App\Http\Controllers\Dashboard\UserController::class, 'show'])->name('users.show');
        Route::match(['put', 'patch'], '/users/{user}', [\App\Http\Controllers\Dashboard\UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [\App\Http\Controllers\Dashboard\UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/dataTable/users', [\App\Http\Controllers\Dashboard\UserController::class, 'dataTable'])->name('users.dataTable');
    });

    // Permission Management Routes (Admin only) - AJAX + Drawer pattern
    Route::middleware(['permission:settings.roles'])->group(function () {
        Route::get('/permissions', [\App\Http\Controllers\Dashboard\PermissionController::class, 'index'])->name('permissions.index');
        Route::post('/permissions', [\App\Http\Controllers\Dashboard\PermissionController::class, 'store'])->name('permissions.store');
        Route::get('/permissions/{permission}', [\App\Http\Controllers\Dashboard\PermissionController::class, 'show'])->name('permissions.show');
        Route::match(['put', 'patch'], '/permissions/{permission}', [\App\Http\Controllers\Dashboard\PermissionController::class, 'update'])->name('permissions.update');
        Route::delete('/permissions/{permission}', [\App\Http\Controllers\Dashboard\PermissionController::class, 'destroy'])->name('permissions.destroy');
        Route::get('/dataTable/permissions', [\App\Http\Controllers\Dashboard\PermissionController::class, 'dataTable'])->name('permissions.dataTable');
    });

    // Role Management Routes (Admin only) - AJAX + Drawer pattern
    Route::middleware(['permission:settings.roles'])->group(function () {
        Route::get('/roles', [\App\Http\Controllers\Dashboard\RoleController::class, 'index'])->name('roles.index');
        Route::post('/roles', [\App\Http\Controllers\Dashboard\RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}', [\App\Http\Controllers\Dashboard\RoleController::class, 'show'])->name('roles.show');
        Route::match(['put', 'patch'], '/roles/{role}', [\App\Http\Controllers\Dashboard\RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [\App\Http\Controllers\Dashboard\RoleController::class, 'destroy'])->name('roles.destroy');
        Route::get('/dataTable/roles', [\App\Http\Controllers\Dashboard\RoleController::class, 'dataTable'])->name('roles.dataTable');
    });

});

require __DIR__.'/auth.php';