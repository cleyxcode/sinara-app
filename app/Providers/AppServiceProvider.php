<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Force HTTPS in production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
        
        // Fix mixed content issues
        if (request()->header('x-forwarded-proto') == 'https') {
            URL::forceScheme('https');
        }
        
        // Set proper asset URL for production
        if (config('app.env') === 'production') {
            $this->app['url']->forceRootUrl(config('app.url'));
        }
    }
}