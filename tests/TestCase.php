<?php

namespace Corcel\Tests;

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
            '--realpath' => realpath(__DIR__ . '/Database/Migrations'),
        ]);
        $this->withFactories(__DIR__ . '/Database/Factories');
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
            'prefix'   => 'wp_',
        ]);
    }
}
