<?php
/**
 * Smarty plugin
 * truncate with multibyte support
 *
 * @param string $value
 * @param string $target
 * @return string
 */
function smarty_modifier_mbtruncate($string, $length = 80, $etc = '...')
{
	if ($length == 0) {return '';}
	if (mb_strlen($string) > $length) {
		$length -= mb_strlen($etc);
		return mb_substr($string, 0, $length, mb_internal_encoding()).$etc;
	} else {
		return $string;
	}
}