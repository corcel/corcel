<?php

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Factory;

/**
 * @param string $abstract
 * @param array $parameters
 * @return mixed|\Illuminate\Foundation\Application
 */
function app($abstract = null, array $parameters = [])
{
    if (is_null($abstract)) {
        return Container::getInstance();
    }

    return empty($parameters)
        ? Container::getInstance()->make($abstract)
        : Container::getInstance()->makeWith($abstract, $parameters);
}

/**
 * @param dynamic class|class,name|class,amount|class,name,amount
 * @return \Illuminate\Database\Eloquent\FactoryBuilder
 */
function factory()
{
    $factory = app(Factory::class);

    $arguments = func_get_args();

    if (isset($arguments[1]) && is_string($arguments[1])) {
        return $factory->of($arguments[0], $arguments[1])->times(isset($arguments[2]) ? $arguments[2] : null);
    } elseif (isset($arguments[1])) {
        return $factory->of($arguments[0])->times($arguments[1]);
    } else {
        return $factory->of($arguments[0]);
    }
}
