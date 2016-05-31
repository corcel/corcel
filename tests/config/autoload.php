<?php require __DIR__ . '/../../vendor/autoload.php';

$capsule = \Corcel\Database::connect($params = [
    'database' => 'corcel',
    'username' => 'ssense',
    'password' => 'ssensesql',
    'host' => '4.4.4.17',
]);

$capsule->addConnection(array_merge($params, [
    'driver' => 'mysql',
    'host' => '4.4.4.17',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]), 'no_prefix');

// $corcel = new \Corcel\Database\Manager();

// $corcel->addConnection([
//     'database' => 'corcel',
//     'username' => 'homestead',
//     'password' => 'secret',
//     'host' => '127.0.0.1',
// ], 'corcel');
