<?php

namespace Leadsales\Common\GatewayBridge;

use Illuminate\Support\ServiceProvider;

class GatewayServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */

    public function boot()
    {
        //Carga las configuraciónes desde el paquete
        $configPath = source_path(env('CONFIG_GATEWAY_PATH'));

        if (file_exists($configPath)) {
            $this->mergeConfigFrom($configPath, 'gateway');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {}

    public function publishFiles() {}
}
