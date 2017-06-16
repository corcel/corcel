<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/helpers.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => ':memory:',
    'prefix' => 'wp_',
]);

// Fake database connection just for testing
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => ':memory',
    'prefix' => 'foo_',
], 'foo');

$capsule->setAsGlobal();
$capsule->bootEloquent();

app()->instance('Capsule', $capsule);

// Include migrations
require __DIR__ . '/database/migrations.php';
require __DIR__ . '/database/factories.php';

