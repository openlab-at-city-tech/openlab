<?php

/**
 * Module to the definition of the functions that relate to both the
 * widgets that shortcode, but also filters and actions that the module
 * can integrating with adding functionality into wordpress.
 *
 * @package SZGoogle
 * @subpackage Admin
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Before the definition of the class, check if there is a definition 
// with the same name or the same as previously defined in other script.

if (!class_exists('SZGoogleAdminHangouts'))
{
	class SZGoogleAdminHangouts extends SZGoogleAdmin
	{
		/**
		 * Creating the menu on the admin panel using values ​​
		 * such as configuration variables object (parent function)
		 */

		function moduleAddMenu()
		{
			// Definition of general values ​​for the creation of a menu associated 
			// with the module options. Example slug, page title and menu title

			$this->menuslug   = 'sz-google-admin-hangouts.php';
			$this->pagetitle  = ucwords(__('google hangouts','sz-google'));
			$this->menutitle  = ucwords(__('google hangouts','sz-google'));

			// Definition of sections that need to be made ​​in HTML
			// sections must be passed as an array of name = > title

			$this->sectionstabs = array(
				'01' => array('anchor' => 'general','description' => __('general','sz-google')),
			);

			$this->sections = array(
				array('tab' => '01','section' => 'sz-google-admin-hangouts-start.php','title' => ucwords(__('start hangout','sz-google'))),
			);

			$this->sectionstitle   = $this->menutitle;
			$this->sectionsoptions = array('sz_google_options_hangouts');

			// Calling up the function of the parent class to process the 
			// variables that contain the values ​​of configuration section

			parent::moduleAddMenu();
 		}

		/**
		 * Function to add sections and the corresponding options in the configuration
		 * page, each option belongs to a section, which is linked to a general tab 
		 */

		function moduleAddFields()
		{
			// General definition array containing a list of sections
			// On every section you have to define an array to list fields

			$this->sectionsmenu = array(
				'01' => array('section' => 'sz_google_hangouts_start','title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-hangouts-start.php'),
			);

			// General definition array containing a list of fields
			// All fields are added to the previously defined sections

			$this->sectionsfields = array(
				'01' => array(array('field' => 'hangouts_start_shortcode','title' => ucfirst(__('shortcode'  ,'sz-google')),'callback' => array($this,'callback_hangouts_start_shortcode')),
				              array('field' => 'hangouts_start_widget'   ,'title' => ucfirst(__('widget'     ,'sz-google')),'callback' => array($this,'callback_hangouts_start_widget')),
				              array('field' => 'hangouts_start_logged'   ,'title' => ucfirst(__('user logged','sz-google')),'callback' => array($this,'callback_hangouts_start_logged')),
				              array('field' => 'hangouts_start_guest'    ,'title' => ucfirst(__('user guest' ,'sz-google')),'callback' => array($this,'callback_hangouts_start_guest')),
				),
			);

			// Calling up the function of the parent class to process the 
			// variables that contain the values ​​of configuration section

			parent::moduleAddFields();
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_hangouts_start_widget()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_hangouts','hangouts_start_widget');
			$this->moduleCommonFormDescription(__('if you enable this option you will find the widget required in the administration menu of your widget and you can plug it into any sidebar defined in your theme. If you disable this option, remember not to leave the widget connected to existing sidebar.','sz-google'));
		}

		function callback_hangouts_start_shortcode()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_hangouts','hangouts_start_shortcode');
			$this->moduleCommonFormDescription(__('if you enable this option you can use this shortcode and enter the corresponding components directly in your article or page. Normally in the shortcodes can be specified the options for customizations. See the documentation section.','sz-google'));
		}

		function callback_hangouts_start_logged()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_hangouts','hangouts_start_logged');
			$this->moduleCommonFormDescription(__('this option controls whether the selected component to be displayed when a user is logged. If you uncheck the option for the user logged and for the guest user, only the site administrator can see this component present in a web page.','sz-google'));
		}

		function callback_hangouts_start_guest()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_hangouts','hangouts_start_guest');
			$this->moduleCommonFormDescription(__('this option controls whether the selected component to be displayed when a guest user is connected. If you uncheck the option for the user logged and for the guest user, only the site administrator can see this component present in a web page.','sz-google'));
		}
	}
}