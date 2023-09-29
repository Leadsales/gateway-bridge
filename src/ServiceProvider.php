<?php

namespace Leadsales\Common\GatewayBridge;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/firebase.php' => $this->app->configPath('firebase.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/firebase.php', 'firebase');

    }
}
