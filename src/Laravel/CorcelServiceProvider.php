<?php

namespace Corcel\Laravel;

use Auth;
use Corcel\Corcel;
use Corcel\Laravel\Auth\AuthUserProvider;
use Corcel\Laravel\Observers\UserObserver;
use Corcel\Model\User;
use Illuminate\Support\ServiceProvider;

/**
 * Class CorcelServiceProvider
 *
 * @package Corcel\Providers\Laravel
 * @author Mickael Burguet <www.rundef.com>
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class CorcelServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        $this->publishConfigFile();
        $this->registerAuthProvider();
        $this->registerObservers();
    }

    /**
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * @return void
     */
    private function publishConfigFile()
    {
        $this->publishes([
            __DIR__ . '/config.php' => base_path('config/corcel.php'),
        ]);
    }

    /**
     * @return void
     */
    private function registerAuthProvider()
    {
        if (Corcel::isLaravel()) {
            Auth::provider('corcel', function ($app, array $config) {
                return new AuthUserProvider($config);
            });
        }
    }

    private function registerObservers(): void
    {
        User::observe(UserObserver::class);
    }
}
