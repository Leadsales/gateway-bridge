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
