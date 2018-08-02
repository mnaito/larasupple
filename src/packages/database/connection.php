<?php
/**
 * Part of the Fuel framework.
 *
 * @package    Fuel
 * @version    1.8
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2016 Fuel Development Team
 * @copyright  2008 - 2009 Kohana Team
 * @link       http://fuelphp.com
 */

namespace Supplements\Core;

abstract class Database_Connection extends \Fuel\Core\Database_Connection
{
	/**
	 * Quote a database identifier, such as a column name. Adds the
	 * table prefix to the identifier if a table name is present.
	 *
	 *     $column = $db->quote_identifier($column);
	 *
	 * You can also use SQL methods within identifiers.
	 *
	 *     // The value of "column" will be quoted
	 *     $column = $db->quote_identifier('COUNT("column")');
	 *
	 * Objects passed to this function will be converted to strings.
	 * [Database_Expression] objects will use the value of the expression.
	 * [Database_Query] objects will be compiled and converted to a sub-query.
	 * All other objects will be converted using the `__toString` method.
	 *
	 * @param   mixed $value any identifier
	 *
	 * @return  string
	 *
	 * @uses    static::table_prefix
	 */
	public function quote_identifier($value)
	{
		// return the intact value if the value consists of all numerical characters besides -, . 
		if (is_numeric($value) && ctype_digit((string)$value)) {
			return $value;
		}
		
		return parent::quote_identifier($value);
	}
}
