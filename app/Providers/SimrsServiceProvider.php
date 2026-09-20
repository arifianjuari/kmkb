<?php

namespace App\Providers;

use App\Services\SimrsService;
use App\Services\HospitalSimrsConnection;
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
        $this->app->singleton(SimrsService::class);
        $this->app->singleton(HospitalSimrsConnection::class);
    }

    public function boot()
    {
        view()->composer('simrs.*', function ($view) {
            $view->with('simrsConnectionStatus', app(SimrsService::class)->connectionStatus());
        });
    }
}
