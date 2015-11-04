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
// with the same name or the same as previously defined in other script.

if (!class_exists('SZGooglePlugin'))
{
	class SZGooglePlugin
	{
		private $module  = false;
		private $options = false;

		private $addCommentHeader = false;
		private $addCommentFooter = false;

		/**
		 * Definition the constructor function, which is called
		 * at the time of the creation of an instance of this class
		 */

		function __construct() 
		{
			$this->module = new SZGoogleModule();

			// Creating object for basic module with domain
			// setup translation and storage configured options

			add_action('plugins_loaded',array($this,'includeLanguageDomain'));

			// Calling functions for loading of the main parts of the
			// plugin with the definition of the modules that are active

			$this->includeHook();		// (1) Registration hook for enable and disable
			$this->includeHead();		// (2) Registration functions for execution HEAD
			$this->includeFooter();		// (3) Registration functions for execution FOOTER
			$this->includeModules();	// (4) Registration functions for execution modules
		}

		/**
		 * Defining hooks for the registration of the shares related
		 * to the operations of activation and deactivation of the plugin
		 */

		function includeHook() 
		{
			register_activation_hook  (SZ_PLUGIN_GOOGLE_MAIN,array(new SZGooglePluginActivation,'action'));
			register_deactivation_hook(SZ_PLUGIN_GOOGLE_MAIN,array(new SZGooglePluginDeactivation,'action'));
		}

		/**
		 * Execution function to add values ​​to the section <head> of the
		 * web page connected with WP_HEAD means action(szgoogle-head)
		 */

		function includeHead() {
			add_action('wp_head',array($this,'addSectionHeaderOpen'),1);
			add_action('wp_head',array($this,'addSectionHeaderHead'),1);
			add_action('wp_head',array($this,'addSectionHeaderEnds'),1);
			add_action('wp_head',array($this,'addSectionHeaderFoot'),99);
		}

		/**
		 * Execution function to add values ​​to the section <footer> of the
		 * web page connected with WP_FOOTER means action(szgoogle-footer)
		 */

		function includeFooter() {
			add_action('wp_footer',array($this,'addSectionFooterOpen'));
			add_action('wp_footer',array($this,'addSectionFooterHead'));
			add_action('wp_footer',array($this,'addSectionFooterBase'));
			add_action('wp_footer',array($this,'addSectionFooterBody'));
			add_action('wp_footer',array($this,'addSectionFooterEnds'));
		}

		/**
		 * Module loading the plugins that are active and enabling
		 * functions on the administrative part only if required
		 */

		function includeModules() 
		{
			$options = (object) $this->getOptions();

			if ($options->plus          == '1') new SZGoogleModulePlus();
			if ($options->analytics     == '1') new SZGoogleModuleAnalytics();
			if ($options->authenticator == '1') new SZGoogleModuleAuthenticator();
			if ($options->calendar      == '1') new SZGoogleModuleCalendar();
			if ($options->drive         == '1') new SZGoogleModuleDrive();
			if ($options->fonts         == '1') new SZGoogleModuleFonts();
			if ($options->groups        == '1') new SZGoogleModuleGroups();
			if ($options->hangouts      == '1') new SZGoogleModuleHangouts();
			if ($options->maps          == '1') new SZGoogleModuleMaps();
			if ($options->panoramio     == '1') new SZGoogleModulePanoramio();
			if ($options->recaptcha     == '1') new SZGoogleModuleRecaptcha();
			if ($options->translate     == '1') new SZGoogleModuleTranslate();
			if ($options->youtube       == '1') new SZGoogleModuleYoutube();
		
			// Calling scripts for integration with the plugin admin panel, adds a
			// menu dedicated to the plugin with all the options related to the modules

			if (is_admin()) new SZGoogleAdminBase();

			// Control is performed if an AJAX call to activate the
			// functions connected to the code corresponding action

			if (defined('DOING_AJAX') && DOING_AJAX) {
				new SZGoogleModuleAjax();
			}
		}

		/**
		 * Creating object for basic module with domain
		 * setup translation and storage configured options
		 */

		function includeLanguageDomain()
		{
			$dirAdmin = dirname(plugin_basename(SZ_PLUGIN_GOOGLE_MAIN)).'/admin/languages';
			$dirFront = dirname(plugin_basename(SZ_PLUGIN_GOOGLE_MAIN)).'/frontend/languages';

			if (is_admin()) load_plugin_textdomain('sz-google',false,$dirAdmin);
			                load_plugin_textdomain('szgooglefront',false,$dirFront);
		}

		/**
		 * Functions for the creation of "action" and processing HTML to
		 * be included in the various sections of the page WEB Head & Footer
		 */

		function addSectionHeaderOpen() { $this->addCodeHeaderHTML('SZ_COMM_OPEN'); }
		function addSectionHeaderHead() { $this->addCodeHeaderBody('SZ_HEAD_HEAD'); }
		function addSectionHeaderEnds() { $this->addCodeHeaderHTML('SZ_COMM_ENDS'); }
		function addSectionHeaderFoot() { $this->addCodeHeaderBody('SZ_HEAD_FOOT'); }

		function addSectionFooterOpen() { $this->addCodeFooterHTML('SZ_COMM_OPEN'); }
		function addSectionFooterHead() { $this->addCodeFooterBody('SZ_FOOT_HEAD'); }
		function addSectionFooterBase() { $this->addCodeFooterBody('SZ_FOOT_BASE'); }
		function addSectionFooterBody() { $this->addCodeFooterBody('SZ_FOOT_BODY'); }
		function addSectionFooterEnds() { $this->addCodeFooterHTML('SZ_COMM_ENDS'); }

		/**
		 * Functions for the creation of "action" and processing HTML to
		 * be included in the various sections of the page WEB Head & Footer
		 */

		function addCodeHeaderBody($action) { if(has_action($action)) do_action($action); }
		function addCodeFooterBody($action) { if(has_action($action)) do_action($action); }

		/**
		 * Functions for the creation of "action" and processing HTML to
		 * be included in the various sections of the page WEB Head & Footer
		 */

		function addCodeHeaderHTML($section) {
			if (has_action('SZ_FOOT_HEAD') or has_action('SZ_FOOT_BASE') or has_action('SZ_FOOT_BODY')) {
				if ($section == 'SZ_COMM_OPEN') echo $this->getCodeCommentOpen();
				if ($section == 'SZ_COMM_ENDS') echo $this->getCodeCommentEnds();
			}
		}

		function addCodeFooterHTML($section) {
			if (has_action('SZ_HEAD_HEAD') or has_action('SZ_HEAD_FOOT')) {
				if ($section == 'SZ_COMM_OPEN') echo $this->getCodeCommentOpen();
				if ($section == 'SZ_COMM_ENDS') echo $this->getCodeCommentEnds();
			}
		}

		/**
		 * Function for the creation of HTML comments to
		 * be inserted before and after the javascript code
		 */

		function getCodeCommentOpen() {
			return "\n<!-- This section is created with the SZ-Google for WordPress plugin ".SZ_PLUGIN_GOOGLE_VERSION." -->\n"
			        ."<!-- ===================================================================== -->\n";
		}

		function getCodeCommentEnds() {
			return "<!-- ===================================================================== -->\n";
		}

		/**
		 * Calculation options related to the module with execution
		 * of formal checks of consistency and setting the default
		 */

		function getOptions()
		{
			if ($this->options) return $this->options;
				else $this->options = $this->module->getOptionsSet('sz_google_options_base');

			return $this->options;
		}
	}
}