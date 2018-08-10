<?php
/**
 * Fuel
 *
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.7
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Mits430\Larasupple\Packages\Observer;

/**
 * Created observer. Makes sure the created timestamp column in a Model record
 * gets a value when a new record is inserted in the database.
 */
class Observer_Created extends \Mits430\Larasupple\Packages\Orm\Observer
{
	/**
	 * @var  string  default property to set the timestamp on
	 */
	public static $property = 'created';

	/**
	 * @var  string  property to set the timestamp on
	 */
	protected $_property;

	/**
	 * @var  string  whether to overwrite an already set timestamp
	 */
	protected $_overwrite;

	/**
	 * Set the properties for this observer instance, based on the parent model's
	 * configuration or the defined defaults.
	 *
	 * @param  string  Model class this observer is called on
	 */
	public function __construct($class)
	{
		$props = $class::observers(get_class($this));
		$this->_property         = isset($props['property']) ? $props['property'] : static::$property;
		$this->_overwrite        = isset($props['overwrite']) ? $props['overwrite'] : true;
	}

	/**
	 * Set the Created property to the current time.
	 *
	 * @param  Model  Model object subject of this observer method
	 */
	public function before_insert(\Mits430\Larasupple\Packages\Orm\Model $obj)
	{
		if ($this->_overwrite or empty($obj->{$this->_property}))
		{
			$obj->{$this->_property} = \DB::expr('NOW()');
		}
	}
}
