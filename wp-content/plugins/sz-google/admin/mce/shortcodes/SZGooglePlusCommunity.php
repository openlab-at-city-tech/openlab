<?php

/**
 * Script to implement the HTML code shared with widgets 
 * in the function pop-up insert shortcodes via GUI
 *
 * @package SZGoogle
 * @subpackage Admin
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Creating array to list the fields that must be 
// present in the form before calling wp_parse_args ()

$array = array(
	'title'      => '', // valore predefinito
	'method'     => '', // valore predefinito
	'specific'   => '', // valore predefinito
	'width'      => '', // valore predefinito
	'width_auto' => '', // valore predefinito
	'align'      => '', // valore predefinito
	'layout'     => '', // valore predefinito
	'theme'      => '', // valore predefinito
	'photo'      => '', // valore predefinito
	'owner'      => '', // valore predefinito
);

// Creating arrays to list of fields to be retrieved FORM 
// and loading the file with the HTML template to display

extract(wp_parse_args($instance,$array),EXTR_OVERWRITE);

// Read the options to control the default values ​​to be 
// assigned to the widget when it is added to the sidebar

if ($object = SZGoogleModule::getObject('SZGoogleModulePlus')) 
{
	$options = (object) $object->getOptions();

	if (!ctype_digit($width) and $width != 'auto') {
		if($layout == 'landscape') $width = $options->plus_shortcode_size_landscape;
			else $width = $options->plus_shortcode_size_portrait;
	}
}

// Setting any of the default parameters for fields 
// that contain invalid values ​​or inconsistent

$DEFAULT = include(dirname(SZ_PLUGIN_GOOGLE_MAIN)."/options/sz_google_options_plus.php");

if (!in_array($photo ,array('true','false')))         $photo  = 'true';
if (!in_array($owner ,array('true','false')))         $owner  = 'false';
if (!in_array($theme ,array('light','dark')))         $theme  = 'light';
if (!in_array($layout,array('portrait','landscape'))) $layout = 'portrait';

if (!ctype_digit($method) or $method == 0) { $method = '1'; }

if (!ctype_digit($width)  or $width  == 0) { 
	if($layout == 'landscape') $width = $DEFAULT['plus_shortcode_size_landscape']['value'];  
		else $width = $DEFAULT['plus_shortcode_size_portrait']['value'];
	$width_auto = '1';
}

// Loading ADMIN template for composition using
// shortcodes in many cases the same code Widget

@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/mce/shortcodes/SZGoogleBaseHeader.php');
@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/SZGoogleWidgetPlusCommunity.php');
@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/mce/shortcodes/SZGoogleBaseFooter.php');