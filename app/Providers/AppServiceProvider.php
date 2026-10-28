<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

require_once __DIR__ . '/../helpers.php';

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Shared-hosting compatibility: Gracefully handle disabled proc_open in Composer/Process calls
        if (!function_exists('proc_open')) {
            $this->app->singleton('composer', function ($app) {
                return new class($app['files'], $app->basePath()) extends \Illuminate\Support\Composer {
                    public function getVersion()
                    {
                        return 'Shared Hosting (proc_open disabled)';
                    }
                };
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
