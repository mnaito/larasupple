<?php
/**
* Smarty plugin
* @package Smarty
* @subpackage plugins
*/

/**
* Include the {@link shared.make_timestamp.php} plugin
*/
// handle with Smarty version 3. Be aware this might not work with Smarty 2!!
require_once(SMARTY_PLUGINS_DIR . 'shared.make_timestamp.php');

function smarty_modifier_date_format2($string, $format = 'M d, Y', $default_date = '')
{
  if ($string != '') {
      $timestamp = smarty_make_timestamp($string);
  } elseif ($default_date != '') {
      $timestamp = smarty_make_timestamp($default_date);
  } else {
      return;
  }
  return date($format, $timestamp);
}

/* vim: set expandtab: */

?>
