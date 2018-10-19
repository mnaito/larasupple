<?php

/**
 * MySQL database connection.
 *
 * @package    Fuel/Database
 * @category   Drivers
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
namespace Mits430\Larasupple\Packages\Database;

class Database_Mysql_Connection extends \Mits430\Larasupple\Vendor\Database\Database_MySQL_Connection
{
	/**
	 * connect to database and send initialization SQL
	 * (non-PHPdoc)
	 * @see \Mits430\Larasupple\Vendor\Database\Database_MySQLi_Connection::connect()
	 */
	public function connect()
	{
		parent::connect();
		
		// has any initial SQL to execute?
		extract($this->_config['connection'] + array(
			'init_sql'	 => '',
		));
		
		if (!empty($init_sql)) {
			$this->query(0, $init_sql, false);
		}
	}
}
