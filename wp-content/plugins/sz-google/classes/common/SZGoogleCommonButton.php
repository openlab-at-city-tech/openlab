<?php

/**
 * Class for executing functions of general use or 
 * calculation of variables to be used in plugin
 *
 * @package SZGoogle
 * @subpackage Classes
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Before the definition of the class, check if there is a definition 
// with the same name or the same as previously defined in other script.

if (!class_exists('SZGoogleCommonButton'))
{
	class SZGoogleCommonButton
	{
		/**
		 * Function to design wrapper tied to a button commonly used
		 * in most of the plugins and modules with the same options
		 */

		static function getButton($atts) 
		{
			extract(shortcode_atts(array(
				'html'         => '', // default value
				'text'         => '', // default value
				'image'        => '', // default value
				'content'      => '', // default value
				'float'        => '', // default value
				'align'        => '', // default value
				'position'     => '', // default value
				'class'        => '', // default value
				'margintop'    => '', // default value
				'marginright'  => '', // default value
				'marginbottom' => '', // default value
				'marginleft'   => '', // default value
				'marginunit'   => '', // default value
				'uniqueID'     => '', // default value
			),$atts));

			// Imposed the default values ​​are specified in the case of
			// the values ​​that do not belong to the range of accepted values

			if (!ctype_digit($margintop)    and $margintop    != 'none') $margintop    = ''; 
			if (!ctype_digit($marginright)  and $marginright  != 'none') $marginright  = ''; 
			if (!ctype_digit($marginbottom) and $marginbottom != 'none') $marginbottom = '1'; 
			if (!ctype_digit($marginleft)   and $marginleft   != 'none') $marginleft   = ''; 

			if (!in_array($marginunit,array('px','pt','em'))) $marginunit = 'em';

			// Calculating the CSS code to be entered in the first wrapper
			// of the button on which you are working on the rendering

			$CSS = '';

			if (!empty($float) and $float != 'none') $CSS .= 'float:'.$float.';';
			if (!empty($align) and $align != 'none') $CSS .= 'text-align:'.$align.';';

			// Calculating the HTML code to perform a WRAP on the
			// code of the button prepared earlier by the caller

			$HTML  = '<div class="'.$class.'"';
				if ($CSS      != '') $HTML .= ' style="'.$CSS.'"';
				if ($uniqueID != '') $HTML .= ' id="'.$uniqueID.'"';
			$HTML .= '>';

			$HTML .= '<div class="sz-google-button" style="';

			if (!empty($margintop)    and $margintop    != 'none') $HTML .= 'margin-top:'   .$margintop   .$marginunit.';';
			if (!empty($marginright)  and $marginright  != 'none') $HTML .= 'margin-right:' .$marginright .$marginunit.';';
			if (!empty($marginbottom) and $marginbottom != 'none') $HTML .= 'margin-bottom:'.$marginbottom.$marginunit.';';
			if (!empty($marginleft)   and $marginleft   != 'none') $HTML .= 'margin-left:'  .$marginleft  .$marginunit.';';

			$HTML .= '">';

			$HTML .= '<div class="sz-google-button-wrap" style="position:relative;">';
			$HTML .= '<div class="sz-google-button-body">';

			// If I find content for the parameter "text" of the shortcod
			// I add it before the original embed code google

			if ($text != '') {
				$HTML .= '<div class="sz-google-button-text">';
				$HTML .= '<p>'.$text.'</p>';
				$HTML .= '</div>';
			}

			// If I find content for the parameter "image" of the shortcod
			// I add it before the original embed code google

			if ($image != '') {
				$HTML .= '<div class="sz-google-button-imgs">';
				$HTML .= '<p><img src="'.$image.'" alt=""/></p>';
				$HTML .= '</div>';
			}

			// If I find content between the start and end of shortcode
			// I add it before the original embed code google

			if ($content != '') {
				$HTML .= '<div class="sz-google-button-cont">';
				$HTML .= $content;
				$HTML .= '</div>';
			}

			$HTML .= '</div>';

			// Adding the code to insert iframe original
			// google with alignment and positioning

			$HTML .= '<div class="sz-google-button-code">';
			$HTML .= '<div class="sz-google-button-side"';
			$HTML .= ' style="display:block;';

			if ($position == 'top')    $HTML .= 'position:absolute;width:100%;padding:0;top:1em;';
			if ($position == 'center') $HTML .= 'position:absolute;width:100%;padding:0;top:40%;';
			if ($position == 'bottom') $HTML .= 'position:absolute;width:100%;padding:0;bottom:1em;';

			if ($align    == 'left')   $HTML .= 'left:1em;text-align:left';
			if ($align    == 'center') $HTML .= 'left:0;text-align:center';
			if ($align    == 'right')  $HTML .= 'right:1em;text-align:right';

			$HTML .= '">';
			$HTML .= $html;
			$HTML .= '</div>';
			$HTML .= '</div>';

			$HTML .= '</div>';
			$HTML .= '</div>';
			$HTML .= '</div>';

			// Return to the function with the whole string containing
			// the HTML code for inserting the code in the page

			return $HTML;
		}
	}
}
