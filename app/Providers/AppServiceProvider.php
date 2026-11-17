<?php

namespace App\Providers;

use App\Models\User;
use App\Support\CatalogPresentation;
use App\Support\PremiumAccess;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

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
        $trustedProxies = trim((string) config('namabiz.trusted_proxies', ''));
        if ($trustedProxies !== '') {
            TrustProxies::at(
                $trustedProxies === '*'
                    ? '*'
                    : array_values(array_filter(array_map('trim', explode(',', $trustedProxies))))
            );
        }

        View::composer(['landing', 'layouts.guest'], function (ViewInstance $view): void {
            $view->with(CatalogPresentation::overview());
        });

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
