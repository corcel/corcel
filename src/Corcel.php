<?php

namespace Corcel;

use Illuminate\Container\Container;

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
        if (!function_exists('app')) {
            return false;
        }

        if (app() instanceof Container) {
            return true;
        }

        return false;
    }
}
