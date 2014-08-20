<?php

require __DIR__ . '/../../vendor/autoload.php';

\Corcel\Database::connect(array(
    'database'  => 'corcel-dev',
    'username'  => 'root',
    'password'  => '123456',
    'host' => '127.0.0.1',
));