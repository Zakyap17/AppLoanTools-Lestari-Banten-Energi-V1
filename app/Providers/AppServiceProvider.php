<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Pagination\Paginator;

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

        if (request()->server('HTTP_X_FORWARDED_PROTO') == 'https') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Register View Composer for Topbpar Notifications
        \Illuminate\Support\Facades\View::composer('partials.topbar', \App\View\Composers\NotificationComposer::class);
    }
}
