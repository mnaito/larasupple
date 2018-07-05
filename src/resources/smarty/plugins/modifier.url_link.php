<?php
/**
 * Smarty plugin
 * URL to link
 *
 * @param string $value
 * @param string $target
 * @return string
 */
function smarty_modifier_url_link($value, $target = null)
{
	$options = "";

	if (!empty($target)) {
		$options = sprintf(" target=\"%s\"", $target);
	}

	$value = preg_replace("/http(s)?:\/\/[^<>[:space:]]+[[:alnum:]\/]/" , '<a '.$options.' href="\\0" onclick="confirm(\'外部サイトに移動します。よろしければOKを押して下さい。\\n(Press OK if you open the external site.)\');">\\0</a>' , $value );


	return $value;
}