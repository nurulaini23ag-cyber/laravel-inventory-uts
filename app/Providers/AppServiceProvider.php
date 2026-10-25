<?php


namespace App\Providers;

use App\Models\User;
use Illuminate\Pagination\Paginator;
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

        Paginator::useBootstrapFive();

        //  Authorization Gates
        Gate::define('admin-only', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('manage-users', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('manage-master', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('cancel-transaction', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('manage-sales', function (User $user) {
            return in_array($user->role, ['admin', 'staff']);
        });

        Gate::define('manage-purchases', function (User $user) {
            return in_array($user->role, ['admin', 'staff']);
        });
    }
}
