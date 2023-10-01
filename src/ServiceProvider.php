<?php

namespace Leadsales\GatewayBridge;

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
        $this->mergeConfigFrom(__DIR__ . '/../config/firebase.php', 'firebase');

        $gatewayPath = $this->app['request']->server->get('GATEWAY_PATH');
        $configPath = source_path($gatewayPath);
        if (file_exists($configPath)) {
            $this->mergeConfigFrom($configPath, 'gateway');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
    }
}
