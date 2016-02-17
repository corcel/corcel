<?php require __DIR__ . '/../../vendor/autoload.php';

use Corcel\Database\Connection;


//\Corcel\Database::connect(array(
//    'database'  => 'corcel-dev',
//    'username'  => 'homestead',
//    'password'  => 'secret',
//    'host' => '127.0.0.1',
//));

$params = [
    'database' => 'corcel-dev',
    'username' => 'homestead',
    'password' => 'secret',
    'host' => '127.0.0.1',
];


$corcel = new Connection('corcel');
$corcel->add($params);