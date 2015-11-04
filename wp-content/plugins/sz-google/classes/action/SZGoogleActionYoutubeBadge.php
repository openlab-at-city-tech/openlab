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

if (!class_exists('SZGoogleActionYoutubeBadge'))
{
	class SZGoogleActionYoutubeBadge extends SZGoogleAction
	{
		/**
		 * Function to create the HTML code of the
		 * module connected to the shortcode required
		 */

		function getShortcode($atts,$content=null) 
		{
			return $this->getHTMLCode(shortcode_atts(array(
				'channel'    => '', // default value
				'width'      => '', // default value
				'widthunit'  => '', // default value
				'height'     => '', // default value
				'heightunit' => '', // default value
				'action'     => 'shortcode',
			),$atts),$content);
		}

		/**
		 * Creating HTML code for the component called to
		 * be used in common for both widgets and shortcode
		 */

		function getHTMLCode($atts=array(),$content=null)
		{
			if (!is_array($atts)) $atts = array();

			// Extraction of the values ​​specified in shortcode, returned values
			// ​​are contained in the variable names corresponding to the key

			extract(shortcode_atts(array(
				'channel'    => '', // default value
				'width'      => '', // default value
				'widthunit'  => '', // default value
				'height'     => '', // default value
				'heightunit' => '', // default value
				'action'     => '', // default value
			),$atts));

			// Loading options for the configuration variables 
			// containing the default values ​​for shortcodes and widgets

			$options = (object) $this->getModuleOptions('SZGoogleModuleYoutube');

			// I delete spaces added and execute the transformation in string
			// lowercase for the control of special values ​​such as "auto"

			$channel    = trim($channel);
			$width      = trim($width);
			$widthunit  = trim($widthunit);
			$height     = trim($height);
			$heightunit = trim($heightunit);

			// Se non sono riuscito ad assegnare nessun valore con le istruzioni
			// precedenti metto dei default assoluti che possono essere cambiati

			if ($channel    == '') $channel    = $options->youtube_channel;
			if ($width      == '') $width      = '300';
			if ($height     == '') $height     = '100';
			if ($widthunit  == '') $widthunit  = 'px';
			if ($heightunit == '') $heightunit = 'px';

			// Se non sono riuscito ad assegnare nessun valore con le istruzioni
			// precedenti metto dei default assoluti che possono essere cambiati

			if (!ctype_digit($width))  $width  = '300'; 
			if (!ctype_digit($height)) $height = '100'; 

			// Creazione codice HTML per embed code da inserire nella pagina wordpress

			$HTML  = '<iframe src="https://www.youtube.com/subscribe_widget?p='.$channel.'" ';
			$HTML .= 'style="overflow:hidden;';
			$HTML .= 'width:'.$width.$widthunit.';';
			$HTML .= 'height:'.$height.$heightunit.';';
			$HTML .= 'border:0;" ';
			$HTML .= 'scrolling="no" frameborder="0"';
			$HTML .= '></iframe>';

			// Return from the function with the whole string containing 
			// the HTML code for inserting the code in the page

			return $HTML;
		}
	}
}