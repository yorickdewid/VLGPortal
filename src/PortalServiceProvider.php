<?php

namespace VLG\GSSAuth;

use \Auth;
use Illuminate\Support\ServiceProvider;

class PortalServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerUserProvider();
        $this->registerManager();
    }

    private function registerManager()
    {
        $this->app->singleton('VLG\GSSAuth\Contracts\Factory', function ($app) {
            return new PortalManager($app);
        });
    }

    private function registerUserProvider()
    {
        // 
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['VLG\GSSAuth\Contracts\Factory'];
    }
}
