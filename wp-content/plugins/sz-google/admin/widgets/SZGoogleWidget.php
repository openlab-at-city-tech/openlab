<?php

/**
 * HTML code of this widget in the administration section
 * This code is on a separate file to exclude it from the frontend
 *
 * @package SZGoogle
 * @subpackage Admin
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Defining and initializing arrays that will
// be used for creating automatic variables

$variables = array();

// Reading array creation and identification of the 
// name with the prefix conventional ID_ NAME_ VALUE_

foreach($array as $item=>$value) 
{
	$PREFIX_I = 'ID_'   .$item;
	$PREFIX_N = 'NAME_' .$item;
	$PREFIX_V = 'VALUE_'.$item;

	$variables[$PREFIX_I] = $this->get_field_id($item);
	$variables[$PREFIX_N] = $this->get_field_name($item);
	$variables[$PREFIX_V] = esc_attr(${$item});
}

// Extraction array for creating variables
// as indicated in the key and associated value

extract($variables,EXTR_OVERWRITE);