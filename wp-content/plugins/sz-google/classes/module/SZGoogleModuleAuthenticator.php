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

if (!class_exists('SZGoogleModuleAuthenticator'))
{
	class SZGoogleModuleAuthenticator extends SZGoogleModule
	{
		/**
		 * Definition of the initial variable array which are
		 * used to identify the module and options related to it
		 */

		function moduleAddSetup()
		{
			$this->moduleSetClassName(__CLASS__);
			$this->moduleSetOptionSet('sz_google_options_authenticator');
		}

		/**
		 * Add the actions of the current module, this function must be
		 * implemented in the case of a non-standard customization via array
		 */

		function moduleAddActions()
		{
			$options = (object) $this->getOptions();

			// Check whether login authentication option is active.
			// In this case, add filters and actions on login phase.

			if ($options->authenticator_login_enable) {
				if (!$this->checkEmergencyFile()) new SZGoogleActionAuthenticatorLogin();
					else if (is_admin()) add_action('admin_notices',array($this,'addAdminNotices'));
			}

			// Add to profile options that are only seen if user=current, are to be
			// included in the options button to create a configuration synchronization

			new SZGoogleActionAuthenticatorProfile();
		}

		/**
		 * Function to indicate the message suspension module
		 * on the bulletin board in the main admin panel.
		 */

		function addAdminNotices() 
		{
			echo '<div class="error"><p>(<b>sz-google</b>) - ';
			echo __('Google Authenticator is suspended because it was found the file of emergency in the root directory.','sz-google');
			echo '</p></div>';
		}

		/**
		 * Function for the control of the file emergency, If
		 * such file is found, the process is temporarily suspended.
		 */

		function checkEmergencyFile() 
		{
			$options = (object) $this->getOptions();

			// If an emergency option is not active I leave the function
			// otherwise check file exists in the root directory

			if (!isset($options->authenticator_emergency) or $options->authenticator_emergency != '1') {
				return false;
			} 

			// Calculate the name of the file to be checked by taking the default 
			// or the value specified in the general configuration of google authenticator

			if (trim($options->authenticator_emergency_file) == '') $filename = ABSPATH.'google-authenticator-disable.php';
				else $filename = ABSPATH.trim($options->authenticator_emergency_file);

			// Checking file exists google-authenticator-emergency.php
			// If the file does not exist off the authentication feature

			if (file_exists($filename)) return true;
				else return false;
		}
	}

	/**
	 * Loading function for PHP allows developers to implement modules in this plugin.
	 * The functions have the same parameters of shortcodes, see the documentation.
	 */

	@require_once(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/functions/SZGoogleFunctionsAuthenticator.php');
}