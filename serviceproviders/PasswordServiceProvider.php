<?php 

use Illuminate\Support\ServiceProvider;


/**
 * @author Mickael Burguet <www.rundef.com>
 */
class PasswordServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Corcel\Password\Encrypter', function ($app) {
            return new Corcel\Password\Encrypter();
        });
    }
}
