<?php

namespace Corcel;

use Illuminate\Container\Container;

/**
 * Class App
 *
 * @package Corcel
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class App
{
    /**
     * @param null|string $abstract
     * @param array $parameters
     * @return mixed|null
     */
    public static function instance($abstract = null, array $parameters = [])
    {
        if (!function_exists('app')) {
            return null;
        }

        if (!app() instanceof Container) {
            return null;
        }

        return app()->make($abstract, $parameters);
    }

    /**
     * @param string $key
     * @param null|mixed $default
     * @return mixed|null
     */
    public static function config($key, $default = null)
    {
        if (!static::instance('config')) {
            return null;
        }

        return static::instance('config')->get($key, $default);
    }
}
