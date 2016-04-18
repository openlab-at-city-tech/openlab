<?php

/**
 * Class to initialize the plugin and recall
 * of all classes that make up the main parts
 *
 * @package SZGoogle
 * @subpackage Classes
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Before the definition of the class, check if there is a definition
// with the same name or the same as previously defined in other script

if (!class_exists('SZGooglePluginActivation'))
{
	class SZGooglePluginActivation
	{
		function action()
		{
			// Formal control options and storage on the database
			// according to a first install or update the plugin

			$this->checkOptions('sz_google_options_api'); 
			$this->checkOptions('sz_google_options_base'); 
			$this->checkOptions('sz_google_options_plus'); 
			$this->checkOptions('sz_google_options_ga');
			$this->checkOptions('sz_google_options_authenticator');
			$this->checkOptions('sz_google_options_calendar'); 
			$this->checkOptions('sz_google_options_drive'); 
			$this->checkOptions('sz_google_options_fonts'); 
			$this->checkOptions('sz_google_options_groups');
			$this->checkOptions('sz_google_options_hangouts');
			$this->checkOptions('sz_google_options_panoramio');
			$this->checkOptions('sz_google_options_recaptcha');
			$this->checkOptions('sz_google_options_translate');
			$this->checkOptions('sz_google_options_youtube');

			// Execution flush rules for rewriting customized
			// only if the plugin adds new options rewrite

			add_action('wp_loaded',array('SZGoogleCommon','rewriteFlushRules'));
		}

		/**
		 * Function for loading files containing options 
		 * linked to the various modules of the plugin
		 */

		private function checkOptions($nameset)
		{
			// Loading options file with array() containing the names of the
			// options that need to be stored in the database of wordpress

			$values = array();
			$fields = include(dirname(SZ_PLUGIN_GOOGLE_MAIN)."/options/{$nameset}.php");

			// Check if I have received an array from the file options
			// otherwise ignore the request of control options

			if (is_array($fields)) 
			{
				foreach($fields as $item=>$data) {
					$values[$item] = $data['value'];
				}

				$this->checkOptionSet($nameset,$values);
			}
		}

		/**
		 * Checking the option of single nameset to check if the indicated
		 * value must be added to the database in the activation phase.
		 */

		private function checkOptionSet($name,$values) 
		{
			if (is_array($values)) {

				// Check if there are the options required
				// and pitch control of each option

				if ($options = get_option($name)) 
				{
					if (!is_array($options)) $options=array(); 

					// Control in the options if there are indices that are
					// no longer used and take them out from the general array

					foreach ($options as $key=>$item) {
						if (!isset($values[$key])) unset($options[$key]);
					}

					// Control options that were included in the new
					// release and add to the container array general

					foreach ($values as $key=>$item) {
						if (!isset($options[$key])) $options[$key]=$item;
					}

					update_option($name,$options);

				} else {

					// If the options do not exist as the plugin may
					// be the first time it is installed -> add array 

					add_option($name,$values);
				}
			}
		}
	}
}