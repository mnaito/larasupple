<?php
namespace Mits430\Larasupple\Packages;

/**
 * Satisfies basic requirements for all apps.
 * @author m.naito
 */
class Model extends \Mits430\Larasupple\Packages\Orm\Model
{
	/** @const */
	const INTNL_CACHEID_KEY = '__supplements_cache_id_';
	
	/** Cache ID template for primary key */
	private static $CACHE_ID_PK         = '_qc_pk_';
	/** Cache ID template for callStatic() */
	private static $CACHE_ID_CS         = '_qc_cs_';
	/** Cache ID template for count query */
	private static $CACHE_ID_COUNT      = '_qc_cc_';
	/** Cache ID template for find_cache() */
	private static $CACHE_ID_FIND       = '_qc_fc_';
	
	
	/**
	 * Determines what observers to use.
	 * @var array
	 */
	protected static $_observers = array(
		'Mits430\\Larasupple\\Packages\\Observer\\Observer_Created',
		'Mits430\\Larasupple\\Packages\\Observer\\Observer_Modified',
	);
	
	/**
	 * @var array  array of valid relation types
	 */
	protected static $_valid_relations = array(
		// Extended relation types
		'belongs_to'    => 'Mits430\\Larasupple\\Packages\\BelongsTo',
		'has_one'       => 'Mits430\\Larasupple\\Packages\\HasOne',
		'has_many'      => 'Mits430\\Larasupple\\Packages\\HasMany',
		'many_many'     => 'Mits430\\Larasupple\\Packages\\ManyMany',
		
		// More relation types
		'belongs_to_table' => 'Mits430\\Larasupple\\Packages\\BelongsToTable',
		'has_many_table' => 'Mits430\\Larasupple\\Packages\\HasManyTable',
		'has_one_table' => 'Mits430\\Larasupple\\Packages\\HasOneTable',
	);
	
