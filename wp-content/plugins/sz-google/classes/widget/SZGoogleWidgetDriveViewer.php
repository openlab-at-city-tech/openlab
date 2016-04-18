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

if (!class_exists('SZGoogleWidgetDriveViewer'))
{
	class SZGoogleWidgetDriveViewer extends SZGoogleWidget
	{
		/**
		 * Definition the constructor function, which is called
		 * at the time of the creation of an instance of this class
		 */

		function __construct() 
		{
			parent::__construct('SZ-Google-Drive-Viewer',__('SZ-Google - Drive Viewer','sz-google'),array(
				'classname'   => 'sz-widget-google sz-widget-google-drive sz-widget-google-drive-viewer', 
				'description' => ucfirst(__('google drive viewer.','sz-google'))
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
				'title'        => '',  // default value
				'url'          => '',  // default value
				'width'        => '',  // default value
				'height'       => '',  // default value
				'pre'          => '',  // default value
				'margintop'    => '0', // default value
				'marginright'  => '0', // default value
				'marginbottom' => '0', // default value
				'marginleft'   => '0', // default value
				'marginunit'   => '',  // default value
			),$instance);

			// Definition of the control variables of the widget, these values​
			// do not affect the items of basic but affect some aspects

			$controls = $this->common_empty(array(
				'width_auto'  => '', // default value
				'height_auto' => '', // default value
			),$instance);

			// Correction of the value of size is specified in
			// the case the automatically and then use javascript

			if ($controls['width_auto']  == '1') $options['width']  = 'auto';
			if ($controls['height_auto'] == '1') $options['height'] = 'auto';

			// Cancel the variable title that belongs to the component  
			// since there is the title of the widget and have the same name

			$options['title'] = '';

			// Create the HTML code for the current widget recalling the basic
			// function which is also invoked by the corresponding shortcode

			$OBJC = new SZGoogleActionDriveViewer();
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
				'title'       => '0', // strip_tags
				'url'         => '1', // strip_tags
				'width'       => '1', // strip_tags
				'width_auto'  => '1', // strip_tags
				'height'      => '1', // strip_tags
				'height_auto' => '1', // strip_tags
				'pre'         => '1', // strip_tags
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
				'title'       => '', // default value
				'url'         => '', // default value
				'width'       => '', // default value
				'width_auto'  => '', // default value
				'height'      => '', // default value
				'height_auto' => '', // default value
				'pre'         => '', // default value
			);

			// Creating arrays for list of fields to be retrieved FORM
			// and loading the file with the HTML template to display

			extract(wp_parse_args($instance,$array),EXTR_OVERWRITE);

			// Reading of the options for the control of default values
			// be assigned to the widget when it is placed in the sidebar

			if ($object = SZGoogleModule::getObject('SZGoogleModuleDrive'))
			{
				$options = (object) $object->getOptions();

				if (!ctype_digit($width)  and $width  != 'auto') $width  = $options->drive_viewer_w_width;
				if (!ctype_digit($height) and $height != 'auto') $height = $options->drive_viewer_w_height;

				// Check if the string contains a value consistent with the
				// selection of the parameter both as a numerical value that character

				$YESNO = array('1','0','n','y');

				if (!in_array($pre,$YESNO)) $pre = $options->drive_viewer_w_wrap_pre;
			}

			// Setting any of the default parameters for the
			// fields that contain invalid values ​​or inconsistent

			$DEFAULT = include(dirname(SZ_PLUGIN_GOOGLE_MAIN)."/options/sz_google_options_drive.php");

			if (!ctype_digit($width)  or $width  == 0) { $width  = $DEFAULT['drive_viewer_w_width']['value'];  $width_auto  = '1'; }
			if (!ctype_digit($height) or $height == 0) { $height = $DEFAULT['drive_viewer_w_height']['value']; $height_auto = '1'; }

			// Setting any of the default parameters for the
			// fields that contain invalid values ​​or inconsistent

			$pre = str_replace(array('0','1'),array('n','y'),$pre);

			// Calling the template for displaying the part 
			// that concerns the administration panel (admin)

			@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/SZGoogleWidget.php');
			@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/' .__CLASS__.'.php');
		}
	}
}