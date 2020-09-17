<?php

namespace Infusionsoft\FrameworkSupport\Lumen;

use Infusionsoft\Infusionsoft;
use Illuminate\Support\ServiceProvider;

/**
 * Class InfusionsoftServiceProvider
 *
 * @package Infusionsoft\FrameworkSupport\Lumen
 */
class InfusionsoftServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('infusionsoft', function ($app) {
            return new Infusionsoft();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'infusionsoft'
        ];
    }

}
