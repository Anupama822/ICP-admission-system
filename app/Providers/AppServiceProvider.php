<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('admin', fn ($user) => $user->isAdmin());
        Gate::define('staff', fn ($user) => $user->isStaff());

        // Admins always pass permission checks; staff permissions are
        // evaluated normally against the permissions granted to them.
        Gate::before(fn ($user, string $ability) => $user->isAdmin() ? true : null);
    }
}
