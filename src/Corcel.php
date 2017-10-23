<?php

namespace Corcel;

use Illuminate\Foundation\Application;

/**
 * Class Corcel
 *
 * @package Corcel
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class Corcel
{
    /**
     * @return bool
     */
    public static function isLaravel()
    {
        return function_exists('app') &&
            app() instanceof Application;
    }

    /**
     * @return bool
     */
    public static function isLumen()
    {
        return preg_match('/Lumen', app()->version()) === 1;
    }
}
