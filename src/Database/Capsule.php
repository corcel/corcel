<?php

namespace Corcel\Database;

use Illuminate\Database\Capsule\Manager;

class Capsule
{
    protected static $instance;

    private function __construct()
    {
        // private method
    }

    /**
     * @return Illuminate\Database\Capsule\Manager
     */
    public static function getInstance()
    {
        if (! isset(static::$instance)) {
            $instance = new Manager();
            $instance->bootEloquent();

            static::$instance = $instance;
        }

        return static::$instance;
    }

    function __clone()
    {
        return $this->getInstance();
    }
}