<?php
/**
 * An Observer that throws an SQL to determine a model's primary key which is numbered by a trigger.
 */
namespace Mits430\Larasupple\Packages\Observer;

class Observer_SequenceTable extends \Mits430\Larasupple\Packages\Orm\Observer
{
	/**
	 * Set the UpdatedAt property to the current time.
	 */
	public function after_insert(\Mits430\Larasupple\Packages\Model $obj)
	{
		$r = \DB::query("select @{$obj->table()}_last_insert_id id", \DB::SELECT)->as_assoc()->execute();
		$obj->__updateIDFieldBySequenceTrigger(\Arr::get($r,0));
	}
}
