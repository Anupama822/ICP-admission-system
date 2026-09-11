<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Permission slugs gated via explicit Gate::define, for use in routes
     * (`can:` middleware), views (`@can`), and the sidebar menu
     * (`'can' => '...'` in config/adminlte.php). Kept explicit rather than
     * relying on spatie's automatic Gate::before wiring, so behavior stays
     * unambiguous regardless of service provider boot order.
     */
    private const PERMISSION_GATES = [
        'students.view',
        'students.create',
        'students.edit',
        'students.delete',
        'students.export-pdf',
        'students.export-csv',
    ];

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

        foreach (self::PERMISSION_GATES as $permission) {
            Gate::define($permission, fn (User $user) => $user->isAdmin() || $user->hasPermissionTo($permission));
        }

        // Admins always pass permission checks; staff permissions are
        // evaluated normally against the permissions granted to them.
        Gate::before(fn ($user, string $ability) => $user->isAdmin() ? true : null);
    }
}
