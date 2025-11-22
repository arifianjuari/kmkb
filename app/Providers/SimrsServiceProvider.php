<?php

namespace App\Providers;

use App\Services\SimrsService;
use Illuminate\Support\ServiceProvider;

class SimrsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SimrsService::class, function ($app) {
            return new SimrsService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
