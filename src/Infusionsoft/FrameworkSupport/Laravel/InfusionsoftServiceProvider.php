<?php

namespace Infusionsoft\FrameworkSupport\Laravel;

use Infusionsoft\Infusionsoft;
use Illuminate\Support\ServiceProvider;

/**
 * Class InfusionsoftServiceProvider
 *
 * @package Infusionsoft\FrameworkSupport\Laravel
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