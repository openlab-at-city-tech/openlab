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

if (!class_exists('SZGoogleWidgetPanoramio'))
{
	class SZGoogleWidgetPanoramio extends SZGoogleWidget
	{
		/**
		 * Definition the constructor function, which is called
		 * at the time of the creation of an instance of this class
		 */

		function __construct() 
		{
			parent::__construct('sz-google-panoramio',__('SZ-Google - Panoramio','sz-google'),array(
				'classname'   => 'sz-widget-google sz-widget-google-panoramio sz-widget-google-panoramio-iframe', 
				'description' => ucfirst(__('panoramio photos.','sz-google'))
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
				'template'    => '', // default value
				'width'       => '', // default value
				'height'      => '', // default value
				'user'        => '', // default value
				'group'       => '', // default value
				'tag'         => '', // default value
				'set'         => '', // default value
				'columns'     => '', // default value
				'rows'        => '', // default value
				'orientation' => '', // default value
				'position'    => '', // default value
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

			$OBJC = new SZGoogleActionPanoramio();
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
				'template'    => '1', // strip_tags
				'width'       => '1', // strip_tags
				'width_auto'  => '1', // strip_tags
				'height'      => '1', // strip_tags
				'height_auto' => '1', // strip_tags
				'user'        => '1', // strip_tags
				'group'       => '1', // strip_tags
				'tag'         => '1', // strip_tags
				'set'         => '1', // strip_tags
				'columns'     => '1', // strip_tags
				'rows'        => '1', // strip_tags
				'orientation' => '1', // strip_tags
				'position'    => '1', // strip_tags
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
				'template'    => '', // default value
				'width'       => '', // default value
				'width_auto'  => '', // default value
				'height'      => '', // default value
				'height_auto' => '', // default value
				'user'        => '', // default value
				'group'       => '', // default value
				'tag'         => '', // default value
				'set'         => '', // default value
				'columns'     => '', // default value
				'rows'        => '', // default value
				'orientation' => '', // default value
				'position'    => '', // default value
			);

			// Creating arrays for list of fields to be retrieved FORM
			// and loading the file with the HTML template to display

			extract(wp_parse_args($instance,$array),EXTR_OVERWRITE);

			// Reading of the options for the control of default values
			// be assigned to the widget when it is placed in the sidebar

			if ($object = SZGoogleModule::getObject('SZGoogleModulePanoramio')) 
			{
				$options = (object) $object->getOptions();

				if (!ctype_digit($columns)) $columns = $options->panoramio_w_columns;
				if (!ctype_digit($rows))    $rows    = $options->panoramio_w_rows;

				if (!in_array($template    ,array('photo','slideshow','list','photo_list'))) $options->panoramio_w_template;
				if (!in_array($set         ,array('all','public','recent')))                 $options->panoramio_w_set;
				if (!in_array($orientation ,array('horizontal','vertical')))                 $options->panoramio_w_orientation;
				if (!in_array($position    ,array('left','top','right','bottom')))           $options->panoramio_w_position;

				if (!ctype_digit($width)  and $width  != 'auto') $width  = $options->panoramio_w_width;
				if (!ctype_digit($height) and $height != 'auto') $height = $options->panoramio_w_height;
			}

			// Setting any of the default parameters for the
			// fields that contain invalid values ​​or inconsistent

			$DEFAULT = include(dirname(SZ_PLUGIN_GOOGLE_MAIN)."/options/sz_google_options_panoramio.php");

			if (!ctype_digit($columns)) $columns = $DEFAULT['panoramio_w_columns']['value'];
			if (!ctype_digit($rows))    $rows    = $DEFAULT['panoramio_w_rows']['value'];

			if (!in_array($template    ,array('photo','slideshow','list','photo_list'))) $template    = $DEFAULT['panoramio_w_template']['value'];
			if (!in_array($set         ,array('all','public','recent')))                 $set         = $DEFAULT['panoramio_w_set']['value'];
			if (!in_array($orientation ,array('horizontal','vertical')))                 $orientation = $DEFAULT['panoramio_w_orientation']['value'];
			if (!in_array($position    ,array('left','top','right','bottom')))           $position    = $DEFAULT['panoramio_w_position']['value'];

			if (!ctype_digit($width)  or $width  == 0) { $width  = $DEFAULT['panoramio_w_width']['value'];  $width_auto  = '1'; }
			if (!ctype_digit($height) or $height == 0) { $height = $DEFAULT['panoramio_w_height']['value']; $height_auto = '1'; }

			// Calling the template for displaying the part 
			// that concerns the administration panel (admin)

			@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/SZGoogleWidget.php');
			@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/' .__CLASS__.'.php');
		}
	}
}