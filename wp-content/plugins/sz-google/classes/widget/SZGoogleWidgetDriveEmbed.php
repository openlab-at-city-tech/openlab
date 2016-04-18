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

if (!class_exists('SZGoogleWidgetDriveEmbed'))
{
	class SZGoogleWidgetDriveEmbed extends SZGoogleWidget
	{
		/**
		 * Definition the constructor function, which is called
		 * at the time of the creation of an instance of this class
		 */

		function __construct() 
		{
			parent::__construct('SZ-Google-Drive-Embed',__('SZ-Google - Drive Embed','sz-google'),array(
				'classname'   => 'sz-widget-google sz-widget-google-drive sz-widget-google-drive-embed', 
				'description' => ucfirst(__('google drive embed.','sz-google'))
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
				'title'        => '', // default value
				'type'         => '', // default value
				'id'           => '', // default value
				'width'        => '', // default value
				'height'       => '', // default value
				'folderview'   => '', // default value
				'single'       => '', // default value
				'gid'          => '', // default value
				'range'        => '', // default value
				'start'        => '', // default value
				'loop'         => '', // default value
				'delay'        => '', // default value
				'margintop'    => '', // default value
				'marginright'  => '', // default value
				'marginbottom' => '', // default value
				'marginleft'   => '', // default value
				'marginunit'   => '', // default value
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

			// Create the HTML code for the current widget recalling the basic
			// function which is also invoked by the corresponding shortcode

			$OBJC = new SZGoogleActionDriveEmbed();
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
				'type'        => '1', // strip_tags
				'id'          => '1', // strip_tags
				'folderview'  => '1', // strip_tags
				'single'      => '1', // strip_tags
				'gid'         => '1', // strip_tags
				'range'       => '1', // strip_tags
				'start'       => '1', // strip_tags
				'loop'        => '1', // strip_tags
				'delay'       => '1', // strip_tags
				'width'       => '1', // strip_tags
				'width_auto'  => '1', // strip_tags
				'height'      => '1', // strip_tags
				'height_auto' => '1', // strip_tags
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
				'type'        => '', // default value
				'id'          => '', // default value
				'folderview'  => '', // default value
				'single'      => '', // default value
				'gid'         => '', // default value
				'range'       => '', // default value
				'start'       => '', // default value
				'loop'        => '', // default value
				'delay'       => '', // default value
				'width'       => '', // default value
				'width_auto'  => '', // default value
				'height'      => '', // default value
				'height_auto' => '', // default value
			);

			// Creating arrays for list of fields to be retrieved FORM
			// and loading the file with the HTML template to display

			extract(wp_parse_args($instance,$array),EXTR_OVERWRITE);

			// Reading of the options for the control of default values
			// be assigned to the widget when it is placed in the sidebar

			if ($object = SZGoogleModule::getObject('SZGoogleModuleDrive')) 
			{
				$options = (object) $object->getOptions();

				if (!ctype_digit($width)  and $width  != 'auto') $width  = $options->drive_embed_w_width;
				if (!ctype_digit($height) and $height != 'auto') $height = $options->drive_embed_w_height;
			}

			// Setting any of the default parameters for the
			// fields that contain invalid values ​​or inconsistent

			$DEFAULT = include(dirname(SZ_PLUGIN_GOOGLE_MAIN)."/options/sz_google_options_drive.php");

			if (!ctype_digit($width)  or $width  == 0) { $width  = $DEFAULT['drive_embed_w_width']['value'];  $width_auto  = '1'; }
			if (!ctype_digit($height) or $height == 0) { $height = $DEFAULT['drive_embed_w_height']['value']; $height_auto = '1'; }

			// Calling the template for displaying the part 
			// that concerns the administration panel (admin)

			@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/SZGoogleWidget.php');
			@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/' .__CLASS__.'.php');
		}
	}
}