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

if (!class_exists('SZGoogleAdminRecaptcha'))
{
	class SZGoogleAdminRecaptcha extends SZGoogleAdmin
	{
		/**
		 * Creating the menu on the admin panel using values ​​
		 * such as configuration variables object (parent function)
		 */

		function moduleAddMenu()
		{
			// Definition of general values ​​for the creation of a menu associated 
			// with the module options. Example slug, page title and menu title

			$this->menuslug   = 'sz-google-admin-recaptcha.php';
			$this->pagetitle  = ucfirst(__('google reCAPTCHA','sz-google'));
			$this->menutitle  = ucfirst(__('google reCAPTCHA','sz-google'));

			// Definition of sections that need to be made ​​in HTML
			// sections must be passed as an array of name = > title

			$this->sectionstabs = array(
				'01' => array('anchor' => 'general','description' => __('general'   ,'sz-google')),
				'02' => array('anchor' => 'style'  ,'description' => __('FORM Style','sz-google')),
			);

			$this->sections = array(
				array('tab' => '01','section' => 'sz-google-admin-recaptcha.php'            ,'title' => ucwords(__('settings'      ,'sz-google'))),
				array('tab' => '01','section' => 'sz-google-admin-recaptcha-enable.php'     ,'title' => ucwords(__('activations'   ,'sz-google'))),
				array('tab' => '01','section' => 'sz-google-admin-recaptcha-emergency.php'  ,'title' => ucwords(__('emergency file','sz-google'))),
				array('tab' => '02','section' => 'sz-google-admin-recaptcha-style.php'      ,'title' => ucwords(__('style'         ,'sz-google'))),
				array('tab' => '02','section' => 'sz-google-admin-recaptcha-corrections.php','title' => ucwords(__('corrections'   ,'sz-google'))),
			);

			$this->sectionstitle   = $this->menutitle;
			$this->sectionsoptions = array('sz_google_options_recaptcha');

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
				'01' => array('section' => 'sz_google_recaptcha'            ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-recaptcha.php'),
				'02' => array('section' => 'sz_google_recaptcha_enabled'    ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-recaptcha-enable.php'),
				'03' => array('section' => 'sz_google_recaptcha_emergency'  ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-recaptcha-emergency.php'),
				'04' => array('section' => 'sz_google_recaptcha_style'      ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-recaptcha-style.php'),
				'05' => array('section' => 'sz_google_recaptcha_corrections','title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-recaptcha-corrections.php'),
			);

			// General definition array containing a list of fields
			// All fields are added to the previously defined sections

			$this->sectionsfields = array(
				'01' => array(array('field' => 'recaptcha_key_site'         ,'title' => ucfirst(__('KEY Site'         ,'sz-google')),'callback' => array($this,'callback_recaptcha_key_site')),
				              array('field' => 'recaptcha_key_secret'       ,'title' => ucfirst(__('KEY Secret'       ,'sz-google')),'callback' => array($this,'callback_recaptcha_key_secret')),),
				'02' => array(array('field' => 'recaptcha_enable_login'     ,'title' => ucfirst(__('enable login'     ,'sz-google')),'callback' => array($this,'callback_recaptcha_enable_login')),),
				'03' => array(array('field' => 'recaptcha_emergency'        ,'title' => ucfirst(__('emergency'        ,'sz-google')),'callback' => array($this,'callback_recaptcha_emergency_enable')),
				              array('field' => 'recaptcha_emergency_file'   ,'title' => ucfirst(__('emergency file'   ,'sz-google')),'callback' => array($this,'callback_recaptcha_emergency_file')),),
				'04' => array(array('field' => 'recaptcha_style_login'      ,'title' => ucfirst(__('style login'      ,'sz-google')),'callback' => array($this,'callback_recaptcha_style_login')),),
				'05' => array(array('field' => 'recaptcha_style_login_CSS'  ,'title' => ucfirst(__('style login CSS'  ,'sz-google')),'callback' => array($this,'callback_recaptcha_style_login_css')),
				              array('field' => 'recaptcha_style_login_width','title' => ucfirst(__('style login width','sz-google')),'callback' => array($this,'callback_recaptcha_style_login_width')),),
			);

			// Calling up the function of the parent class to process the 
			// variables that contain the values ​​of configuration section

			parent::moduleAddFields();
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)
		 */

		function callback_recaptcha_key_site() 
		{ 
			$this->moduleCommonFormText('sz_google_options_recaptcha','recaptcha_key_site','large');
			$this->moduleCommonFormDescription(__('Before using this form you have to ask for the keys on the official website of google. Go (<a target="_blank" href="https://www.google.com/recaptcha/admin#list">Get reCAPTCHA</a>) and enter keys that are assigned in these areas of the plugin. Keyless module does not work.','sz-google'));
		}

		function callback_recaptcha_key_secret() 
		{ 
			$this->moduleCommonFormText('sz_google_options_recaptcha','recaptcha_key_secret','large');
			$this->moduleCommonFormDescription(__('Before using this form you have to ask for the keys on the official website of google. Go (<a target="_blank" href="https://www.google.com/recaptcha/admin#list">Get reCAPTCHA</a>) and enter keys that are assigned in these areas of the plugin. Keyless module does not work.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)
		 */

		function callback_recaptcha_enable_login() 
		{ 
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_recaptcha','recaptcha_enable_login');
			$this->moduleCommonFormDescription(__('enable this option to integrate the control of google reCAPTCHA in login panel. Deactivation is used to implement login with PHP functions in the case has been heavily customized. See online documentation for more details.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)
		 */

		function callback_recaptcha_emergency_enable() 
		{ 
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_recaptcha','recaptcha_emergency');
			$this->moduleCommonFormDescription(__('enable this option to manage the files of an emergency. With this option enabled you can disable the authenticator will be sending in FTP a file in the root directory of wordpress. The file name is specified in the next field.','sz-google'));
		}

		function callback_recaptcha_emergency_file() 
		{ 
			$this->moduleCommonFormText('sz_google_options_recaptcha','recaptcha_emergency_file','large',__('google-recaptcha-disable.php','sz-google'));
			$this->moduleCommonFormDescription(__('indicates the name of the file to be used for the emergency function. If the file specified in this field is found on the root of the wordpress function authenticator is temporarily suspended. Default name is <b>google-recaptcha-disable.php<b/>.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)
		 */

		function callback_recaptcha_style_login() 
		{ 
			$values = array('light'=>__('theme light','sz-google'),'dark'=>__('theme dark','sz-google'));
			$this->moduleCommonFormSelect('sz_google_options_recaptcha','recaptcha_style_login',$values,'medium','');
			$this->moduleCommonFormDescription(__('indicate the style to apply to the plugin when you see the widget reCAPTHA on the login form. At the moment the values provided by google are light and dark. Specify a value consistent with its theme.','sz-google'));
		}

		function callback_recaptcha_style_login_css() 
		{ 
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_recaptcha','recaptcha_style_login_CSS');
			$this->moduleCommonFormDescription(__('the standard login wordpress is too small to hold the widget reCAPTCHA google, if you activate this option the plugin will increase the size of the standard login to create a better visual result. Minimum size is 350 pixel.','sz-google'));
		}

		function callback_recaptcha_style_login_width() 
		{ 
			$this->moduleCommonFormNumberStep1('sz_google_options_recaptcha','recaptcha_style_login_width','medium','auto');
			$this->moduleCommonFormDescription(__('if you have activated the previous option, you can specify the width of the widget custom login. If nothing is specified it will be used the value of 350 pixels. Change it if you have installed a custom login.','sz-google'));
		}
	}
}