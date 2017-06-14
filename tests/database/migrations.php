<?php

use Illuminate\Container\Container;

$capsule = Container::getInstance()->make('Capsule');

$files = glob(__DIR__ . '/migrations/*.php');

foreach ($files as $file) {
    require $file;
}
