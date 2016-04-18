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

if (!class_exists('SZGoogleWidgetPlusComments'))
{
	class SZGoogleWidgetPlusComments extends SZGoogleWidget
	{
		/**
		 * Definition the constructor function, which is called
		 * at the time of the creation of an instance of this class
		 */

		function __construct() 
		{
			parent::__construct('SZ-Google-Comments',__('SZ-Google - G+ Comments','sz-google'),array(
				'classname'   => 'sz-widget-google sz-widget-google-plus sz-widget-google-plus-comments', 
				'description' => ucfirst(__('google+ comments.','sz-google'))
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
				'url'        => '', // default value
				'width'      => '', // default value
				'align'      => '', // default value
				'title'      => '', // default value
				'class1'     => '', // default value
				'class2'     => '', // default value
			),$instance);

			// Definition of the control variables of the widget, these values​
			// do not affect the items of basic but affect some aspects

			$controls = $this->common_empty(array(
				'method'     => '', // default value
				'width_auto' => '', // default value
			),$instance);

			// Correction of the value of size is specified in
			// the case the automatically and then use javascript

			if ($controls['method']     == '1') $options['url']   = '';
			if ($controls['width_auto'] == '1') $options['width'] = 'auto';

			// Create the HTML code for the current widget recalling the basic
			// function which is also invoked by the corresponding shortcode

			if ($object = SZGoogleModule::getObject('SZGoogleModulePlus')) {
				$HTML = $object->getPlusCommentsShortcode($options);
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
				'method'     => '1', // strip_tags
				'url'        => '0', // strip_tags
				'width'      => '1', // strip_tags
				'width_auto' => '1', // strip_tags
				'align'      => '1', // strip_tags
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
				'url'        => '', // default value
				'method'     => '', // default value
				'width'      => '', // default value
				'width_auto' => '', // default value
				'align'      => '', // default value
			);

			// Creating arrays for list of fields to be retrieved FORM
			// and loading the file with the HTML template to display

			extract(wp_parse_args($instance,$array),EXTR_OVERWRITE);

			// Impostazione eventuale di parametri di default per i
			// campi che contengono dei valori non validi o non coerenti 

			if (!ctype_digit($method) or $method == 0) { $method = '1'; }
			if (!ctype_digit($width)  or $width  == 0) { $width = 'auto'; $width_auto = '1'; }

			// Calling the template for displaying the part 
			// that concerns the administration panel (admin)

			@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/SZGoogleWidget.php');
			@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/' .__CLASS__.'.php');
		}
	}
}