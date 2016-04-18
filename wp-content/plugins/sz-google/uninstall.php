<?php

/**
 * File called at uninstall plugins: In this step you have to perform the cleaning 
 * of the options stored in the database and see if wordpress multisite environment.
 *
 * @package SZGoogle
 * @subpackage Plugin
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if(!defined('WP_UNINSTALL_PLUGIN')) die();

// Before the definition of the class, check if there is a definition 
// with the same name or the same as previously defined in other script.

if (!class_exists('SZGoogleUninstall'))
{
	class SZGoogleUninstall
	{
		/**
		 * Definition the constructor function, which is called
		 * at the time of the creation of an instance of this class
		 */

		function __construct()
		{
			if (is_multisite()) $this->uninstall_delete_options_multisite();  
				else $this->uninstall_delete_options_single();
		}

		/**
		 * Cancellation of the configuration options for 
		 * individual blogs during the uninstallation
		 */

		function uninstall_delete_options_single()
		{
			delete_option('sz_google_options_api');           // Google API
			delete_option('sz_google_options_authenticator'); // Google Authenticator
			delete_option('sz_google_options_base');          // Google Setup
			delete_option('sz_google_options_calendar');      // Google Calendar
			delete_option('sz_google_options_drive');         // Google Drive
			delete_option('sz_google_options_fonts');         // Google Fonts
			delete_option('sz_google_options_ga');            // Google Analytics
			delete_option('sz_google_options_groups');        // Google Groups
			delete_option('sz_google_options_hangouts');      // Google Hangouts
			delete_option('sz_google_options_maps');          // Google Maps
			delete_option('sz_google_options_panoramio');     // Google Panoramio
			delete_option('sz_google_options_plus');          // Google Plus
			delete_option('sz_google_options_recaptcha');     // Google reCAPTCHA
			delete_option('sz_google_options_translate');     // Google Translate
			delete_option('sz_google_options_youtube');       // Google Youtube
		}

		/**
		 * Cancellation of the configuration options for 
		 * the whole network during the uninstallation
		 */

		function uninstall_delete_options_multisite()
		{
			global $wpdb;
			$blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);

			// Loop main network with all blogs configured, 
			// for each run of the cancellation options.

			if ($blogs) {
				foreach($blogs as $blog) {
					switch_to_blog($blog['blog_id']);
					$this->uninstall_delete_options_single();
				}
			}

			// Restoring the blog and after reading the main loop 
			// of the blogs belonging to the complete network

			restore_current_blog();
		}
	}

	// Creating object to perform the uninstall feature of
	// the plugin with the cleaning of the options related

	new SZGoogleUninstall();
}