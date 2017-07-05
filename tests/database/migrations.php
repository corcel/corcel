<?php

use Illuminate\Container\Container;

/** @var Illuminate\Database\Capsule\Manager $capsule */
$capsule = Container::getInstance()->make('Capsule');
$connections = $capsule->getContainer()['config']['database.connections'];

$files = glob(__DIR__ . '/migrations/*.php');

foreach ($files as $file) {
    foreach ($connections as $connection => $config) {
        require $file;
    }
}
