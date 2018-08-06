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

namespace Mits430\Larasupple\Packages\Database;

class Database_Query_Builder_Insert extends \Mits430\Larasupple\Vendor\Database\Database_Query_Builder_Insert
{
	/**
	 * Compile the SQL query and return it.
	 *
	 * @param   mixed  $db  Database instance or instance name
	 *
	 * @return  string
	 */
	public function compile($db = null)
	{
		if ( ! $db instanceof \Database_Connection)
		{
			// Get the database instance
			$db = \Database_Connection::instance($db);
		}

		// Start an insertion query
		$query = 'INSERT INTO '.$db->quote_table($this->_table);

		// Add the column names
		$query .= ' ('.implode(', ', array_map(array($db, 'quote_identifier'), $this->_columns)).') ';

		if (is_array($this->_values))
		{
			// Callback for quoting values
			$quote = array($db, 'quote');

			$groups = array();
			foreach ($this->_values as $group)
			{
				foreach ($group as $i => $value)
				{
					if (is_string($value) AND isset($this->_parameters[$value]))
					{
						// Use the parameter value
						$group[$i] = $this->_parameters[$value];
					}
				}

				$groups[] = '('.implode(', ', array_map($quote, $group)).')';
			}

			// Add the values
			$query .= 'VALUES '.implode(', ', $groups);
		}
		else
		{
			// Add the sub-query
			$query .= (string) $this->_values;
		}
		
		if ($db instanceof \Database_Pgsql_Connection)
		{
			$columns = \DB::list_columns($this->_table);
			if (isset($columns['id']))
				$query .= ' RETURNING id';
		}
		
		return $query;
	}
}