	/**
	 * Initialize required components
	 */
	public static function _init() {
		\Cache::_init();
	}
	
	
	/**
	 * Update primary key just for later reference.
	 * (intended to be used by a special observer, Observer_SequenceTable.)
	 * @param array $arr
	 */
	public function __updateIDFieldBySequenceTrigger($arr) {
		if (empty($arr)) return;
		$pkey_fields = static::primary_key();
		$this->_data[$pkey_fields[0]] = $arr[$pkey_fields[0]];
	}
	
	
	/**
	 * Create an array and apply to_array() method for every records.
	 * The original index will be lost.
	 *  
	 * @param array $array
	 * @param bool $preservedKey
	 * @param string $custom
	 * @param string $recurse
	 * @return multitype:NULL
	 */
	public static function to_array_r($array, $preservedKey = false, $custom=false, $recurse=false) {
		if (!is_array($array))
			return null;
		
		$newArr = array();
		
		if ($preservedKey) {
			foreach ($array as $key => $rec) {
				// Followed by FuelPHP
				$newArr[$key] = $rec->to_array($custom, $recurse);
			}
		} else {
			foreach ($array as $key => $rec) {
				// Followed by FuelPHP
				$newArr[] = $rec->to_array($custom, $recurse);
			}
		}
		return $newArr;
	}
	
	
	/**
	 * Create an array and extract Model objects in the array with specific index key 
	 * 
	 * @param array $array
	 * @param string $indexField a value in multiple nested array can be accessed through . (dot) syntax 
	 * @param string $custom
	 * @param string $recurse
	 * @return multitype:NULL
	 */
	public static function to_array_pluck($array, $indexField, $custom=false, $recurse=false) {
		if (!is_array($array))
			return null;
		
		$newArr = array();
		foreach ($array as $key => $rec) {
			if (!isset($rec[$indexField])) {
				throw new \Exception('Unknown index specified for the array. Maybe 2nd argument is misspelled? : '.$indexField);
			}
			$_key = \Arr::get($rec, $indexField);
			$newArr[$_key] = $rec->to_array($custom, $recurse);
		}
		return $newArr;
	}
	
	
	/**
	 * Save the object and its related data.
	 *
	 * @param  mixed  $cascade
	 *     null = use default config,
	 *     bool = force/prevent cascade,
	 *     array cascades only the relations that are in the array
	 *
	 * @return bool
	 */
	public function save_with_cache($cascade = null, $use_transaction = false)
	{
		if (!$this->_is_new) {
			$cache_id = (isset($this->{self::INTNL_CACHEID_KEY})) ? $this->{self::INTNL_CACHEID_KEY} : self::_cacheid_pk($this->id);
			\Cache::delete($cache_id);
		}
		return parent::save($cascade, $use_transaction);
	}
	
	
	/**
	 * Delete current object from database and default cache storage
	 *
	 * @param   mixed $cascade
	 *     null = use default config,
	 *     bool = force/prevent cascade,
	 *     array cascades only the relations that are in the array
	 * @param bool $use_transaction
	 *
	 * @throws \Exception
	 *
	 * @return  Model  this instance as a new object without primary key(s)
	 */
	public function delete_with_cache($cascade = null, $use_transaction = false) {
		$cache_id = (isset($this->{self::INTNL_CACHEID_KEY})) ? $this->{self::INTNL_CACHEID_KEY} : self::_cacheid_pk($this->id);
		\Cache::delete($cache_id);
		return parent::delete($cascade, $use_transaction);
	}
	
	
	/**
	 * Delete current object cache
	 *
	 * @throws \Exception
	 */
	public function delete_cache() {
		$cache_id = (isset($this->{self::INTNL_CACHEID_KEY})) ? $this->{self::INTNL_CACHEID_KEY} : self::_cacheid_pk($this->id);
		\Cache::delete($cache_id);
	}
	
	
	/**
	 * Additionally supports find_cache_* and count_cache_* methods. 
	 * @param string $method
	 * @param mixed $args
	 */
	public static function __callStatic($method, $args)
	{
		// Start with count_by? Get counting!
		if (strpos($method, 'count_cache_by') === 0)
		{
			$find_type = 'count';
			$fields = substr($method, 14);
		}

		// Otherwise, lets find stuff
		elseif (strpos($method, 'find_cache_') === 0)
		{
			$find_type = strncmp($method, 'find_cache_all_by_', 18) === 0 ? 'all' : (strncmp($method, 'find_cache_by_', 14) === 0 ? 'first' : false);
			$fields = $find_type === 'first' ? substr($method, 14) : substr($method, 18);
		}
		
		// Found something callable to cache what you might want..
		if (!empty($find_type)) {
			// Get a cache id for this query.
			if ($find_type == 'first' && $fields == 'id') {
				// Selected by id
				$cache_id = self::_cacheid_pk(\Arr::get($args, 0));
			}
			// Selected not by id
			else {
				// Get a table name
				$model = static::table();
				$cache_id = self::_cacheid_cs($find_type, $fields, $args);
			}
			
			// Get a cache time and force flag
			$ctime = 0;
			$force = false;
			
			// Counts number of additional arguments required by the original method. 
			$mult_f_argv = preg_split( "/(_and_|_or_)/", $fields);
			$mult_f_argc = count($mult_f_argv);
			
			// "Select all" or count requires one more array as argument value.
			if ($find_type == 'all' || $find_type == 'count')
				$mult_f_argc++;
			
			// Count all arguments.
			$argc = count($args);
			
			// Obtain those parameters followed by the diffenence.
			$arg_diff = $argc - $mult_f_argc;
			$options = false;
			switch ($arg_diff) {
				case 1:
					// (Ex). Model_Foo::find_cache_by_something(999, 3600);
					$ctime = array_pop($args);
					break;
				case 2:
					// (Ex). Model_Foo::find_cache_by_something(999, 3600, array( ... options ... ));
					$options = array_pop($args);
					$ctime = array_pop($args);
					break;
				case 3:
					// (Ex). Model_Foo::find_cache_by_something(999, 3600, array( ... options ... ), false);
					$force = array_pop($args);
					$options = array_pop($args);
					$ctime = array_pop($args);
					break;
			}
			// Reorganize args if there's a option array
			if ($options !== false) {
				array_push($args, $options);
			}
			
			// Use default cache time if ctime is empty.
			if (empty($ctime)) {
				$ctime = \Config::get('cache.expiration');
			}
			
			// Returns the cached object/array
			if (($cachedResult = \Cache::get($cache_id)) != null && $force == false) {
				return $cachedResult;
			}
			
			// Use the original name to call a desired method.
			$method = str_replace('_cache_', '_', $method);
			
			if (!isset($args[1])) {
				$args[1] = array();
			}
			$result = parent::__callStatic($method, $args);
			if ($find_type == 'all') {
				$arr_result = $result;
				
				// Cahche the result only if find('all') returns an array containing at least one Model.
				if (count($arr_result) > 0)
					\Cache::set($cache_id, $arr_result, $ctime);

				return $arr_result;
			}
			else if ($find_type == 'count') {
				// Cahche the result only if count() returns 1 or above.
				if ($result > 0)
					\Cache::set($cache_id, $result, $ctime);
				
				return $result;
			}
			else {
				// find() returns null if threre is no matched result.
				if (!is_null($result)) {
					$result->{self::INTNL_CACHEID_KEY} = $cache_id;
					
					\Cache::set($cache_id, $result, $ctime);
				}
				return $result;
			}
		}
		
		return parent::__callStatic($method, $args);
	}
	
	
	/**
	 * Find one or more entries with cache consideration
	 *
	 * @param int|null $id
	 * @param array $options
	 * @param int $ctime
	 * @param boolean $force
	 *
	 * @throws \FuelException
	 *
	 * @return  Model|Model[]
	 */
	public static function find_cache($id = null, $options = null, $ctime = null, $force = false)
	{
		if (is_null($options))
			$options = array();
		
		// use the default cache time when $ctime is not given
		if (empty($ctime)) {
			$ctime = \Config::get('cache.expiration');
		}
		
		// PK search
		if (is_numeric($id)) {
			$cache_id = self::_cacheid_pk($id);
		}
		// Includes where condition.
		else {
			// $id may contain too long characters so just include it into option's hash. 
			$cache_id = self::_cacheid_find($options);
		}
		
		// Returns the cached object/array
		// Negative cache time means that we don't return a cached result.
		if (($cachedResult = \Cache::get($cache_id)) != null && $force == false) {
			return $cachedResult;
		}
		
		// Use the method to get a desired result.
		$result = parent::find($id, $options);
		
		if ($id === 'all') {
			$arr_result = $result;
			
			// Cahche the result only if find('all') returns an array containing at least one Model.
			if (count($arr_result) > 0)
				\Cache::set($cache_id, $arr_result, $ctime);
			
			return $arr_result;
		}
		else {
			// ONLY "NON NULL" RESULT ARE SAVED
			// find() returns null if threre is no matched result. 
			if (!is_null($result))
				$result->{self::INTNL_CACHEID_KEY} = $cache_id;
				
				\Cache::set($cache_id, $result, $ctime);
			
			return $result;
		}
	}
	
	
	/**
	 * Count entries and store it to the default cache engine, optionally only those matching the $options
	 *
	 * @param   array
	 * @param   int $ctime
	 * @param   boolean force update
	 * @return  int
	 */
	public static function count_cache($options = null, $ctime = null, $force = false)
	{
		if (is_null($options))
			$options = array(); 
		
		// use the default cache time
		if (empty($ctime)) {
			$ctime = \Config::get('cache.expiration');
		}
		$cache_id = self::_cacheid_count($options);
		
		// Returns the cached object/array
		// Negative cache time means that we don't return a cached result.
		if (($cachedResult = \Cache::get($cache_id)) != null && $force == false) {
			return $cachedResult;
		}
		
		// Use the method to get a desired result.
		$result = parent::count($options);
		// Cahche the result only if count() returns 1 or above.
		if ($result > 0)
			\Cache::set($cache_id, $result, $ctime);
		
		return $result;
	}
	
	
	/**
	 * Delete cache data of list cache for this model
	 */
	public static function flush_cached_data() {
		$table = static::table();
		\Cache::delete_all(self::$CACHE_ID_PK."{$table}");
		\Cache::delete_all(self::$CACHE_ID_FIND."{$table}");
		\Cache::delete_all(self::$CACHE_ID_CS."{$table}");
	}
	
	
	/**
	 * Delete cache data of record count for this model.
	 */
	public static function flush_cached_count() {
		$table = static::table();
		\Cache::delete_all(self::$CACHE_ID_COUNT."{$table}");
	}
	
	
	/**
	 * Returns cache key for query cache
	 * @param string id Record ID
	 * @return string cache_id
	 */
	private static function _cacheid_pk($id) {
		$table = self::table();
		return self::$CACHE_ID_PK."{$table}.{$id}";
	}
	
	
	/**
	 * Returns cache key for call static
	 * @param string find_type $find_type
	 * @param string fields fields
	 * @param array options condition
	 * @return string cache_id
	 */
	private static function _cacheid_cs($find_type, $fields, $options) {
		$table = self::table();
		return self::$CACHE_ID_CS."{$table}.".md5($find_type.$fields.json_encode($options));
	}
	
	
	/**
	 * Returns cache key for count cache
	 * @param array options condition
	 * @return string cache_id
	 */
	private static function _cacheid_count($options = null) {
		$table = self::table();
		$cache_id = self::$CACHE_ID_COUNT."{$table}.".md5(json_encode($options));
		return $cache_id;
	}
	
	
	/**
	 * Returns cache key for query (that returns list of something) cache
	 * @param array options condition
	 * @return string cache_id
	 */
	private static function _cacheid_find($options = null) {
		$table = self::table();
		$cache_id = self::$CACHE_ID_FIND."{$table}.".md5(json_encode($options));
		return $cache_id;
	}
}