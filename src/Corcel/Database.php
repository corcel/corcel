<?php 

namespace Corcel;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{
    static protected $baseParams = array(
        'driver'    => 'mysql',
        'host'      => 'localhost',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
    );

    public static function connect(array $params)
    {
        $capsule = new Capsule;
        $params = array_merge(static::$baseParams, $params);
        $capsule->addConnection($params);
        $capsule->bootEloquent();        
    }
}

