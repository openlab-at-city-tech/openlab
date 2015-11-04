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
	'title'         => '', // valore predefinito
	'calendarT'     => '', // valore predefinito
	'calendar'      => '', // valore predefinito
	'mode'          => '', // valore predefinito
	'weekstart'     => '', // valore predefinito
	'language'      => '', // valore predefinito
	'timezone'      => '', // valore predefinito
	'width'         => '', // valore predefinito
	'height'        => '', // valore predefinito
	'showtitle'     => '', // valore predefinito
	'shownavs'      => '', // valore predefinito
	'showdate'      => '', // valore predefinito
	'showprint'     => '', // valore predefinito
	'showtabs'      => '', // valore predefinito
	'showcalendars' => '', // valore predefinito
	'showtimezone'  => '', // valore predefinito
	'width_auto'    => '', // valore predefinito
	'height_auto'   => '', // valore predefinito
);

// Creating arrays to list of fields to be retrieved FORM 
// and loading the file with the HTML template to display

extract(wp_parse_args($instance,$array),EXTR_OVERWRITE);

// Read the options to control the default values ​​to be 
// assigned to the widget when it is added to the sidebar

if ($object = SZGoogleModule::getObject('SZGoogleModuleCalendar')) 
{
	$options = (object) $object->getOptions();

	if ($options->calendar_s_show_title     == '1') $options->calendar_s_show_title     = 'y'; else $options->calendar_s_show_title     = 'n';
	if ($options->calendar_s_show_navs      == '1') $options->calendar_s_show_navs      = 'y'; else $options->calendar_s_show_navs      = 'n';
	if ($options->calendar_s_show_date      == '1') $options->calendar_s_show_date      = 'y'; else $options->calendar_s_show_date      = 'n';
	if ($options->calendar_s_show_print     == '1') $options->calendar_s_show_print     = 'y'; else $options->calendar_s_show_print     = 'n';
	if ($options->calendar_s_show_tabs      == '1') $options->calendar_s_show_tabs      = 'y'; else $options->calendar_s_show_tabs      = 'n';
	if ($options->calendar_s_show_calendars == '1') $options->calendar_s_show_calendars = 'y'; else $options->calendar_s_show_calendars = 'n';
	if ($options->calendar_s_show_timezone  == '1') $options->calendar_s_show_timezone  = 'y'; else $options->calendar_s_show_timezone  = 'n';

	if ($mode          == '') $mode          = $options->calendar_o_mode;
	if ($weekstart     == '') $weekstart     = $options->calendar_o_weekstart;
	if ($language      == '') $language      = $options->calendar_o_language;
	if ($timezone      == '') $timezone      = $options->calendar_o_timezone;
	if ($showtitle     == '') $showtitle     = $options->calendar_s_show_title;
	if ($shownavs      == '') $shownavs      = $options->calendar_s_show_navs;
	if ($showdate      == '') $showdate      = $options->calendar_s_show_date;
	if ($showprint     == '') $showprint     = $options->calendar_s_show_print;
	if ($showtabs      == '') $showtabs      = $options->calendar_s_show_tabs;
	if ($showcalendars == '') $showcalendars = $options->calendar_s_show_calendars;
	if ($showtimezone  == '') $showtimezone  = $options->calendar_s_show_timezone;

	if (!ctype_digit($width)  and $width  != 'auto') $width  = $options->calendar_s_width;
	if (!ctype_digit($height) and $height != 'auto') $height = $options->calendar_s_height;
}

// Setting any of the default parameters for fields 
// that contain invalid values ​​or inconsistent

$DEFAULT = include(dirname(SZ_PLUGIN_GOOGLE_MAIN)."/options/sz_google_options_calendar.php");

if (!ctype_digit($width)  or $width  == 0) { $width  = $DEFAULT['calendar_s_width']['value'];  $width_auto  = '1'; }
if (!ctype_digit($height) or $height == 0) { $height = $DEFAULT['calendar_s_height']['value']; $height_auto = '1'; }

// Loading ADMIN template for composition using
// shortcodes in many cases the same code Widget

@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/mce/shortcodes/SZGoogleBaseHeader.php');
@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/SZGoogleWidgetCalendar.php');
@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/mce/shortcodes/SZGoogleBaseFooter.php');