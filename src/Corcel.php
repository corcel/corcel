<?php

namespace Corcel;

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

        $class = get_class(app());

        if ($class === 'Illuminate\\Foundation\\Application' ||
            $class === 'Laravel\\Lumen\\Application') {
            return true;
        }

        return false;
    }
}
