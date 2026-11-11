<?php

namespace App\Providers;

use App\Models\User;
use App\Support\PremiumAccess;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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
        RateLimiter::for('api-auth', function (Request $request) {
            $email = strtolower((string) $request->input('email', ''));

            return Limit::perMinute(10)->by($email.'|'.$request->ip());
        });

        RateLimiter::for('reviews', function (Request $request) {
            return Limit::perMinute(5)->by((string) ($request->user()?->id ?: $request->ip()));
        });

        Gate::define('view-premium-data', function (?User $user) {
            return PremiumAccess::isPremium($user);
        });

        Gate::define('access-admin', function (User $user): bool {
            return $user->isAdmin();
        });

        Blade::if('premium', function () {
            return PremiumAccess::isPremium(auth()->user());
        });
    }
}
