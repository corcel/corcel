<?php

/**
 * Corcel\BaseModel
 *
 * @author Alexander Kammerer <alexander.kammerer@online.de>
 */

namespace Corcel;
use Illuminate\Database\Eloquent\Model as Eloquent;

class BaseModel extends Eloquent {
	/**
	 * You may want to use multiple databases and reset the connection for this model.
	 * @var String
	 */
	private static $dbConnection;

	/**
	 * Overwrite default constructor to set the connection attribute before booting.
	 */
	public function __construct() {
		// Set the connection to the one we want to use.
		$this->setConnection(self::$dbConnection);
		parent::__construct();
	}

	/**
	 * Set the conection to the Wordpress database.
	 * @param String $connection Connection used to connect to the Wordpress database.
	 */
	public static function setConfiguredConnection($connection) {
		self::$dbConnection = $connection;
	}
}
