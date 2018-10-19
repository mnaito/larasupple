<?php
namespace Mits430\Larasupple\Packages;

/**
 * Extention for Arr class
 * https://github.com/fuel/docs/commit/49e904ae59a63142222ed2900d3fd6822485fece
 */
class ArrUtil extends \Mits430\Larasupple\Packages\Arr
{
	/**
	 * Retrieve a value of a key from an assoc-array with trimming process.
	 *
	 * @param   array   array to extract from
	 * @param   string  key name
	 * @param   mixed   default value
	 * @return  mixed
	 */
	public static function get_trim(array $array, $key, $default = NULL)
	{
		$value = self::get($array, $key, $default);
		if(isset($value)){
			if (!is_scalar($value)) {
				throw new \Exception('The value is not a scalar');
			}
			$value = trim(mb_convert_kana($value, "s"));
		}
		return $value;
	}
	
	
	/**
	 * Recursively trims all values of an associative array.
	 * @param array $array
	 * @return array
	 */
	public static function trim_r(array $array) {
		array_walk_recursive($array, function(&$item, $key){ $item = (is_scalar($item)) ? trim(mb_convert_kana($item, "s")) : $item; });

		return $array;
	}
}