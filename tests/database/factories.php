<?php

use Faker\Factory as Faker;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Factory;

$factory = Factory::construct(
    Faker::create(), __DIR__ . '/../database/factories'
);

$ioc = Container::getInstance();
$ioc->instance(Factory::class, $factory);
