<?php

namespace Corcel\Database;

use Illuminate\Database\Capsule\Manager as Capsule;

class Manager extends Capsule
{
    /**
     * Base params. Wordpress use by default MySQL databases and more.
     */
    protected $baseParams = array(
        'driver'    => 'mysql',
        'host'      => 'localhost',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => 'wp_',
    );

    public function __construct($container)
    {
        parent::__construct($container);
        $this->bootEloquent();
    }


    public function addConnection(array $params, $name = 'default')
    {
        $params = array_merge($this->baseParams, $params);
        $connections = $this->getDatabaseManager()->getConnections();

        if (count($connections) == 0) {
            $name = 'default';
        }

        return parent::addConnection($params, $name);
    }


}
