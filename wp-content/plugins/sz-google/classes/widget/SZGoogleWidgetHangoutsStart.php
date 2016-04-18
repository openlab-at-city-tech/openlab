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

if (!class_exists('SZGoogleWidgetHangoutsStart'))
{
	class SZGoogleWidgetHangoutsStart extends SZGoogleWidget
	{
		/**
		 * Definition the constructor function, which is called
		 * at the time of the creation of an instance of this class
		 */

		function __construct() 
		{
			parent::__construct('SZ-GOOGLE-HANGOUTS-START',__('SZ-Google - Hangouts start','sz-google'),array(
				'classname'   => 'sz-widget-google sz-widget-google-hangouts sz-widget-google-hangouts-start', 
				'description' => ucfirst(__('hangout start button.','sz-google'))
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
				'type'         => '', // default value
				'width'        => '', // default value
				'topic'        => '', // default value
				'float'        => '', // default value
				'align'        => '', // default value
				'text'         => '', // default value
				'img'          => '', // default value
				'position'     => '', // default value
				'profile'      => '', // default value
				'email'        => '', // default value
				'logged'       => '', // default value
				'guest'        => '', // default value
				'margintop'    => '', // default value
				'marginright'  => '', // default value
				'marginbottom' => '', // default value
				'marginleft'   => '', // default value
				'marginunit'   => '', // default value
				'class'        => '', // default value
			),$instance);

			// Definition of the control variables of the widget, these values​
			// do not affect the items of basic but affect some aspects

			$controls = $this->common_empty(array(
				'badge'        => '', // default value
				'width_auto'   => '', // default value
			),$instance);

			// If the widget I excluded from the badge button I reset
			// the variables of the badge possibly set and saved

			if ($controls['badge'] != '1') 
			{
				$options['img']      = '';
				$options['text']     = '';
				$options['position'] = '';
			}

			// Correction of the value of size is specified in
			// the case the automatically and then use javascript

			if ($controls['width_auto'] == '1') $options['width'] = 'auto';

			// Create the HTML code for the current widget recalling the basic
			// function which is also invoked by the corresponding shortcode

			$OBJC = new SZGoogleActionHangoutsStart();
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
				'title'      => '0', // strip_tags
				'type'       => '1', // strip_tags
				'topic'      => '1', // strip_tags
				'badge'      => '1', // strip_tags
				'text'       => '0', // strip_tags
				'img'        => '1', // strip_tags
				'align'      => '1', // strip_tags
				'position'   => '1', // strip_tags
				'profile'    => '0', // strip_tags
				'email'      => '0', // strip_tags
				'logged'     => '1', // strip_tags
				'guest'      => '1', // strip_tags
				'width'      => '1', // strip_tags
				'width_auto' => '1', // strip_tags
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
				'title'      => '', // default value
				'type'       => '', // default value
				'topic'      => '', // default value
				'badge'      => '', // default value
				'text'       => '', // default value
				'img'        => '', // default value
				'align'      => '', // default value
				'position'   => '', // default value
				'profile'    => '', // default value
				'email'      => '', // default value
				'logged'     => '', // default value
				'guest'      => '', // default value
				'width'      => '', // default value
				'width_auto' => '', // default value
			);

			// Creating arrays for list of fields to be retrieved FORM
			// and loading the file with the HTML template to display

			extract(wp_parse_args($instance,$array),EXTR_OVERWRITE);

			// Setting any of the default parameters for the
			// fields that contain invalid values ​​or inconsistent

			if (!ctype_digit($width) or $width == 0) { $width = 'auto'; $width_auto = '1'; }

			// Reading of the options for the control of default values
			// be assigned to the widget when it is placed in the sidebar

			if ($object = SZGoogleModule::getObject('SZGoogleModuleHangouts')) 
			{
				$options = (object) $object->getOptions();

				// Controllo se la stringa contiene un valore coerente con la
				// selezione del parametro sia come valore numerico che carattere

				$YESNO = array('1','0','n','y');

				if (!in_array($logged,$YESNO)) $logged = $options->hangouts_start_logged;
				if (!in_array($guest ,$YESNO)) $guest  = $options->hangouts_start_guest;
			}

			// Setting any of the default parameters for the
			// fields that contain invalid values ​​or inconsistent

			$logged = str_replace(array('0','1'),array('n','y'),$logged);
			$guest  = str_replace(array('0','1'),array('n','y'),$guest);

			// Calling the template for displaying the part 
			// that concerns the administration panel (admin)

			@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/SZGoogleWidget.php');
			@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/' .__CLASS__.'.php');
		}
	}
}