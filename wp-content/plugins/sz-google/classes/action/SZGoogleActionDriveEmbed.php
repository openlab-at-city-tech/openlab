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

if (!class_exists('SZGoogleActionDriveEmbed'))
{
	class SZGoogleActionDriveEmbed extends SZGoogleAction
	{
		/**
		 * Function to create the HTML code of the
		 * module connected to the shortcode required
		 */

		function getShortcode($atts,$content=null) 
		{
			return $this->getHTMLCode(shortcode_atts(array(
				'type'          => '', // default value
				'id'            => '', // default value
				'single'        => '', // default value
				'gid'           => '', // default value
				'range'         => '', // default value
				'start'         => '', // default value
				'loop'          => '', // default value
				'delay'         => '', // default value
				'folderview'    => '', // default value
				'width'         => '', // default value
				'height'        => '', // default value
				'margintop'     => '', // default value
				'marginright'   => '', // default value
				'marginbottom'  => '', // default value
				'marginleft'    => '', // default value
				'marginunit'    => '', // default value
				'action'        => 'shortcode',
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
				'type'          => '', // default value
				'id'            => '', // default value
				'single'        => '', // default value
				'gid'           => '', // default value
				'range'         => '', // default value
				'start'         => '', // default value
				'loop'          => '', // default value
				'delay'         => '', // default value
				'folderview'    => '', // default value
				'width'         => '', // default value
				'height'        => '', // default value
				'margintop'     => '', // default value
				'marginright'   => '', // default value
				'marginbottom'  => '', // default value
				'marginleft'    => '', // default value
				'marginunit'    => '', // default value
				'action'        => '', // default value
			),$atts));

			// Loading options for the configuration variables 
			// containing the default values ​​for shortcodes and widgets

			$options = $this->getModuleOptions('SZGoogleModuleDrive');

			// I delete spaces added and execute the transformation in string
			// lowercase for the control of special values ​​such as "auto"

			$id            = trim($id);
			$type          = strtolower(trim($type));
			$single        = strtolower(trim($single));
			$gid           = strtolower(trim($gid));
			$range         = strtolower(trim($range));
			$start         = strtolower(trim($start));
			$loop          = strtolower(trim($loop));
			$delay         = strtolower(trim($delay));
			$folderview    = strtolower(trim($folderview));
			$width         = strtolower(trim($width));
			$height        = strtolower(trim($height));
			$margintop     = strtolower(trim($margintop));
			$marginright   = strtolower(trim($marginright));
			$marginbottom  = strtolower(trim($marginbottom));
			$marginleft    = strtolower(trim($marginleft));
			$marginunit    = strtolower(trim($marginunit));

			// if not specifically a valid URL for the creation of the
			// button. I leave the function and return an empty string

			if (empty($id)) { return ''; }

			// Control settings that affect the size of the component
			// because some documents have a default size specification

			if ($action == 'widget') 
			{
				if ($width  == '') $width  = $options['drive_embed_w_width'];
				if ($height == '') 
				{
					if ($type == 'document')     $height = $options['drive_embed_w_height'];
					if ($type == 'folder')       $height = $options['drive_embed_w_height'];
					if ($type == 'pdf')          $height = $options['drive_embed_w_height'];
					if ($type == 'forms')        $height = $options['drive_embed_w_height'];
					if ($type == 'presentation') $height = $options['drive_embed_w_height_p'];
					if ($type == 'spreadsheet')  $height = $options['drive_embed_w_height'];
					if ($type == 'video')        $height = $options['drive_embed_w_height_v'];
				}

			// Control settings that affect the size of the component
			// because some documents have a default size specification

			} else {

				if ($width  == '') $width  = $options['drive_embed_s_width'];
				if ($height == '') 
				{
					if ($type == 'document')     $height = $options['drive_embed_s_height'];
					if ($type == 'folder')       $height = $options['drive_embed_s_height'];
					if ($type == 'pdf')          $height = $options['drive_embed_s_height'];
					if ($type == 'forms')        $height = $options['drive_embed_s_height'];
					if ($type == 'presentation') $height = $options['drive_embed_s_height_p'];
					if ($type == 'spreadsheet')  $height = $options['drive_embed_s_height'];
					if ($type == 'video')        $height = $options['drive_embed_s_height_v'];
				}
			}

			// Control variables used as options to use if there
			// are no values ​​that are inconsistent with those permitted

			if (!in_array($start ,array('true','false'))) $start  = 'false'; 
			if (!in_array($loop  ,array('true','false'))) $loop   = 'false'; 
			if (!in_array($single,array('true','false'))) $single = 'false'; 

			if (!in_array($type,array('document','folder','forms','image','pdf','presentation','spreadsheet','video'))) $type = 'document'; 
			if (!in_array($folderview,array('list','grid'))) $folderview = 'list'; 

			// Checking the numeric fields and make sure they do not contain
			// non-numeric characters, in this case apply the default values

			if (!ctype_digit($delay)) $delay  = '3';
			if (!ctype_digit($width)) $width  = '';
			if (!ctype_digit($height))$height = '';
			if (!ctype_digit($gid))   $gid    = '0';

			// Configuration variables to create the HTML
			// code to use respecting the required options

			if ($type == 'document')     $optionSRC = 'https://docs.google.com/document/d/%s/pub?embedded=true';
			if ($type == 'folder')       $optionSRC = 'https://docs.google.com/embeddedfolderview?id=%s#%s';
			if ($type == 'forms')        $optionSRC = 'https://docs.google.com/forms/d/%s/viewform?embedded=true';
			if ($type == 'image')        $optionSRC = 'https://drive.google.com/uc?export=view&id=%s';
			if ($type == 'pdf')          $optionSRC = 'https://docs.google.com/file/d/%s/preview';
			if ($type == 'presentation') $optionSRC = 'https://docs.google.com/presentation/d/%s/embed?start=%s&amp;loop=%s&amp;delayms=%s';
			if ($type == 'spreadsheet')  $optionSRC = 'https://docs.google.com/spreadsheet/pub?key=%s&amp;output=html&amp;widget=true&amp;single=%s&amp;gid=%s';
			if ($type == 'video')        $optionSRC = 'https://docs.google.com/file/d/%s/preview';

			// Control the size of the widget and formal control of the numerical
			// values, if I find some inconsistency apply the default preset

			if ($width  == '')     $width = "100%";
			if ($width  == 'auto') $width = "100%";

			if (in_array($type,array('image'))) {
				if ($height == '')        $height = 'auto';
				if (ctype_digit($width))  $width  = $width.'px';
				if (ctype_digit($height)) $height = $height.'px';
			}

			if (in_array($type,array('presentation','video'))) {
				if ($height == '')     $height = '250';
				if ($height == 'auto') $height = '250';
			}

			if (in_array($type,array('document','folder','forms','pdf','spreadsheet'))) {
				if ($height == '')     $height = '400';
				if ($height == 'auto') $height = '400';
			}

			// Creating CSS code for the composition of the margins
			// using the specified options in shortcode or PHP functions

			$marginCSS = $this->getModuleObject('SZGoogleModuleDrive')->getStyleCSSfromMargins(
				$margintop,$marginright,$marginbottom,$marginleft,$marginunit);

			// Creating URL iframe according to the type required and the various
			// formats which differ by the number and name of the allowed options

			if ($type == 'document')     $optionSRC = sprintf($optionSRC,urlencode($id));
			if ($type == 'folder')       $optionSRC = sprintf($optionSRC,urlencode($id),urlencode($folderview));
			if ($type == 'forms')        $optionSRC = sprintf($optionSRC,urlencode($id));
			if ($type == 'image')        $optionSRC = sprintf($optionSRC,urlencode($id));
			if ($type == 'pdf')          $optionSRC = sprintf($optionSRC,urlencode($id));
			if ($type == 'presentation') $optionSRC = sprintf($optionSRC,urlencode($id),urlencode($start),urlencode($loop),urlencode($delay.'000'));
			if ($type == 'spreadsheet')  $optionSRC = sprintf($optionSRC,urlencode($id),urlencode($single),urlencode($gid),urlencode($range));
			if ($type == 'video')        $optionSRC = sprintf($optionSRC,urlencode($id));

			if ($type == 'spreadsheet' && $range != '') $optionSRC .= "&amp;range=".urlencode($range);

			// Opening of the divisions that represent the
			// common wrapper on any customizations display

			$HTML  = '<div class="sz-google-drive">';
			$HTML .= '<div class="sz-google-drive-embed" style="'.$marginCSS.'">';

			if ($type == 'document')     $HTML .= $this->getHTMLFile($optionSRC,$width,$height);
			if ($type == 'folder')       $HTML .= $this->getHTMLFile($optionSRC,$width,$height);
			if ($type == 'forms')        $HTML .= $this->getHTMLFile($optionSRC,$width,$height);
			if ($type == 'image')        $HTML .= $this->getHTMLImage($optionSRC,$width,$height);
			if ($type == 'pdf')          $HTML .= $this->getHTMLFile($optionSRC,$width,$height);
			if ($type == 'presentation') $HTML .= $this->getHTMLPresentation($optionSRC,$width,$height);
			if ($type == 'spreadsheet')  $HTML .= $this->getHTMLFile($optionSRC,$width,$height);
			if ($type == 'video')        $HTML .= $this->getHTMLFile($optionSRC,$width,$height);

			// Closing of the divisions that represent the
			// common wrapper on any customizations display

			$HTML .= '</div>';
			$HTML .= '</div>';

			// Return from the function with the whole string containing 
			// the HTML code for inserting the code in the page

			return $HTML;
		}

		/**
		 * Creating HTML code for the component called to
		 * be used in common for both widgets and shortcode
		 */

		function getHTMLFile($optionSRC,$width,$height)
		{
			$HTML  = '<div class="sz-google-drive-embed-embed">';
			$HTML .= '<script type="text/javascript">';
			$HTML .= "var h='<'+'";
			$HTML .= 'iframe src="%s" width="%s" height="%s"';
			$HTML .= ' frameborder="0"';
			$HTML .= ' style="border:none;"';
			$HTML .= "></'+'iframe'+'>';";
			$HTML .= "document.write(h);";
			$HTML .= '</script>';
			$HTML .= '</div>';

			$HTML  = sprintf($HTML,$optionSRC,$width,$height);

			// Return from the function with the whole string containing 
			// the HTML code for inserting the code in the page

			return $HTML;
		}

		/**
		 * Creating HTML code for the component called to
		 * be used in common for both widgets and shortcode
		 */

		function getHTMLPresentation($optionSRC,$width,$height)
		{
			$HTML  = '<div class="sz-google-drive-embed-embed">';
			$HTML .= '<script type="text/javascript">';
			$HTML .= "var h='<'+'";
			$HTML .= 'iframe src="%s" width="%s" height="%s"';
			$HTML .= ' frameborder="0"';
			$HTML .= ' style="border:none;"';
			$HTML .= ' allowfullscreen="true"';
			$HTML .= ' mozallowfullscreen="true"';
			$HTML .= ' webkitallowfullscreen="true"';
			$HTML .= "></'+'iframe'+'>';";
			$HTML .= "document.write(h);";
			$HTML .= '</script>';
			$HTML .= '</div>';

			$HTML  = sprintf($HTML,$optionSRC,$width,$height);

			// Return from the function with the whole string containing 
			// the HTML code for inserting the code in the page

			return $HTML;
		}

		/**
		 * Creating HTML code for the component called to
		 * be used in common for both widgets and shortcode
		 */

		function getHTMLImage($optionSRC,$width,$height)
		{
			$HTML  = '<div class="sz-google-drive-embed-embed" ';
			$HTML .= 'style="width:%s;height:%s">';
			$HTML .= '<img src="%s" style="width:100%%;height:auto"/>';
			$HTML .= '</div>';

			$HTML  = sprintf($HTML,$width,$height,$optionSRC);

			// Return from the function with the whole string containing 
			// the HTML code for inserting the code in the page

			return $HTML;
		}
	}
}