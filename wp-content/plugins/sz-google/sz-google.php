<?php
/*
Plugin Name: Google for WordPress
Plugin URI: https://otherplus.com/tech/wordpress-google/
Description: Plugin to integrate <a href="http://google.com" target="_blank">Google's</a> products in <a href="http://wordpress.org" target="_blank">WordPress</a> with particular attention to the widgets provided by the social network Google+. Before using the plug-in <em>google for wordpress</em> pay attention to the options to be specified in the admin panel and enter all the parameters necessary for the proper functioning of the plugin. If you want to know the latest news and releases from the plug-in <a href="http://wordpress.org/plugins/wordpress-google/">google for wordpress</a> follow the <a href="https://plus.google.com/+wpitalyplus" target="_blank">official page</a> present in Google+ or subscribe to our community <a href="https://plus.google.com/communities/109254048492234113886" target="_blank">WP Italyplus</a> always present on Google+.
Author: Massimo Della Rovere
Version: 1.9.4
Author URI: https://plus.google.com/+MassimoDellaRovere
License: GPLv2 or later
Copyright 2012-2014 otherplus (email: wordpress@otherplus.com)
Text Domain:sz-google
Domain Path:/admin/languages
*/

/**
 * This plugin was written with the support of our community WP Italyplus
 * on the social network google+, I take this opportunity to thank all the
 * people who have helped and supported the development of this plugin for
 * Wordpress, modules to be developed are still many, so any idea or advice
 * that may be of interest to future developments can be posted to community.
 * 
 * @Website..: https://otherplus.com/tech/
 * @Community: https://plus.google.com/communities/109254048492234113886
 *
 * Thanks to Eugenio Petullà for support and developer code.
 * Thanks to Patrizio Dell'Anna for plugin translate in english.
 * Thanks to AJ Clarke for article about tinymce tweaks.
 * Thanks to Henrik Schack for inspiration in authenticator found in repository.
 * Thanks to Michael Kliewe for PHP part of code found in @PHPGangsta.
 *
 */

if (!defined('ABSPATH')) die("Accesso diretto al file non permesso");

// Defining constants for use in plug-in for general use,
// here the constants must be defined by URL, basename, version, etc.

define('SZ_PLUGIN_GOOGLE',true);
define('SZ_PLUGIN_GOOGLE_MAIN',__FILE__);
define('SZ_PLUGIN_GOOGLE_VERSION','1.9.4');

// Definition of some basic functions to be used in the plugin 
// for calling special functions php which depend on the version

