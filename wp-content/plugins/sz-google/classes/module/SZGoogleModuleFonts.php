<?php

/**
 * Module to the definition of the functions that relate to both the
 * widgets that shortcode, but also filters and actions that the module
 * can integrating with adding functionality into wordpress.
 *
 * @package SZGoogle
 * @subpackage Modules
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Before the definition of the class, check if there is a definition 
// with the same name or the same as previously defined in other script.

if (!class_exists('SZGoogleModuleFonts'))
{
	class SZGoogleModuleFonts extends SZGoogleModule
	{
		/**
		 * Definition of the initial variable array which are
		 * used to identify the module and options related to it
		 */

		function moduleAddSetup()
		{
			$this->moduleSetClassName(__CLASS__);
			$this->moduleSetOptionSet('sz_google_options_fonts');
		}
		
		/**
		 * Add the actions of the current module, this function must be
		 * implemented in the case of a non-standard customization via array
		 */

		function moduleAddActions()
		{ 
			$options = (object) $this->getOptions();

			// Checking if I have to activate a system to add the 
			// components of selection standard TinyMCE editor

			if ($options->fonts_tinyMCE_family == '1' or
			    $options->fonts_tinyMCE_size   == '1') 
			{
				new SZGoogleActionFontsTinyMCE();
			}

			// Checking if I have to activate the charging system for
			// fonts specified in the admin panel (active and with name)

			$testvalue = array('','nofonts');

			if (!in_array($options->fonts_family_L1_name,$testvalue) or
			    !in_array($options->fonts_family_L2_name,$testvalue) or
			    !in_array($options->fonts_family_L3_name,$testvalue) or
			    !in_array($options->fonts_family_L4_name,$testvalue) or
			    !in_array($options->fonts_family_L5_name,$testvalue) or
			    !in_array($options->fonts_family_L6_name,$testvalue) or
			    !in_array($options->fonts_family_B1_name,$testvalue) or
			    !in_array($options->fonts_family_P1_name,$testvalue) or
			    !in_array($options->fonts_family_B2_name,$testvalue) or
			    !in_array($options->fonts_family_H1_name,$testvalue) or
			    !in_array($options->fonts_family_H2_name,$testvalue) or
			    !in_array($options->fonts_family_H3_name,$testvalue) or
			    !in_array($options->fonts_family_H4_name,$testvalue) or
			    !in_array($options->fonts_family_H5_name,$testvalue) or
			    !in_array($options->fonts_family_H6_name,$testvalue))
			{
				add_action('SZ_HEAD_HEAD',array($this,'moduleAddFonts'),20);
			}

			// Check if you have specified a level that requires
			// the CSS automatic to be applied to the selected element

			if (!in_array($options->fonts_family_B1_name,$testvalue) or
			    !in_array($options->fonts_family_P1_name,$testvalue) or
			    !in_array($options->fonts_family_B2_name,$testvalue) or
			    !in_array($options->fonts_family_H1_name,$testvalue) or
			    !in_array($options->fonts_family_H2_name,$testvalue) or
			    !in_array($options->fonts_family_H3_name,$testvalue) or
			    !in_array($options->fonts_family_H4_name,$testvalue) or
			    !in_array($options->fonts_family_H5_name,$testvalue) or
			    !in_array($options->fonts_family_H6_name,$testvalue))
			{
				add_action('SZ_HEAD_FOOT',array($this,'moduleAddCSS'),20);
			}
		}

		/**
		 * Add information in <head> for loading fonts necessary
		 * in section <body> with method manual or automatic
		 */

		function moduleAddFonts()
		{
			$options = $this->getOptions();

			// Preparation work array and control levels available to
			// specify the font to be downloaded that has been specified

			$fontslist = array();
			$fontsload = array();
			$testvalue = array('','nofonts');

			if (!in_array($options['fonts_family_L1_name'],$testvalue)) $fontslist[] = $options['fonts_family_L1_name'];
			if (!in_array($options['fonts_family_L2_name'],$testvalue)) $fontslist[] = $options['fonts_family_L2_name'];
			if (!in_array($options['fonts_family_L3_name'],$testvalue)) $fontslist[] = $options['fonts_family_L3_name'];
			if (!in_array($options['fonts_family_L4_name'],$testvalue)) $fontslist[] = $options['fonts_family_L4_name'];
			if (!in_array($options['fonts_family_L5_name'],$testvalue)) $fontslist[] = $options['fonts_family_L5_name'];
			if (!in_array($options['fonts_family_L6_name'],$testvalue)) $fontslist[] = $options['fonts_family_L6_name'];
			if (!in_array($options['fonts_family_B1_name'],$testvalue)) $fontslist[] = $options['fonts_family_B1_name'];
			if (!in_array($options['fonts_family_P1_name'],$testvalue)) $fontslist[] = $options['fonts_family_P1_name'];
			if (!in_array($options['fonts_family_B2_name'],$testvalue)) $fontslist[] = $options['fonts_family_B2_name'];
			if (!in_array($options['fonts_family_H1_name'],$testvalue)) $fontslist[] = $options['fonts_family_H1_name'];
			if (!in_array($options['fonts_family_H2_name'],$testvalue)) $fontslist[] = $options['fonts_family_H2_name'];
			if (!in_array($options['fonts_family_H3_name'],$testvalue)) $fontslist[] = $options['fonts_family_H3_name'];
			if (!in_array($options['fonts_family_H4_name'],$testvalue)) $fontslist[] = $options['fonts_family_H4_name'];
			if (!in_array($options['fonts_family_H5_name'],$testvalue)) $fontslist[] = $options['fonts_family_H5_name'];
			if (!in_array($options['fonts_family_H6_name'],$testvalue)) $fontslist[] = $options['fonts_family_H6_name'];

			// I read all of the fonts listed and prepare an array without further 
			// duplication as it is possible to specify the same font on different levels

			foreach ($fontslist as $key=>$value) {
				if (!isset($fontsload[$value])) $fontsload[$value] = $value;
			}

			// If array of fonts to be loaded contains some element provider and the multiple
			// loading, but using only one statement stylesheet syntax like to google

			if (!empty($fontsload)) 
			{
				$fontstring = urlencode(implode('|',$fontsload));
				echo '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family='.$fontstring.'"/>'."\n";
			}
		}

		/**
		 * Add information in <head> for loading fonts necessary
		 * in section <body> with method manual or automatic
		 */

		function moduleAddCSS()
		{
			$options = $this->getOptions();
			$testvalue = array('','nofonts');

			echo "<style>";

			if (!in_array($options['fonts_family_B1_name'],$testvalue)) echo "body{font-family:'".$options['fonts_family_B1_name']."'}";
			if (!in_array($options['fonts_family_P1_name'],$testvalue)) echo "p{font-family:'".$options['fonts_family_P1_name']."'}";
			if (!in_array($options['fonts_family_B2_name'],$testvalue)) echo "blockquote { font-family:'".$options['fonts_family_B2_name']."'}";
			if (!in_array($options['fonts_family_H1_name'],$testvalue)) echo "h1{font-family:'".$options['fonts_family_H1_name']."'}";
			if (!in_array($options['fonts_family_H2_name'],$testvalue)) echo "h2{font-family:'".$options['fonts_family_H2_name']."'}";
			if (!in_array($options['fonts_family_H3_name'],$testvalue)) echo "h3{font-family:'".$options['fonts_family_H3_name']."'}";
			if (!in_array($options['fonts_family_H4_name'],$testvalue)) echo "h4{font-family:'".$options['fonts_family_H4_name']."'}";
			if (!in_array($options['fonts_family_H5_name'],$testvalue)) echo "h5{font-family:'".$options['fonts_family_H5_name']."'}";
			if (!in_array($options['fonts_family_H6_name'],$testvalue)) echo "h6{font-family:'".$options['fonts_family_H6_name']."'}";

			echo "</style>\n";
		}
	}

	/**
	 * Loading function for PHP allows developers to implement modules in this plugin.
	 * The functions have the same parameters of shortcodes, see the documentation.
	 */

	@require_once(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/functions/SZGoogleFunctionsFonts.php');
}