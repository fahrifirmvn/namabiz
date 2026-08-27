<?php

namespace App\Providers;

use App\Models\User;
use App\Support\PremiumAccess;
use Illuminate\Support\Facades\Blade;
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
        Gate::define('view-premium-data', function (?User $user) {
            return PremiumAccess::isPremium($user);
        });

        Blade::if('premium', function () {
            return PremiumAccess::isPremium(auth()->user());
        });
    }
}
