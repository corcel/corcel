<?php

namespace Corcel;

use Illuminate\Contracts\Foundation\Application;

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
        return function_exists('app') && (
            app() instanceof Application ||
            strpos(app()->version(), 'Lumen') === 0
        );
    }
}
