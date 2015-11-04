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

if (!class_exists('SZGoogleAdminTranslate'))
{
	class SZGoogleAdminTranslate extends SZGoogleAdmin
	{
		/**
		 * Creating the menu on the admin panel using values ​​
		 * such as configuration variables object (parent function)
		 */

		function moduleAddMenu()
		{
			// Definition of general values ​​for the creation of a menu associated 
			// with the module options. Example slug, page title and menu title

			$this->menuslug   = 'sz-google-admin-translate.php';
			$this->pagetitle  = ucwords(__('google translate','sz-google'));
			$this->menutitle  = ucwords(__('google translate','sz-google'));

			// Definition of sections that need to be made ​​in HTML
			// sections must be passed as an array of name = > title

			$this->sectionstabs = array(
				'01' => array('anchor' => 'general' ,'description' => __('general' ,'sz-google')),
				'02' => array('anchor' => 'advanced','description' => __('advanced','sz-google')),
			);

			$this->sections = array(
				array('tab' => '01','section' => 'sz-google-admin-translate.php'         ,'title' => ucwords(__('settings'         ,'sz-google'))),
				array('tab' => '01','section' => 'sz-google-admin-translate-language.php','title' => ucwords(__('language setting' ,'sz-google'))),
				array('tab' => '01','section' => 'sz-google-admin-translate-enable.php'  ,'title' => ucwords(__('activation'       ,'sz-google'))),
				array('tab' => '02','section' => 'sz-google-admin-translate-advanced.php','title' => ucwords(__('advanced settings','sz-google'))),
			);

			$this->sectionstitle   = $this->menutitle;
			$this->sectionsoptions = array('sz_google_options_translate');

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
				'01' => array('section' => 'sz_google_translate_section' ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-translate.php'),
				'02' => array('section' => 'sz_google_translate_language','title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-translate-language.php'),
				'03' => array('section' => 'sz_google_translate_active'  ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-translate-enable.php'),
				'04' => array('section' => 'sz_google_translate_advanced','title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-translate-advanced.php'),
			);

			// General definition array containing a list of fields
			// All fields are added to the previously defined sections

			$this->sectionsfields = array(
				'01' => array(array('field' => 'translate_meta'        ,'title' => ucfirst(__('code META'          ,'sz-google')),'callback' => array($this,'callback_translate_meta')),
				              array('field' => 'translate_mode'        ,'title' => ucfirst(__('display mode'       ,'sz-google')),'callback' => array($this,'callback_translate_mode')),),
				'02' => array(array('field' => 'translate_language'    ,'title' => ucfirst(__('website language'   ,'sz-google')),'callback' => array($this,'callback_translate_language')),),
				'03' => array(array('field' => 'translate_shortcode'   ,'title' => ucfirst(__('shortcode'          ,'sz-google')),'callback' => array($this,'callback_translate_shortcode')),
				              array('field' => 'translate_widget'      ,'title' => ucfirst(__('widget'             ,'sz-google')),'callback' => array($this,'callback_translate_widget')),),
				'04' => array(array('field' => 'translate_automatic'   ,'title' => ucfirst(__('automatic banner'   ,'sz-google')),'callback' => array($this,'callback_translate_automatic')),
				              array('field' => 'translate_multiple'    ,'title' => ucfirst(__('multiple language'  ,'sz-google')),'callback' => array($this,'callback_translate_multiple')),
				              array('field' => 'translate_analytics'   ,'title' => ucwords(__('google analytics'   ,'sz-google')),'callback' => array($this,'callback_translate_analytics')),
				              array('field' => 'translate_analytics_ua','title' => ucwords(__('google analytics UA','sz-google')),'callback' => array($this,'callback_translate_analytics_ua')),),
			);

			// Calling up the function of the parent class to process the 
			// variables that contain the values ​​of configuration section

			parent::moduleAddFields();
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_translate_meta() 
		{
			$this->moduleCommonFormText('sz_google_options_translate','translate_meta','large',__('insert your META code','sz-google'));
			$this->moduleCommonFormDescription(__('before you use the google translate module must register the site in google account using Google Translate Tools. Once inserit your site to perform the action "get code", display meta code and insert this in the field.','sz-google'));
		}

		function callback_translate_mode() 
		{
			$values = array(
				'I1' => __('inline vertical'  ,'sz-google'),
				'I2' => __('inline horizontal','sz-google'),
				'I3' => __('inline dropdown'  ,'sz-google'),
			); 

			$this->moduleCommonFormSelect('sz_google_options_translate','translate_mode',$values,'medium','');
			$this->moduleCommonFormDescription(__('with this parameter you can set the type of view you want to use for the widget to translate the language selection, you can choose for example vertical, horizontal or simple. If you want to use a custom positioning can use the function PHP.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_translate_language() 
		{
			$values = SZGoogleCommon::getLanguages();
			$this->moduleCommonFormSelect('sz_google_options_translate','translate_language',$values,'medium','');
			$this->moduleCommonFormDescription(__('specify the language associated with your website, if you do not specify any value will be called get_bloginfo(\'language\') and set the same language related to the theme of wordpress. Supported languages ​​http://translate.google.com/about/.','sz-google'));
		}

		function callback_translate_widget() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_translate','translate_widget');
			$this->moduleCommonFormDescription(__('if you enable this option you will find the widget required in the administration menu of your widget and you can plug it into any sidebar defined in your theme. If you disable this option, remember not to leave the widget connected to existing sidebar.','sz-google'));
		}

		function callback_translate_shortcode() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_translate','translate_shortcode');
			$this->moduleCommonFormDescription(sprintf(__('if you enable this option you can use the shortcode %s and enter the corresponding component directly in your article or page. Normally in the shortcodes can be specified the options for customizations.','sz-google'),'[sz-gtranslate]'));
		}

		function callback_translate_automatic() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_translate','translate_automatic');
			$this->moduleCommonFormDescription(__('automatically display translation banner to users speaking languages other than the language of your page. If the language set on the visitor\'s browser is different from that of the website page displays the banner of translation.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_translate_multiple() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_translate','translate_multiple');
			$this->moduleCommonFormDescription(__('your page contains content in multiple languages. Enable this option only if your pages contain content in different languages, in this case Google will use an algorithm of analysis other than the standard. For details read the official documentation.','sz-google'));
		}

		function callback_translate_analytics() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_translate','translate_analytics');
			$this->moduleCommonFormDescription(__('if you enable this option, you can check the requirements and the translation statistics directly to your google analytics account. Remember that to run this option you must specify the code assigned to your profile analytics.','sz-google'));
		}

		function callback_translate_analytics_ua() 
		{
			$this->moduleCommonFormText('sz_google_options_translate','translate_analytics_ua','medium',__('google analytics UA','sz-google'));
			$this->moduleCommonFormDescription(__('enter the code assigned to the profile of google analytics on which to collect statistical data relating to requests for translation. If you have the google analytics module of the plugin is automatically taken into the UA code of module.','sz-google'));
		}
	}
}