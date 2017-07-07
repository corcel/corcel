<?php

namespace Corcel\Laravel\Auth;

use Illuminate\Support\ServiceProvider;

/**
 * @author Mickael Burguet <www.rundef.com>
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        \Auth::provider('corcel', function ($app, array $config) {
            return new AuthUserProvider($config);
        });
    }

    /**
     * Register bindings in the container.
     */
    public function register()
    {
    }
}
