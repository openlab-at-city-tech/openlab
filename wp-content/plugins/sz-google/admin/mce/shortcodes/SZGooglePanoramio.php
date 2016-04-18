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
	'title'       => '', // valore predefinito
	'template'    => '', // valore predefinito
	'width'       => '', // valore predefinito
	'width_auto'  => '', // valore predefinito
	'height'      => '', // valore predefinito
	'height_auto' => '', // valore predefinito
	'user'        => '', // valore predefinito
	'group'       => '', // valore predefinito
	'tag'         => '', // valore predefinito
	'set'         => '', // valore predefinito
	'columns'     => '', // valore predefinito
	'rows'        => '', // valore predefinito
	'orientation' => '', // valore predefinito
	'position'    => '', // valore predefinito
);

// Creating arrays to list of fields to be retrieved FORM 
// and loading the file with the HTML template to display

extract(wp_parse_args($instance,$array),EXTR_OVERWRITE);

// Read the options to control the default values ​​to be 
// assigned to the widget when it is added to the sidebar

if ($object = SZGoogleModule::getObject('SZGoogleModulePanoramio')) 
{
	$options = (object) $object->getOptions();

	if (!ctype_digit($columns)) $columns = $options->panoramio_s_columns;
	if (!ctype_digit($rows))    $rows    = $options->panoramio_s_rows;

	if (!in_array($template    ,array('photo','slideshow','list','photo_list'))) $options->panoramio_s_template;
	if (!in_array($set         ,array('all','public','recent')))                 $options->panoramio_s_set;
	if (!in_array($orientation ,array('horizontal','vertical')))                 $options->panoramio_s_orientation;
	if (!in_array($position    ,array('left','top','right','bottom')))           $options->panoramio_s_position;

	if (!ctype_digit($width)  and $width  != 'auto') $width  = $options->panoramio_s_width;
	if (!ctype_digit($height) and $height != 'auto') $height = $options->panoramio_s_height;
}

// Setting any of the default parameters for fields 
// that contain invalid values ​​or inconsistent

$DEFAULT = include(dirname(SZ_PLUGIN_GOOGLE_MAIN)."/options/sz_google_options_panoramio.php");

if (!ctype_digit($columns)) $columns = $DEFAULT['panoramio_s_columns']['value'];
if (!ctype_digit($rows))    $rows    = $DEFAULT['panoramio_s_rows']['value'];

if (!in_array($template    ,array('photo','slideshow','list','photo_list'))) $template    = $DEFAULT['panoramio_s_template']['value'];
if (!in_array($set         ,array('all','public','recent')))                 $set         = $DEFAULT['panoramio_s_set']['value'];
if (!in_array($orientation ,array('horizontal','vertical')))                 $orientation = $DEFAULT['panoramio_s_orientation']['value'];
if (!in_array($position    ,array('left','top','right','bottom')))           $position    = $DEFAULT['panoramio_s_position']['value'];

if (!ctype_digit($width)  or $width  == 0) { $width  = $DEFAULT['panoramio_s_width']['value'];  $width_auto  = '1'; }
if (!ctype_digit($height) or $height == 0) { $height = $DEFAULT['panoramio_s_height']['value']; $height_auto = '1'; }

// Loading ADMIN template for composition using
// shortcodes in many cases the same code Widget

@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/mce/shortcodes/SZGoogleBaseHeader.php');
@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/SZGoogleWidgetPanoramio.php');
@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/mce/shortcodes/SZGoogleBaseFooter.php');