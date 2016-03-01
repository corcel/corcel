<?php require __DIR__ . '/../../vendor/autoload.php';

\Corcel\Database::connect(array(
   'database'  => 'corcel-dev',
   'username'  => 'homestead',
   'password'  => 'secret',
   'host' => '127.0.0.1',
));

// $corcel = new \Corcel\Database\Manager();

// $corcel->addConnection([
//     'database' => 'corcel-dev',
//     'username' => 'homestead',
//     'password' => 'secret',
//     'host' => '127.0.0.1',
// ], 'corcel');