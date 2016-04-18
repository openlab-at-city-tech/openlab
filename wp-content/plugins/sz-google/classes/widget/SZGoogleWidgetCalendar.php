<?php

/**
 * Class for the definition of a widget that is
 * called by the class of the main module
 *
 * @package SZGoogle
 * @subpackage Widgets
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Before the definition of the class, check if there is a definition
// with the same name or the same as previously defined in other script

if (!class_exists('SZGoogleWidgetCalendar'))
{
	class SZGoogleWidgetCalendar extends SZGoogleWidget
	{
		/**
		 * Definition the constructor function, which is called
		 * at the time of the creation of an instance of this class
		 */

		function __construct() 
		{
			parent::__construct('SZ-GOOGLE-CALENDAR',__('SZ-Google - Calendar','sz-google'),array(
				'classname'   => 'sz-widget-google sz-widget-google-calendar sz-widget-google-calendar-embed', 
				'description' => ucfirst(__('google calendar.','sz-google'))
			));
		}

		/**
		 * Generation of the HTML code of the widget
		 * for the full display in the sidebar associated
		 */

		function widget($args,$instance)
		{
			// Checking whether there are the variables that are used during the processing
			// the script and check the default values ​​in case they were not specified

			$options = $this->common_empty(array(
				'title'         => '', // default value
				'calendar'      => '', // default value
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
			),$instance);

			// Definition of the control variables of the widget, these values​
			// do not affect the items of basic but affect some aspects

			$controls = $this->common_empty(array(
				'calendarT'     => '', // default value
				'width_auto'    => '', // default value
				'height_auto'   => '', // default value
			),$instance);

			// Correction of the value of size is specified in
			// the case the automatically and then use javascript

			if ($controls['width_auto']  == '1') $options['width']  = 'auto';
			if ($controls['height_auto'] == '1') $options['height'] = 'auto';

			// Cancel the variable title that belongs to the component  
			// since there is the title of the widget and have the same name

			$options['title'] = $controls['calendarT'];

			// Create the HTML code for the current widget recalling the basic
			// function which is also invoked by the corresponding shortcode

			$OBJC = new SZGoogleActionCalendar();
			$HTML = $OBJC->getHTMLCode($options);

			// Output HTML code linked to the widget to
			// display call to the general standard for wrap

			echo $this->common_widget($args,$instance,$HTML);
		}

		/**
		 * Changing parameters related to the widget FORM 
		 * with storing the values ​​directly in the database
		 */

		function update($new_instance,$old_instance) 
		{
			// Performing additional operations on fields of the
			// form widget before it is stored in the database

			return $this->common_update(array(
				'title'         => '0', // strip_tags
				'calendarT'     => '0', // strip_tags
				'calendar'      => '1', // strip_tags
				'mode'          => '1', // strip_tags
				'weekstart'     => '1', // strip_tags
				'language'      => '1', // strip_tags
				'timezone'      => '1', // strip_tags
				'width'         => '1', // strip_tags
				'height'        => '1', // strip_tags
				'showtitle'     => '1', // strip_tags
				'shownavs'      => '1', // strip_tags
				'showdate'      => '1', // strip_tags
				'showprint'     => '1', // strip_tags
				'showtabs'      => '1', // strip_tags
				'showcalendars' => '1', // strip_tags
				'showtimezone'  => '1', // strip_tags
				'width_auto'    => '1', // strip_tags
				'height_auto'   => '1', // strip_tags
			),$new_instance,$old_instance);
		}

		/**
		 * FORM display the widget in the management of 
		 * sidebar in the administration panel of wordpress
		 */

		function form($instance) 
		{
			// Creating arrays for list fields that must be
			// present in the form before calling wp_parse_args()

			$array = array(
				'title'         => '', // default value
				'calendarT'     => '', // default value
				'calendar'      => '', // default value
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
				'width_auto'    => '', // default value
				'height_auto'   => '', // default value
			);

			// Creating arrays for list of fields to be retrieved FORM
			// and loading the file with the HTML template to display

			extract(wp_parse_args($instance,$array),EXTR_OVERWRITE);

			// Reading of the options for the control of default values
			// be assigned to the widget when it is placed in the sidebar

			if ($object = SZGoogleModule::getObject('SZGoogleModuleCalendar')) 
			{
				$options = (object) $object->getOptions();

				if ($options->calendar_w_show_title     == '1') $options->calendar_w_show_title     = 'y'; else $options->calendar_w_show_title     = 'n';
				if ($options->calendar_w_show_navs      == '1') $options->calendar_w_show_navs      = 'y'; else $options->calendar_w_show_navs      = 'n';
				if ($options->calendar_w_show_date      == '1') $options->calendar_w_show_date      = 'y'; else $options->calendar_w_show_date      = 'n';
				if ($options->calendar_w_show_print     == '1') $options->calendar_w_show_print     = 'y'; else $options->calendar_w_show_print     = 'n';
				if ($options->calendar_w_show_tabs      == '1') $options->calendar_w_show_tabs      = 'y'; else $options->calendar_w_show_tabs      = 'n';
				if ($options->calendar_w_show_calendars == '1') $options->calendar_w_show_calendars = 'y'; else $options->calendar_w_show_calendars = 'n';
				if ($options->calendar_w_show_timezone  == '1') $options->calendar_w_show_timezone  = 'y'; else $options->calendar_w_show_timezone  = 'n';

				if ($mode          == '') $mode          = $options->calendar_o_mode;
				if ($weekstart     == '') $weekstart     = $options->calendar_o_weekstart;
				if ($language      == '') $language      = $options->calendar_o_language;
				if ($timezone      == '') $timezone      = $options->calendar_o_timezone;
				if ($showtitle     == '') $showtitle     = $options->calendar_w_show_title;
				if ($shownavs      == '') $shownavs      = $options->calendar_w_show_navs;
				if ($showdate      == '') $showdate      = $options->calendar_w_show_date;
				if ($showprint     == '') $showprint     = $options->calendar_w_show_print;
				if ($showtabs      == '') $showtabs      = $options->calendar_w_show_tabs;
				if ($showcalendars == '') $showcalendars = $options->calendar_w_show_calendars;
				if ($showtimezone  == '') $showtimezone  = $options->calendar_w_show_timezone;

				if (!ctype_digit($width)  and $width  != 'auto') $width  = $options->calendar_w_width;
				if (!ctype_digit($height) and $height != 'auto') $height = $options->calendar_w_height;
			}

			// Setting any of the default parameters for the
			// fields that contain invalid values ​​or inconsistent

			$DEFAULT = include(dirname(SZ_PLUGIN_GOOGLE_MAIN)."/options/sz_google_options_calendar.php");

			if (!ctype_digit($width)  or $width  == 0) { $width  = $DEFAULT['calendar_w_width']['value'];  $width_auto  = '1'; }
			if (!ctype_digit($height) or $height == 0) { $height = $DEFAULT['calendar_w_height']['value']; $height_auto = '1'; }

			// Calling the template for displaying the part 
			// that concerns the administration panel (admin)

			@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/SZGoogleWidget.php');
			@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/' .__CLASS__.'.php');
		}
	}
}