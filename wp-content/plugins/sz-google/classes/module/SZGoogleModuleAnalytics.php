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

if (!class_exists('SZGoogleModuleAnalytics'))
{
	class SZGoogleModuleAnalytics extends SZGoogleModule
	{
		/**
		 * Definition of the initial variable array which are
		 * used to identify the module and options related to it
		 */

		function moduleAddSetup()
		{
			$this->moduleSetClassName(__CLASS__);
			$this->moduleSetOptionSet('sz_google_options_ga');
		}

		/**
		 * Add the actions of the current module, this function must be
		 * implemented in the case of a non-standard customization via array
		 */

		function moduleAddActions()
		{
			$options = (object) $this->getOptions();

			// If you are on the frontend add action header or footer 
			// based on what was specified in the configuration

			if (!is_admin() and $options->ga_enable_front == '1') {
				if ($options->ga_position == 'H') add_action('wp_head',array(new SZGoogleActionAnalytics($this),'action'));
				if ($options->ga_position == 'F') add_action('SZ_FOOT_BODY',array(new SZGoogleActionAnalytics($this),'action'));
			}
		}

		/**
		 * Function to calculate the Google Analytics code
		 * to be used in the tracking code entered manually
		 */

		function getGAId($atts=array()) {
			$options = $this->getOptions();
			return trim($options['ga_uacode']);
		}
	}

	/**
	 * Loading function for PHP allows developers to implement modules in this plugin.
	 * The functions have the same parameters of shortcodes, see the documentation.
	 */

	@require_once(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/functions/SZGoogleFunctionsAnalytics.php');
}