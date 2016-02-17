<?php

namespace Corcel\Database;

use Corcel\Database\Capsule;

class Connection
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

    protected $name;

    public function __construct($name = 'default')
    {
        $this->name = $name;
    }

    public function add(array $params)
    {
        $params  = array_merge($this->baseParams, $params);

        $capsule = Capsule::getInstance();
        $connections = $capsule->getDatabaseManager()->getConnections();

        if (count($connections) == 0) {
            $this->name = 'default';
        }

        $capsule->addConnection($params, $this->name);

        return $capsule;
    }


}
