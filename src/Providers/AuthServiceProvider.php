<?php

namespace Corcel\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * @author Mickael Burguet <www.rundef.com>
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        \Auth::provider('corcel', function($app, array $config) {
            return new AuthUserProvider();
        });
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
    }
}
