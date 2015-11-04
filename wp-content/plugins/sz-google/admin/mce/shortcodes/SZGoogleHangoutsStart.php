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
	'type'       => '', // valore predefinito
	'topic'      => '', // valore predefinito
	'badge'      => '', // valore predefinito
	'text'       => '', // valore predefinito
	'img'        => '', // valore predefinito
	'align'      => '', // valore predefinito
	'position'   => '', // valore predefinito
	'profile'    => '', // valore predefinito
	'email'      => '', // valore predefinito
	'logged'     => '', // valore predefinito
	'guest'      => '', // valore predefinito
	'width'      => '', // valore predefinito
	'width_auto' => '', // valore predefinito
);

// Creating arrays to list of fields to be retrieved FORM 
// and loading the file with the HTML template to display

extract(wp_parse_args($instance,$array),EXTR_OVERWRITE);

// Setting any of the default parameters for fields 
// that contain invalid values ​​or inconsistent

if (!ctype_digit($width) or $width == 0) { $width = 'auto'; $width_auto = '1'; }

// Read the options to control the default values ​​to be 
// assigned to the widget when it is added to the sidebar

if ($object = SZGoogleModule::getObject('SZGoogleModuleHangouts')) 
{
	$options = (object) $object->getOptions();

	// Checking if the string contains a value consistent with the
	// selection of the parameter as a numeric value that is character

	$YESNO = array('1','0','n','y');

	if (!in_array($logged,$YESNO)) $logged = $options->hangouts_start_logged;
	if (!in_array($guest ,$YESNO)) $guest  = $options->hangouts_start_guest;
}

// If the values are taken from the default options, you can
// create the problems caused to the difference in the storage state

$logged = str_replace(array('0','1'),array('n','y'),$logged);
$guest  = str_replace(array('0','1'),array('n','y'),$guest);

// Loading ADMIN template for composition using
// shortcodes in many cases the same code Widget

@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/mce/shortcodes/SZGoogleBaseHeader.php');
@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/SZGoogleWidgetHangoutsStart.php');
@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/mce/shortcodes/SZGoogleBaseFooter.php');