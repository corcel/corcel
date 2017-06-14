<?php

use Faker\Generator as Faker;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Factory;

$factory = Factory::construct(new Faker, __DIR__.'/../database/factories');

$ioc = Container::getInstance();
$ioc->instance(Factory::class, $factory);

/**
 * @param string $class
 * @param int $times
 * @return mixed
 */
function factory($class, $times = 1)
{
    $container = Container::getInstance();
    $factory = $container->make(Factory::class);

    return $factory->of($class)->times($times);
}