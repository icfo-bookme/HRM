<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Define Gates based on permissions
        try {
            Permission::all()->each(function ($permission) {
                Gate::define($permission->slug, function ($user) use ($permission) {
                    return $user->hasPermission($permission->slug);
                });
            });
        } catch (\Exception $e) {
            // Table might not exist during migrations
        }

        // Blade Directives
        Blade::if('permission', function (string $slug) {
            $user = auth()->user();
            return $user && $user->hasPermission($slug);
        });

        Blade::if('role', function (string $slug) {
            $user = auth()->user();
            return $user && $user->hasRole($slug);
        });

        Blade::if('hasanyrole', function (array $slugs) {
            $user = auth()->user();
            return $user && $user->hasAnyRole($slugs);
        });
    }
}