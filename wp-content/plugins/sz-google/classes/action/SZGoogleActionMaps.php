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

if (!class_exists('SZGoogleActionMaps'))
{
	class SZGoogleActionMaps extends SZGoogleAction
	{
		static private $sequenceMaps = 0;

		/**
		 * Function to create the HTML code of the
		 * module connected to the shortcode required
		 */

		function getShortcode($atts,$content=null) 
		{
			return $this->getHTMLCode(shortcode_atts(array(
				'width'    => '',  // default value
				'height'   => '',  // default value
				'lat'      => '',  // default value
				'lng'      => '',  // default value
				'zoom'     => '',  // default value
				'view'     => '',  // default value
				'layer'    => '',  // default value
				'wheel'    => '',  // default value
				'marker'   => '',  // default value
				'lazyload' => '',  // default value
				'action'   => 'S', // default value
			),$atts),$content);
		}

		/**
		 * Create HTML for the component called to be
		 * used in common for both widgets and shortcodes
		 */

		function getHTMLCode($atts=array(),$content=null)
		{
			if (!is_array($atts)) $atts = array();

			// Extracting the values ​​specified in the options, return values
			// ​​are contained in the variable names corresponding to the key

			$options = shortcode_atts(array(
				'width'    => '', // default value
				'height'   => '', // default value
				'lat'      => '', // default value
				'lng'      => '', // default value
				'zoom'     => '', // default value
				'view'     => '', // default value
				'layer'    => '', // default value
				'wheel'    => '', // default value
				'marker'   => '', // default value
				'lazyload' => '', // default value
				'action'   => '', // default value
			),$atts);

			$keyatts = $this->checkOptions($options);

			// Check the numerical value of the scale and add the code to pixels
			// This string will be used to create the code of the CSS style

			if ($keyatts->width  == 'auto' or $keyatts->width  == '') $keyatts->width  = "100%";
			if ($keyatts->height == 'auto' or $keyatts->height == '') $keyatts->height = 'auto';

			if (ctype_digit($keyatts->width) ) $keyatts->width  .= 'px'; 
			if (ctype_digit($keyatts->height)) $keyatts->height .= 'px'; 

			// Creating a unique identifier to recognize the embed code, in the
			// case where the function is called multiple times in the same page

			$keyatts->unique = ++self::$sequenceMaps;
			$keyatts->idHTML = 'sz-google-maps-id-'.$keyatts->unique;

			// Creating HTML for embed code add to the page wordpress
			// To read infrormazioni parameters see the documentation

			if ($keyatts->height == 'auto') 
			{
				$HTML  = '<div class="sz-google-maps">';
				$HTML .= '<div class="sz-google-maps-wrap" style="';
				$HTML .= 'width:'.$keyatts->width.';';
				$HTML .= 'position:relative;padding-bottom:75%;height:0;overflow:hidden;';
				$HTML .= '">';
				$HTML .= '<div id="'.$keyatts->idHTML.'" style="width:100%;height:100%;position:absolute;top:0;left:0;"></div>';
				$HTML .= '</div>';
				$HTML .= '</div>';

			} else {

				$HTML  = '<div class="sz-google-maps">';
				$HTML .= '<div class="sz-google-maps-wrap" style="';
				$HTML .= 'width:' .$keyatts->width .';';
				$HTML .= 'height:'.$keyatts->height.';';
				$HTML .= '">';
				$HTML .= '<div id="'.$keyatts->idHTML.'" style="width:100%;height:100%"></div>';
				$HTML .= '</div>';
				$HTML .= '</div>';
			}

			// Adding the JavaScript code for rendering widget, 
			// This code also add the sidebar, but is entered only once

			$this->getModuleObject('SZGoogleModuleMaps')->addCodeJavascriptFooter(array(
				'idHTML'   => $keyatts->idHTML,
				'unique'   => $keyatts->unique,
				'width'    => $keyatts->width,
				'height'   => $keyatts->height,
				'lat'      => $keyatts->lat,
				'lng'      => $keyatts->lng,
				'zoom'     => $keyatts->zoom,
				'view'     => $keyatts->view,
				'wheel'    => $keyatts->wheel,
				'marker'   => $keyatts->marker,
				'lazyload' => $keyatts->lazyload,
				'layer'    => $keyatts->layer,
			));

			// Return the whole string containing
			// HTML to insert the code in the page

			return $HTML;
		}

		/**
		 * Create HTML for the component called to be
		 * used in common for both widgets and shortcodes
		 */

		function checkOptions($options=array())
		{
			// Loading options for the configuration variables that
			// contain the default values for shortcodes and widgets

			$check = (object) $options;
			$admin = (object) $this->getModuleOptions('SZGoogleModuleMaps');

			// Deleting spaces added too and execute the transformation to a
			// string lowercase for the control of special values ​​such as "auto"

			$check->lat      = trim($check->lat);
			$check->lng      = trim($check->lng);
			$check->zoom     = trim($check->zoom);

			$check->view     = strtoupper(trim($check->view));
			$check->width    = strtolower(trim($check->width));
			$check->height   = strtolower(trim($check->height));
			$check->wheel    = strtolower(trim($check->wheel));
			$check->marker   = strtolower(trim($check->marker));
			$check->lazyload = strtolower(trim($check->lazyload));

			// Control the default values related to the shortcode
			// Replace the values if these were not specified

			if ($check->wheel    == 'true' ) $check->wheel    = '1';
			if ($check->marker   == 'true' ) $check->marker   = '1';
			if ($check->lazyload == 'true' ) $check->lazyload = '1';

			if ($check->wheel    == 'false') $check->wheel    = '0';
			if ($check->marker   == 'false') $check->marker   = '0';
			if ($check->lazyload == 'false') $check->lazyload = '0';

			// Control the default values related to the shortcode
			// Replace the values if these were not specified

			if ($check->action == 'S') {
				if ($check->width    == '') $check->width    = $admin->maps_s_width;
				if ($check->height   == '') $check->height   = $admin->maps_s_height;
				if ($check->lat      == '') $check->lat      = $admin->maps_s_lat;
				if ($check->lng      == '') $check->lng      = $admin->maps_s_lng;
				if ($check->zoom     == '') $check->zoom     = $admin->maps_s_zoom;
				if ($check->view     == '') $check->view     = $admin->maps_s_view;
				if ($check->layer    == '') $check->layer    = $admin->maps_s_layer;
				if ($check->wheel    == '') $check->wheel    = $admin->maps_s_wheel;
				if ($check->marker   == '') $check->marker   = $admin->maps_s_marker;
				if ($check->lazyload == '') $check->lazyload = $admin->maps_s_lazy;
			}

			// Control the default values related to the widget
			// Replace the values if these were not specified

			if ($check->action == 'W') {
				if ($check->width    == '') $check->width    = $admin->maps_w_width;
				if ($check->height   == '') $check->height   = $admin->maps_w_height;
				if ($check->lat      == '') $check->lat      = $admin->maps_w_lat;
				if ($check->lng      == '') $check->lng      = $admin->maps_w_lng;
				if ($check->zoom     == '') $check->zoom     = $admin->maps_w_zoom;
				if ($check->view     == '') $check->view     = $admin->maps_w_view;
				if ($check->layer    == '') $check->layer    = $admin->maps_w_layer;
				if ($check->wheel    == '') $check->wheel    = $admin->maps_w_wheel;
				if ($check->marker   == '') $check->marker   = $admin->maps_w_marker;
				if ($check->lazyload == '') $check->lazyload = $admin->maps_w_lazy;
			}

			// Check the values passed in arrays that specify the size
			// if the dimension must be calculated automatically

			if (!ctype_digit($check->width)  and $check->width  != 'auto') $check->width  = 'auto'; 
			if (!ctype_digit($check->height) and $check->height != 'auto') $check->height = 'auto'; 

			// Control the zoom value of google maps
			// Must be between 1 and a value of 20

			if (!ctype_digit($check->zoom)) $check->zoom = '8';
			if ($check->zoom < '1' or $check->zoom > '20') $check->zoom = '8';

			// Control the type view of google maps
			// Must be ROADMAP, SATELLITE, HYBRID and TERRAIN

			if ($check->action == 'S' or $check->action == 'W') {
				if (!in_array($check->wheel    ,array('0','1',''))) $check->wheel    = '0';
				if (!in_array($check->marker   ,array('0','1',''))) $check->marker   = '0';
				if (!in_array($check->lazyload ,array('0','1',''))) $check->lazyload = '0';
				if (!in_array($check->view     ,array('ROADMAP','SATELLITE','HYBRID','TERRAIN'))) $check->view  = 'ROADMAP';
				if (!in_array($check->layer    ,array('NOTHING','TRAFFIC','TRANSIT','BICYCLE')))  $check->layer = 'NOTHING';
			}

			// Setting any of the default parameters for
			// fields that contain invalid values or inconsistent

			$DEFAULT = include(dirname(SZ_PLUGIN_GOOGLE_MAIN)."/options/sz_google_options_maps.php");

			if ($check->action == 'S') {
				if (!ctype_digit($check->zoom)   or $check->zoom   == 0) { $check->width  = $DEFAULT['maps_s_zoom'  ]['value']; }
				if (!ctype_digit($check->width)  or $check->width  == 0) { $check->width  = $DEFAULT['maps_s_width' ]['value']; $check->width_auto  = '1'; }
				if (!ctype_digit($check->height) or $check->height == 0) { $check->height = $DEFAULT['maps_s_height']['value']; $check->height_auto = '1'; }
			}

			if ($check->action == 'W') {
				if (!ctype_digit($check->zoom)   or $check->zoom   == 0) { $check->width  = $DEFAULT['maps_w_zoom'  ]['value']; }
				if (!ctype_digit($check->width)  or $check->width  == 0) { $check->width  = $DEFAULT['maps_w_width' ]['value']; $check->width_auto  = '1'; }
				if (!ctype_digit($check->height) or $check->height == 0) { $check->height = $DEFAULT['maps_w_height']['value']; $check->height_auto = '1'; }
			}

			if ($check->width  == 'auto') $check->width_auto  = '1';
			if ($check->height == 'auto') $check->height_auto = '1';

			// Return the correct parameters to the calling function
			// The format of the return is an object not an array

			return $check;
		}
	}
}