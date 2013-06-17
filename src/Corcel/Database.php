<?php 

namespace Corcel;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{
    public static function connect(array $params)
    {
        $capsule = new Capsule;
        $capsule->addConnection($params);
        $capsule->bootEloquent();        
    }
}

