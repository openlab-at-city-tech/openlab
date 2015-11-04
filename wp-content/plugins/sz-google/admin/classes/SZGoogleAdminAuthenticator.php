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

if (!class_exists('SZGoogleAdminAuthenticator'))
{
	class SZGoogleAdminAuthenticator extends SZGoogleAdmin
	{
		/**
		 * Creating the menu on the admin panel using values ​​
		 * such as configuration variables object (parent function)
		 */

		function moduleAddMenu()
		{
			// Definition of general values ​​for the creation of a menu associated 
			// with the module options. Example slug, page title and menu title

			$this->menuslug   = 'sz-google-admin-authenticator.php';
			$this->pagetitle  = ucwords(__('google authenticator','sz-google'));
			$this->menutitle  = ucwords(__('google authenticator','sz-google'));

			// Definition of sections that need to be made ​​in HTML
			// sections must be passed as an array of name = > title

			$this->sectionstabs = array(
				'01' => array('anchor' => 'general' ,'description' => __('general','sz-google')),
			);

			$this->sections = array(
				array('tab' => '01','section' => 'sz-google-admin-authenticator.php'          ,'title' => ucwords(__('settings','sz-google'))),
				array('tab' => '01','section' => 'sz-google-admin-authenticator-emergency.php','title' => ucwords(__('emergency file','sz-google'))),
			);

			$this->sectionstitle   = $this->menutitle;
			$this->sectionsoptions = array('sz_google_options_authenticator');

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
				'01' => array('section' => 'sz_google_authenticator_enabled'  ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-authenticator.php'),
				'02' => array('section' => 'sz_google_authenticator_emergency','title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-authenticator-emergency.php'),
			);

			// General definition array containing a list of fields
			// All fields are added to the previously defined sections

			$this->sectionsfields = array(
				'01' => array(array('field' => 'authenticator_login_enable'   ,'title' => ucfirst(__('enable'          ,'sz-google')),'callback' => array($this,'callback_authenticator_login_enable')),
				              array('field' => 'authenticator_discrepancy'    ,'title' => ucfirst(__('discrepancy'     ,'sz-google')),'callback' => array($this,'callback_authenticator_discrepancy')),),
				'02' => array(array('field' => 'authenticator_emergency_codes','title' => ucfirst(__('emergency codes' ,'sz-google')),'callback' => array($this,'callback_authenticator_emergency_codes')),
				              array('field' => 'authenticator_emergency'      ,'title' => ucfirst(__('emergency enable','sz-google')),'callback' => array($this,'callback_authenticator_emergency_enable')),
				              array('field' => 'authenticator_emergency_file' ,'title' => ucfirst(__('emergency file'  ,'sz-google')),'callback' => array($this,'callback_authenticator_emergency_file')),),
			);

			// Calling up the function of the parent class to process the 
			// variables that contain the values ​​of configuration section

			parent::moduleAddFields();
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_authenticator_login_enable() 
		{ 
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_authenticator','authenticator_login_enable');
			$this->moduleCommonFormDescription(__('enable this option to integrate the control of google authenticator in login panel. Deactivation is used to implement login with PHP functions in the case has been heavily customized. See online documentation for more details.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_authenticator_discrepancy() 
		{ 
			$values = array(
				'1'  => __('30 seconds','sz-google'),
				'2'  => __('1 Minutes' ,'sz-google'),
				'4'  => __('2 Minutes' ,'sz-google'),
				'6'  => __('3 Minutes' ,'sz-google'),
				'8'  => __('4 Minutes' ,'sz-google'),
				'10' => __('5 Minutes' ,'sz-google')
			); 

			$this->moduleCommonFormSelect('sz_google_options_authenticator','authenticator_discrepancy',$values,'medium','');
			$this->moduleCommonFormDescription(__('indicate time of discrepancy that should be used by the plugin. This value indicates the time of tolerance that is applied to the generation of the authenticator code with respect to time auto-generation. Default value is 30 seconds.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_authenticator_emergency_codes() 
		{ 
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_authenticator','authenticator_emergency_codes');
			$this->moduleCommonFormDescription(__('Enable this option to manage the emergency codes. Are of backup codes that can be used in case of emergency, for example, when our smartphones is inoperable or have problems on-time password. Each code can be used only once.','sz-google'));		}

		function callback_authenticator_emergency_enable() 
		{ 
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_authenticator','authenticator_emergency');
			$this->moduleCommonFormDescription(__('Enable this option to manage the files of an emergency. With this option enabled you can disable the authenticator will be sending in FTP a file in the root directory of wordpress. The file name is specified in the next field.','sz-google'));
		}

		function callback_authenticator_emergency_file() 
		{ 
			$this->moduleCommonFormText('sz_google_options_authenticator','authenticator_emergency_file','large',__('google-authenticator-disable.php','sz-google'));
			$this->moduleCommonFormDescription(__('Indicates the name of the file to be used for the emergency function. If the file specified in this field is found on the root of the wordpress function authenticator is temporarily suspended. Default name is <b>google-authenticator-disable.php<b/>.','sz-google'));
		}
	}
}