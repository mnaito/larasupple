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
 * Modified observer. Makes sure the updated timestamp column in a Model record
 * gets a value when a record is updated in the database.
 */
class Observer_Modified extends \Mits430\Larasupple\Packages\Orm\Observer
{
	/**
	 * @var  string  property to set the timestamp on
	 */
	public static $property = 'modified';

	/**
	 * @var  string  property to set the timestamp on
	 */
	protected $_property;

	/**
	 * @var array Names of any relations that should be taken into account when checking if the model has been updated
	 */
	protected $_relations;

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
		$this->_relations        = isset($props['relations']) ? $props['relations'] : array();
	}

	/**
	 * Set the Modified property to the current time.
	 *
	 * @param  Model  Model object subject of this observer method
	 */
	public function before_save(\Mits430\Larasupple\Packages\Orm\Model $obj)
	{
		$this->before_update($obj);
	}

	/**
	 * Set the Modified property to the current time.
	 *
	 * @param  Model  Model object subject of this observer method
	 */
	public function before_update(\Mits430\Larasupple\Packages\Orm\Model $obj)
	{
		// If there are any relations loop through and check if any of them have been changed
		$relation_changed = false;
		foreach ( $this->_relations as $relation)
		{
			if ($this->relation_changed($obj, $relation))
			{
				$relation_changed = true;
				break;
			}
		}

		if ($obj->is_changed() or $relation_changed)
		{
			$obj->{$this->_property} = \DB::expr('NOW()');
		}
	}

	/**
	 * Checks to see if any models in the given relation are changed. This function is lazy so will return true as soon
	 * as it finds something that has changed.
	 *
	 * @param Model  $obj
	 * @param string $relation
	 *
	 * @return bool
	 */
	protected function relation_changed(\Mits430\Larasupple\Packages\Orm\Model $obj, $relation)
	{
		// Check that the relation exists
		if ($obj->relations($relation) === false)
		{
			throw new \InvalidArgumentException('Unknown relation '.$relation);
		}

		// If the relation is not loaded then ignore it.
		if ( ! $obj->is_fetched($relation))
		{
			return false;
		}

		$relation_object = $obj->relations($relation);

		// Check if whe have a singular relation
		if ($relation_object->is_singular())
		{
			// If so check that one model
			return $obj->{$relation}->is_changed();
		}

		// Else we have an array of related objects so start checking them all
		foreach ($obj->{$relation} as $related_model)
		{
			if ($related_model->is_changed())
			{
				return true;
			}
		}

		return false;
	}
}
