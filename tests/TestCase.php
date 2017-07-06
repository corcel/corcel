<?php

namespace Corcel\Tests;

use Illuminate\Contracts\Auth\Authenticatable;
use Orchestra\Database\ConsoleServiceProvider;

/**
 * Class TestCase
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->loadMigrationsFrom([
            '--database' => 'wp',
            '--realpath' => __DIR__.'/database/migrations',
        ]);

        $this->loadMigrationsFrom([
            '--database' => 'foo',
            '--realpath' => __DIR__.'/database/migrations',
        ]);

        $this->withFactories(__DIR__.'/database/factories');
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'wp');

        $app['config']->set('database.connections.wp', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => 'wp_',
        ]);

        $app['config']->set('database.connections.foo', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => 'foo_',
        ]);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            ConsoleServiceProvider::class,
        ];
    }

    /**
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  string $driver
     * @return void
     */
    public function be(Authenticatable $user, $driver = null)
    {
        // TODO: Implement be() method.
    }
}
