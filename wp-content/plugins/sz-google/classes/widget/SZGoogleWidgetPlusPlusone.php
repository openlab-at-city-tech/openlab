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

if (!class_exists('SZGoogleWidgetPlusPlusone'))
{
	class SZGoogleWidgetPlusPlusone extends SZGoogleWidget
	{
		/**
		 * Definition the constructor function, which is called
		 * at the time of the creation of an instance of this class
		 */

		function __construct() 
		{
			parent::__construct('SZ-GOOGLE-PLUS-ONE',__('SZ-Google - G+ Plus one','sz-google'),array(
				'classname'   => 'sz-widget-google sz-widget-google-plus sz-widget-google-plus-one', 
				'description' => ucfirst(__('google +1.','sz-google'))
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
				'url'          => '', // default value
				'width'        => '', // default value
				'size'         => '', // default value
				'annotation'   => '', // default value
				'float'        => '', // default value
				'align'        => '', // default value
				'text'         => '', // default value
				'img'          => '', // default value
				'position'     => '', // default value
				'margintop'    => '', // default value
				'marginright'  => '', // default value
				'marginbottom' => '', // default value
				'marginleft'   => '', // default value
				'marginunit'   => '', // default value
			),$instance);

			// Definition of the control variables of the widget, these values​
			// do not affect the items of basic but affect some aspects

			$controls = $this->common_empty(array(
				'badge'        => '', // default value
				'urltype'      => '', // default value
			),$instance);

			// If the widget I excluded from the badge button I reset
			// the variables of the badge possibly set and saved

			if ($controls['badge']  != '1') {
				$options['img']      = '';
				$options['text']     = '';
				$options['position'] = '';
			}

			// If the widget I selected to calculate the address from
			// the current post cancel the variable with any address

			if ($controls['urltype'] != '1') $options['url'] = '';

			// Create the HTML code for the current widget recalling the basic
			// function which is also invoked by the corresponding shortcode

			if ($object = SZGoogleModule::getObject('SZGoogleModulePlus')) {
				$HTML = $object->getPlusPlusoneShortcode($options);
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
				'badge'      => '1', // strip_tags
				'url'        => '0', // strip_tags
				'urltype'    => '1', // strip_tags
				'text'       => '0', // strip_tags
				'img'        => '0', // strip_tags
				'align'      => '1', // strip_tags
				'position'   => '1', // strip_tags
				'size'       => '1', // strip_tags
				'annotation' => '1', // strip_tags
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
				'badge'      => '', // default value
				'url'        => '', // default value
				'urltype'    => '', // default value
				'text'       => '', // default value
				'img'        => '', // default value
				'align'      => '', // default value
				'position'   => '', // default value
				'size'       => '', // default value
				'annotation' => '', // default value
			);

			// Creating arrays for list of fields to be retrieved FORM
			// and loading the file with the HTML template to display

			extract(wp_parse_args($instance,$array),EXTR_OVERWRITE);

			// Calling the template for displaying the part 
			// that concerns the administration panel (admin)

			@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/SZGoogleWidget.php');
			@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/' .__CLASS__.'.php');
		}
	}
}