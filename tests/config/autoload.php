<?php require __DIR__ . '/../../vendor/autoload.php';

$capsule = \Corcel\Database::connect($params = [
    'database' => 'corcel-dev',
    'username' => 'homestead',
    'password' => 'secret',
    'host' => '127.0.0.1',
]);

$capsule->addConnection(array_merge($params, [
    'driver' => 'mysql',
    'host' => 'localhost',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]), 'no_prefix');

// $corcel = new \Corcel\Database\Manager();

// $corcel->addConnection([
//     'database' => 'corcel-dev',
//     'username' => 'homestead',
//     'password' => 'secret',
//     'host' => '127.0.0.1',
// ], 'corcel');
