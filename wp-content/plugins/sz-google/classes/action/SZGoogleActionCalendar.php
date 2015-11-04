<?php

/**
 * Define a class that identifies an action called by the
 * main module based on the options that have been activated
 *
 * @package SZGoogle
 * @subpackage Actions
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Before the definition of the class, check if there is a definition 
// with the same name or the same as previously defined in other script.

if (!class_exists('SZGoogleActionCalendar'))
{
	class SZGoogleActionCalendar extends SZGoogleAction
	{
		/**
		 * Function to create the HTML code of the
		 * module connected to the shortcode required
		 */

		function getShortcode($atts,$content=null) 
		{
			return $this->getHTMLCode(shortcode_atts(array(
				'calendar'      => '', // default value
				'title'         => '', // default value
				'mode'          => '', // default value
				'weekstart'     => '', // default value
				'language'      => '', // default value
				'timezone'      => '', // default value
				'width'         => '', // default value
				'height'        => '', // default value
				'showtitle'     => '', // default value
				'shownavs'      => '', // default value
				'showdate'      => '', // default value
				'showprint'     => '', // default value
				'showtabs'      => '', // default value
				'showcalendars' => '', // default value
				'showtimezone'  => '', // default value
				'action'        => 'shortcode',
			),$atts),$content);
		}

		/**
		 * Creating HTML code for the component called to
		 * be used in common for both widgets and shortcode
		 */

		function getHTMLCode($atts=array(),$content=null)
		{
			if (!is_array($atts)) $atts = array();

			// Extraction of the values ​​specified in shortcode, returned values
			// ​​are contained in the variable names corresponding to the key

			extract(shortcode_atts(array(
				'calendar'      => '', // default value
				'title'         => '', // default value
				'mode'          => '', // default value
				'weekstart'     => '', // default value
				'language'      => '', // default value
				'timezone'      => '', // default value
				'width'         => '', // default value
				'height'        => '', // default value
				'showtitle'     => '', // default value
				'shownavs'      => '', // default value
				'showdate'      => '', // default value
				'showprint'     => '', // default value
				'showtabs'      => '', // default value
				'showcalendars' => '', // default value
				'showtimezone'  => '', // default value
				'action'        => '', // default value
			),$atts));

			// Loading options for the configuration variables 
			// containing the default values ​​for shortcodes and widgets

			$options = (object) $this->getModuleOptions('SZGoogleModuleCalendar');

			// I delete spaces added and execute the transformation in string
			// lowercase for the control of special values ​​such as "auto"

			$calendar      = trim($calendar);
			$title         = trim($title);
			$action        = trim($action);
			$language      = trim($language);
			$timezone      = trim($timezone);

			$mode          = strtoupper(trim($mode));
			$weekstart     = strtolower(trim($weekstart));
			$width         = strtolower(trim($width));
			$height        = strtolower(trim($height));
			$showtitle     = strtolower(trim($showtitle));
			$shownavs      = strtolower(trim($shownavs));
			$showdate      = strtolower(trim($showdate));
			$showprint     = strtolower(trim($showprint));
			$showtabs      = strtolower(trim($showtabs));
			$showcalendars = strtolower(trim($showcalendars));
			$showtimezone  = strtolower(trim($showtimezone));

			// Conversion of the values ​​specified directly covered in the
			// parameters with the values ​​used for storing default values

			if ($showtitle     == 'yes' or $showtitle     == 'y') $showtitle     = '1'; 
			if ($shownavs      == 'yes' or $shownavs      == 'y') $shownavs      = '1'; 
			if ($showdate      == 'yes' or $showdate      == 'y') $showdate      = '1'; 
			if ($showprint     == 'yes' or $showprint     == 'y') $showprint     = '1'; 
			if ($showtabs      == 'yes' or $showtabs      == 'y') $showtabs      = '1'; 
			if ($showcalendars == 'yes' or $showcalendars == 'y') $showcalendars = '1'; 
			if ($showtimezone  == 'yes' or $showtimezone  == 'y') $showtimezone  = '1'; 

			if ($showtitle     == 'no'  or $showtitle     == 'n') $showtitle     = '0'; 
			if ($shownavs      == 'no'  or $shownavs      == 'n') $shownavs      = '0'; 
			if ($showdate      == 'no'  or $showdate      == 'n') $showdate      = '0'; 
			if ($showprint     == 'no'  or $showprint     == 'n') $showprint     = '0'; 
			if ($showtabs      == 'no'  or $showtabs      == 'n') $showtabs      = '0'; 
			if ($showcalendars == 'no'  or $showcalendars == 'n') $showcalendars = '0'; 
			if ($showtimezone  == 'no'  or $showtimezone  == 'n') $showtimezone  = '0'; 

			// If I could not assign any value to the instructions
			// above, put the default absolute and can be changed

			$YESNO = array('1','0');

			if ($action == 'widget')
			{
				if ($calendar == '') $calendar = $options->calendar_w_calendars;
				if ($title    == '') $title    = $options->calendar_w_title;
				if ($width    == '') $width    = $options->calendar_w_width;
				if ($height   == '') $height   = $options->calendar_w_height;

				if (!in_array($showtitle    ,$YESNO)) $showtitle     = $options->calendar_w_show_title;
				if (!in_array($shownavs     ,$YESNO)) $shownavs      = $options->calendar_w_show_navs;
				if (!in_array($showdate     ,$YESNO)) $showdate      = $options->calendar_w_show_date;
				if (!in_array($showprint    ,$YESNO)) $showprint     = $options->calendar_w_show_print;
				if (!in_array($showtabs     ,$YESNO)) $showtabs      = $options->calendar_w_show_tabs;
				if (!in_array($showcalendars,$YESNO)) $showcalendars = $options->calendar_w_show_calendars;
				if (!in_array($showtimezone ,$YESNO)) $showtimezone  = $options->calendar_w_show_timezone;

			} else {

				if ($calendar == '') $calendar = $options->calendar_s_calendars;
				if ($title    == '') $title    = $options->calendar_s_title;
				if ($width    == '') $width    = $options->calendar_s_width;
				if ($height   == '') $height   = $options->calendar_s_height;

				if (!in_array($showtitle    ,$YESNO)) $showtitle     = $options->calendar_s_show_title;
				if (!in_array($shownavs     ,$YESNO)) $shownavs      = $options->calendar_s_show_navs;
				if (!in_array($showdate     ,$YESNO)) $showdate      = $options->calendar_s_show_date;
				if (!in_array($showprint    ,$YESNO)) $showprint     = $options->calendar_s_show_print;
				if (!in_array($showtabs     ,$YESNO)) $showtabs      = $options->calendar_s_show_tabs;
				if (!in_array($showcalendars,$YESNO)) $showcalendars = $options->calendar_s_show_calendars;
				if (!in_array($showtimezone ,$YESNO)) $showtimezone  = $options->calendar_s_show_timezone;
			}

			// Control the variable title if specified in the option
			// otherwise check the special value with language translation

			if ($calendar  == '') $calendar  = $options->calendar_o_calendars;
			if ($title     == '') $title     = $options->calendar_o_title;
			if ($mode      == '') $mode      = $options->calendar_o_mode;
			if ($weekstart == '') $weekstart = $options->calendar_o_weekstart;
			if ($language  == '') $language  = $options->calendar_o_language;
			if ($timezone  == '') $timezone  = $options->calendar_o_timezone;

			if (!in_array($weekstart,array('1','2','7')))  $weekstart = '1';

			// Calculating the variable of language translation to be applied
			// to embed the google calendar. Special value 99 for that wordpress

			if (!array_key_exists($language,SZGoogleCommon::getLanguages())) $language = $options->calendar_o_language;
			if (!array_key_exists($language,SZGoogleCommon::getLanguages())) $language = '99';

			if ($language == '99') $language = substr(get_bloginfo('language'),0,2);	

			if (!array_key_exists($timezone,SZGoogleCommon::getTimeZone())) $timezone = $options->calendar_o_timezone;
			if (!array_key_exists($timezone,SZGoogleCommon::getTimeZone())) $timezone = 'none';

			// Checking the values ​​passed in arrays that specify the size of the widget
			// otherwise imposed on the value of the one specified in the options

			if (!ctype_digit($width)  and $width  != 'auto') $width  = 'auto';
			if (!ctype_digit($height) and $height != 'auto') $height = 'auto';

			// Control the size of the widget and formal control of the numerical
			// values, if I find some inconsistency apply the default preset

			if ($width  == '')     $width  = "100%";
			if ($width  == 'auto') $width  = "100%";

			if ($height == '')     $height = '400';
			if ($height == 'auto') $height = '400';

			// Create array containing variables that are used
			// in the URL string reference to embed on iframe

			$URLarray = array();

			$URLarray[] = "hl=".urlencode($language);
			$URLarray[] = "height=".urlencode($height);

			if ($title     != '') $URLarray[] = 'title='.urlencode($title);
			if ($weekstart != '') $URLarray[] = 'wkst=' .urlencode($weekstart);
			if ($timezone  != '') $URLarray[] = 'ctz='  .urlencode($timezone);

			if ($mode == 'AGENDA') $URLarray[] = "mode=".urlencode($mode);
			if ($mode == 'WEEK')   $URLarray[] = "mode=".urlencode($mode);

			if ($showtitle     != '1') $URLarray[] = "showTitle=0";
			if ($shownavs      != '1') $URLarray[] = "showNav=0";
			if ($showdate      != '1') $URLarray[] = "showDate=0";
			if ($showprint     != '1') $URLarray[] = "showPrint=0";
			if ($showtabs      != '1') $URLarray[] = "showTabs=0";
			if ($showcalendars != '1') $URLarray[] = "showCalendars=0";
			if ($showtimezone  != '1') $URLarray[] = "showTz=0";

			// Creating array containing the names of the calendars to display
			// The names must be separated by a comma in the specific variable

			$CALarray = explode(',',$calendar);

			foreach ($CALarray as $key=>$value) {
				if (trim($value) != '') $URLarray[] = 'src='.urlencode(trim($value));
			}

			// Creating HTML embed code to add to the page wordpress
			// First prepare the code of a single button, and then call wrapping function

			$HTML  = '<script type="text/javascript">';
			$HTML .= "var h='<'+'";
			$HTML .= 'iframe src="https://www.google.com/calendar/embed?'.implode("&amp;",$URLarray).'" ';
			$HTML .= 'style="border-width:0" ';
			$HTML .= 'width="' .$width .'" ';
			$HTML .= 'height="'.$height.'" ';
			$HTML .= 'frameborder="0" scrolling="no"';
			$HTML .= "></'+'iframe'+'>';";
			$HTML .= "document.write(h);";
			$HTML .= '</script>';

			// Return from the function with the whole string containing 
			// the HTML code for inserting the code in the page

			return $HTML;
		}
	}
}