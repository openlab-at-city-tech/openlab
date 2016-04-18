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

if (!class_exists('SZGoogleWidgetPlusAuthorBadge'))
{
	class SZGoogleWidgetPlusAuthorBadge extends SZGoogleWidget
	{
		/**
		 * Definition the constructor function, which is called
		 * at the time of the creation of an instance of this class
		 */

		function __construct() 
		{
			parent::__construct('SZ-Google-Plus-Author-Badge',__('SZ-Google - G+ Author badge','sz-google'),array(
				'classname'   => 'sz-widget-google sz-widget-google-plus sz-widget-google-plus-author-badge', 
				'description' => ucfirst(__('google+ author badge.','sz-google'))
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
				'mode'      => '',  // default value
				'link'      => '',  // default value
				'cover'     => '',  // default value
				'photo'     => '',  // default value
				'biography' => '',  // default value
				'width'     => '',  // default value
				'action'    => 'W', // default value
			),$instance);

			// Definition of the control variables of the widget, these values​
			// do not affect the items of basic but affect some aspects

			$controls = $this->common_empty(array(
				'width_auto' => '', // default value
			),$instance);

			// Correction of the value of size is specified in
			// the case the automatically and then use javascript

			if ($controls['width_auto'] == '1') $options['width'] = 'auto';

			// Create the HTML code for the current widget recalling the basic
			// function which is also invoked by the corresponding shortcode

			$OBJC = new SZGoogleActionPlusAuthorBadge();
			$HTML = $OBJC->getHTMLCode($options);

			// Output HTML code linked to the widget to
			// display call to the general standard for wrap

			if ($HTML != NULL) echo $this->common_widget($args,$instance,$HTML);
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
				'mode'       => '0', // strip_tags
				'link'       => '0', // strip_tags
				'cover'      => '0', // strip_tags
				'photo'      => '0', // strip_tags
				'biography'  => '0', // strip_tags
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
				'mode'       => '', // default value
				'link'       => '',  // default value
				'cover'      => '',  // default value
				'photo'      => '',  // default value
				'biography'  => '',  // default value
				'width'      => '',  // default value
				'width_auto' => '',  // default value
			);

			// Creating arrays for list of fields to be retrieved FORM
			// and loading the file with the HTML template to display

			$OBJC = new SZGoogleActionPlusAuthorBadge();
			extract((array) $OBJC->checkOptions(wp_parse_args($instance,$array),EXTR_OVERWRITE));

			// Calling the template for displaying the part 
			// that concerns the administration panel (admin)

			@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/SZGoogleWidget.php');
			@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/' .__CLASS__.'.php');
		}
	}
}