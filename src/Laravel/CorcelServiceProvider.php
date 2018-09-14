<?php

namespace Corcel\Laravel;

use Auth;
use Corcel\Corcel;
use Corcel\Laravel\Auth\AuthUserProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Corcel\Model\Post;
use Corcel\Model\Page;
use Corcel\Model\CustomLink;
use Corcel\Model\Taxonomy;

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
        $this->registerMorphMaps();
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

    /**
     * register morph maps for polymorphic relations
     *
     * @return void
     */
    public function registerMorphMaps()
    {
        Relation::morphMap([
            'post' => Post::class,
            'page' => Page::class,
            'custom' => CustomLink::class,
            'category' => Taxonomy::class,
        ]);
    }

    /**
     * @return void
     */
    public function register()
    {
        //
    }
}