@require(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/functions/SZGoogleFunctions.php');

// Before the definition of the class, check if there is a definition 
// with the same name or the same as previously defined in other script.

if (!class_exists('SZGoogleCheck'))
{
	class SZGoogleCheck
	{
		private $PHP       = '5.2.0'; // Minimum requirement PHP
		private $WORDPRESS = '3.5.0'; // Minimum requirement WORDPRESS

		/**
		 * Definition the constructor function, which is called
		 * at the time of the creation of an instance of this class
		 */

		function __construct()
		{
			if ($this->is_compatible_version()) $this->load_plugin_framework();
				else if (is_admin()) add_action('admin_notices',array($this,'load_plugin_admin_notices'));

			register_activation_hook(__FILE__,array($this,'activate'));
		}

		/**
		 * Hook function for activation plugin to execute
		 * for version control and minimum prerequisites needed
		 */

		function activate()
		{
			if (!$this->is_compatible_version()) 
			{
				$HTML  = '<div>Activation plugin Google for WordPress in not possible:</div>';
				$HTML .= '<ul>';

				if (!$this->is_compatible_PHP())       $HTML .= '<li>'.$this->get_admin_notices_PHP(false).'</li>';
				if (!$this->is_compatible_WORDPRESS()) $HTML .= '<li>'.$this->get_admin_notices_WORDPRESS(false).'</li>';

				$HTML .= '</ul>';

				wp_die($HTML,'Activation (Google for WordPress) is not possible',array('back_link' => true));
			};
		}

		/**
		 * If the plugin is active, but the minimum requirements are not met
		 * the function is called to add the details on the notice board error
		 */

		function load_plugin_admin_notices() {
			if (!$this->is_compatible_PHP())       echo $this->get_admin_notices_PHP(true);
			if (!$this->is_compatible_WORDPRESS()) echo $this->get_admin_notices_WORDPRESS(true);
		}

		function get_admin_notices_PHP($wrap) {
			return $this->get_admin_notices_TEXT($wrap,'PHP',phpversion(),$this->PHP);
		}

		function get_admin_notices_WORDPRESS($wrap) {
			return $this->get_admin_notices_TEXT($wrap,'WORDPRESS',$GLOBALS['wp_version'],$this->WORDPRESS);
		}

		/**
		 * A function that creates a generic error to be displayed during 
		 * the activation function or on the bulletin board of directors.
		 */

		function get_admin_notices_TEXT($wrap,$s1,$s2,$s3) 
		{
			$HTML = 'Your server is running %s version %s but this plugin requires at least %s';

			if ($wrap === false) $HTML = "<div>{$HTML}</div>";
				else $HTML = "<div class=\"error\"><p>(<b>Google for Wordpress</b>) - {$HTML}</p></div>";

			return sprintf($HTML,$s1,$s2,$s3);
		}

		/**
		 * Checking compatibility with installed versions of the plugin
		 * In case of incompatibility still fully loaded plugin (return)
		 */

		function is_compatible_version() {
			if ($this->is_compatible_PHP() && $this->is_compatible_WORDPRESS()) return true;
				else return false;
		}

		/**
		 * Checking the compatibility of the plugin with the version of PHP
		 * In case of incompatibility still fully loaded plugin (return)
		 */

		function is_compatible_PHP() {
			if (version_compare(phpversion(),$this->PHP,'<')) return false;
				else return true;
		}

		/**
		 * Checking the compatibility of the plugin with the version of Wordpress
		 * In case of incompatibility still fully loaded plugin (return)
		 */

		function is_compatible_WORDPRESS() {
			if (version_compare($GLOBALS['wp_version'],$this->WORDPRESS,'<')) return false;
				else return true;
		}

		/**
		 * If the plugin is compatible according to the minimum requirements
		 * load files that contain the main framework of the plugin.
		 */

		function load_plugin_framework() 
		{
			// Enabling dynamic class loading without using
			// function require before the class definition

			spl_autoload_register(array($this,'auto_loader_classes'));

			// Creating object class that runs the full loading of 
			// the plugin with the modules enabled, filters etc, etc.

			new SZGooglePlugin();
		}

		/**
		 * Activation function autoloader for classes of the plugin, if the function is
		 * called for different classes by the system SZGoogle autoloading is ignored
		 */

		function auto_loader_classes($classname) 
		{
			if (substr($classname,0,8) != 'SZGoogle') return;

			// Loading classes that process the administration section
			// these classes begin with the prefix "SZGoogleAdmin"

			if (substr($classname,0,13) == 'SZGoogleAdmin') {
				if (is_readable(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/classes/'.$classname.'.php')) {
				       @require(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/classes/'.$classname.'.php'); return;
				}
			}

			// Loading the classes that belong to the plugin, looking for the prefix
			// after the name "SZGoogle" using it as part of the directory "classes"

			$prefix = preg_split('#([A-Z][^A-Z]*)#',$classname,null,PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

			if (is_readable(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/classes/'.strtolower($prefix[3]).'/'.$classname.'.php')) {
			       @require(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/classes/'.strtolower($prefix[3]).'/'.$classname.'.php'); return;
			}
		}

		/**
		 * Function for the shakes that are shown on the description of the plugin,
		 * in this way are loaded directly into the .pot file without manual entry.
		 */

		function dummy_plugin_description() 
		{
			$plugin      = __('Google for WordPress');
			$pluginURL   = __('https://otherplus.com/tech/wordpress-google/');
			$author      = __('Massimo Della Rovere');
			$authorURL   = __('https://plus.google.com/+MassimoDellaRovere');
			$description = __('Plugin to integrate <a href="http://google.com" target="_blank">Google\'s</a> products in <a href="http://wordpress.org" target="_blank">WordPress</a> with particular attention to the widgets provided by the social network Google+. Before using the plug-in <em>google for wordpress</em> pay attention to the options to be specified in the admin panel and enter all the parameters necessary for the proper functioning of the plugin. If you want to know the latest news and releases from the plug-in <a href="http://wordpress.org/plugins/wordpress-google/">google for wordpress</a> follow the <a href="https://plus.google.com/+wpitalyplus" target="_blank">official page</a> present in Google+ or subscribe to our community <a href="https://plus.google.com/communities/109254048492234113886" target="_blank">WP Italyplus</a> always present on Google+.');
		}
	}

	// Creating main object for the control of compatibility.
	// If the requirements are consistent run fully loaded.

	new SZGoogleCheck();
}