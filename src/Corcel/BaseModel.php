<?php

/**
 * Corcel\BaseModel
 *
 * @author Alexander Kammerer <alexander.kammerer@online.de>
 */

namespace Corcel;
use Illuminate\Database\Eloquent\Model as Eloquent;

class BaseModel extends Eloquent {
	protected $connection = 'mysql_wp';
}
