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

if (!class_exists('SZGoogleAdminGroups'))
{
	class SZGoogleAdminGroups extends SZGoogleAdmin
	{
		/**
		 * Creating the menu on the admin panel using values ​​
		 * such as configuration variables object (parent function)
		 */

		function moduleAddMenu()
		{
			// Definition of general values ​​for the creation of a menu associated 
			// with the module options. Example slug, page title and menu title

			$this->menuslug   = 'sz-google-admin-groups.php';
			$this->pagetitle  = ucwords(__('google groups','sz-google'));
			$this->menutitle  = ucwords(__('google groups','sz-google'));

			// Definition of sections that need to be made ​​in HTML
			// sections must be passed as an array of name = > title

			$this->sectionstabs = array(
				'01' => array('anchor' => 'general','description' => __('general','sz-google')),
			);

			$this->sections = array(
				array('tab' => '01','section' => 'sz-google-admin-groups-language.php','title' => ucwords(__('language setting','sz-google'))),
				array('tab' => '01','section' => 'sz-google-admin-groups-enable.php'  ,'title' => ucwords(__('activation'      ,'sz-google'))),
				array('tab' => '01','section' => 'sz-google-admin-groups-display.php' ,'title' => ucwords(__('display setting' ,'sz-google'))),
			);

			$this->sectionstitle   = $this->menutitle;
			$this->sectionsoptions = array('sz_google_options_groups');

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
				'01' => array('section' => 'sz_google_groups_language','title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-groups-language.php'),
				'02' => array('section' => 'sz_google_groups_active'  ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-groups-enable.php'),
				'03' => array('section' => 'sz_google_groups_display' ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-groups-display.php'),
			);

			// General definition array containing a list of fields
			// All fields are added to the previously defined sections

			$this->sectionsfields = array(
				'01' => array(array('field' => 'groups_language'   ,'title' => ucfirst(__('default language','sz-google')),'callback' => array($this,'callback_groups_language')),),
				'02' => array(array('field' => 'groups_shortcode'  ,'title' => ucfirst(__('shortcode'       ,'sz-google')),'callback' => array($this,'callback_groups_shortcode')),
				              array('field' => 'groups_widget'     ,'title' => ucfirst(__('widget'          ,'sz-google')),'callback' => array($this,'callback_groups_widget')),),
				'03' => array(array('field' => 'groups_name'       ,'title' => ucfirst(__('group name'      ,'sz-google')),'callback' => array($this,'callback_groups_name')),
				              array('field' => 'groups_showsearch' ,'title' => ucfirst(__('show search'     ,'sz-google')),'callback' => array($this,'callback_groups_showsearch')),
				              array('field' => 'groups_showtabs'   ,'title' => ucfirst(__('show tabs'       ,'sz-google')),'callback' => array($this,'callback_groups_showtabs')),
				              array('field' => 'groups_hidetitle'  ,'title' => ucfirst(__('hide title'      ,'sz-google')),'callback' => array($this,'callback_groups_hidetitle')),
				              array('field' => 'groups_hidesubject','title' => ucfirst(__('hide subject'    ,'sz-google')),'callback' => array($this,'callback_groups_hidesubject')),
				              array('field' => 'groups_width'      ,'title' => ucfirst(__('default width'   ,'sz-google')),'callback' => array($this,'callback_groups_width')),
				              array('field' => 'groups_height'     ,'title' => ucfirst(__('default height'  ,'sz-google')),'callback' => array($this,'callback_groups_height')),),
			);

			// Calling up the function of the parent class to process the 
			// variables that contain the values ​​of configuration section

			parent::moduleAddFields();
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_groups_widget() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_groups','groups_widget');
			$this->moduleCommonFormDescription(__('if you enable this option you will find the widget required in the administration menu of your widget and you can plug it into any sidebar defined in your theme. If you disable this option, remember not to leave the widget connected to existing sidebar.','sz-google'));
		}

		function callback_groups_shortcode() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_groups','groups_shortcode');
			$this->moduleCommonFormDescription(sprintf(__('if you enable this option you can use the shortcode %s and enter the corresponding component directly in your article or page. Normally in the shortcodes can be specified the options for customizations.','sz-google'),'[sz-ggroups]'));
		}

		function callback_groups_language() 
		{
			$values = SZGoogleCommon::getLanguages();
			$this->moduleCommonFormSelect('sz_google_options_groups','groups_language',$values,'medium','');
			$this->moduleCommonFormDescription(__('specify the language associated with your website, if you do not specify any value will be called the get_bloginfo(\'language\') and set the same language related to the theme of wordpress. Supported languages ​​http://translate.google.com/about/.','sz-google'));
		}

		function callback_groups_name() 
		{
			$this->moduleCommonFormText('sz_google_options_groups','groups_name','medium',__('insert default name','sz-google'));
			$this->moduleCommonFormDescription(__('in this area specify a group name that will be used in all those conditions in which you do not specify any value for the parameter "name". In any case, you can specify any name that is on the shortcode on the widget module google groups.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_groups_showsearch() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_groups','groups_showsearch');
			$this->moduleCommonFormDescription(__('select value "yes" if you want to show a search box, "no" if you don\'t want the box to show. This field is used as default value, but you can change this by specifying a specific value via the shortcode or php function. See official documentation.','sz-google'));
		}

		function callback_groups_showtabs() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_groups','groups_showtabs');
			$this->moduleCommonFormDescription(__('select value "yes" if you want to show the view selector tabs, "no" if you don\'t want to show tabs. This field is used as default value, but you can change this by specifying a specific value via the shortcode or php function. See official documentation.','sz-google'));
		}

		function callback_groups_hidetitle() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_groups','groups_hidetitle');
			$this->moduleCommonFormDescription(__('select value "yes" if you want to hide the forum title and description, "no" if you don\'t want to leave the title or description. This field is used as default value, but you can change this by specifying a specific value in shortcode or php function.','sz-google'));
		}

		function callback_groups_hidesubject() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_groups','groups_hidesubject');
			$this->moduleCommonFormDescription(__('select value "yes" if you want to hide the subject of the last post in My Forums view, "no" if you want to leave the subject visible. This field is used as default value, but you can change this by specifying a specific value in shortcode or php function.','sz-google'));
		}

		function callback_groups_width() 
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_groups','groups_width','medium','auto');
			$this->moduleCommonFormDescription(__('with this field you can set the width of the container iframe that will be used by defaul, when not specified as a parameter of the widget or the shortcode, if you see a value equal to zero, the default size will be 100% and will occupy the entire space.','sz-google'));
		}

		function callback_groups_height() 
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_groups','groups_height','medium','auto');
			$this->moduleCommonFormDescription(__('with this field you can set the height in pixels of the container iframe that will be used by defaul, when not specified as a parameter of the widget or the shortcode, if you see a value equal to zero, the default size will be 700 pixels.','sz-google'));
		}
	}
}