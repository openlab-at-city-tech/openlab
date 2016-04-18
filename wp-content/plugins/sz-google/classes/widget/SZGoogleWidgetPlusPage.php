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

if (!class_exists('SZGoogleWidgetPlusPage'))
{
	class SZGoogleWidgetPlusPage extends SZGoogleWidget 
	{
		/**
		 * Definition the constructor function, which is called
		 * at the time of the creation of an instance of this class
		 */

		function __construct() 
		{
			parent::__construct('SZ-Google-Page',__('SZ-Google - G+ Page','sz-google'),array(
				'classname'   => 'sz-widget-google sz-widget-google-plus sz-widget-google-plus-page', 
				'description' => ucfirst(__('google+ page.','sz-google'))
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
				'id'        => '', // default value
				'type'      => '', // default value
				'width'     => '', // default value
				'align'     => '', // default value
				'layout'    => '', // default value
				'theme'     => '', // default value
				'cover'     => '', // default value
				'tagline'   => '', // default value
				'publisher' => '', // default value
				'text'      => '', // default value
				'image'     => '', // default value
			),$instance);

			// Definition of the control variables of the widget, these values​
			// do not affect the items of basic but affect some aspects

			$controls = $this->common_empty(array(
				'method'     => '', // default value
				'specific'   => '', // default value
				'width_auto' => '', // default value
			),$instance);

			// Correction of the value of size is specified in
			// the case the automatically and then use javascript

			if ($controls['method']     != '1') $options['id']    = $controls['specific']; 
			if ($controls['method']     == '1') $options['id']    = ''; 
			if ($controls['width_auto'] == '1') $options['width'] = 'auto';

			// Create the HTML code for the current widget recalling the basic
			// function which is also invoked by the corresponding shortcode

			if ($object = SZGoogleModule::getObject('SZGoogleModulePlus')) {
				$HTML = $object->getPlusPageShortcode($options);
			}

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
				'method'     => '1', // strip_tags
				'specific'   => '1', // strip_tags
				'width'      => '1', // strip_tags
				'width_auto' => '1', // strip_tags
				'align'      => '1', // strip_tags
				'layout'     => '1', // strip_tags
				'theme'      => '1', // strip_tags
				'cover'      => '1', // strip_tags
				'tagline'    => '1', // strip_tags
				'publisher'  => '1', // strip_tags
				'text'       => '1', // strip_tags
				'image'      => '1', // strip_tags
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
				'method'     => '', // default value
				'specific'   => '', // default value
				'width'      => '', // default value
				'width_auto' => '', // default value
				'align'      => '', // default value
				'layout'     => '', // default value
				'theme'      => '', // default value
				'cover'      => '', // default value
				'tagline'    => '', // default value
				'publisher'  => '', // default value
				'text'       => '', // default value
				'image'      => '', // default value
			);

			// Creating arrays for list of fields to be retrieved FORM
			// and loading the file with the HTML template to display

			extract(wp_parse_args($instance,$array),EXTR_OVERWRITE);

			// Reading of the options for the control of default values
			// be assigned to the widget when it is placed in the sidebar

			if ($object = SZGoogleModule::getObject('SZGoogleModulePlus')) 
			{
				$options = (object) $object->getOptions();

				if (!ctype_digit($width) and $width != 'auto') {
					if($layout == 'landscape') $width = $options->plus_widget_size_landscape;
						else $width = $options->plus_widget_size_portrait;
				}
			}

			// Setting any of the default parameters for the
			// fields that contain invalid values ​​or inconsistent

			$DEFAULT = include(dirname(SZ_PLUGIN_GOOGLE_MAIN)."/options/sz_google_options_plus.php");

			if (!in_array($cover    ,array('true','false')))         $cover     = 'true';
			if (!in_array($tagline  ,array('true','false')))         $tagline   = 'true';
			if (!in_array($publisher,array('true','false')))         $publisher = 'false';
			if (!in_array($theme    ,array('light','dark')))         $theme     = 'light';
			if (!in_array($layout   ,array('portrait','landscape'))) $layout    = 'portrait';
			if (!in_array($type     ,array('standard','popup')))     $type      = 'standard';

			if (!ctype_digit($method) or $method == 0) { $method = '1'; }

			if (!ctype_digit($width)  or $width  == 0) { 
				if($layout == 'landscape') $width = $DEFAULT['plus_widget_size_landscape']['value'];  
					else $width = $DEFAULT['plus_widget_size_portrait']['value'];
				$width_auto = '1';
			}
			
			// Calling the template for displaying the part 
			// that concerns the administration panel (admin)

			@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/SZGoogleWidget.php');
			@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/' .__CLASS__.'.php');
		}
	}
}