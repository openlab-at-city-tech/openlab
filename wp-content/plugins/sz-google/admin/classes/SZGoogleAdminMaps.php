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

if (!class_exists('SZGoogleAdminMaps'))
{
	class SZGoogleAdminMaps extends SZGoogleAdmin
	{
		/**
		 * Creating the menu on the admin panel using values ​​
		 * such as configuration variables object (parent function)
		 */

		function moduleAddMenu()
		{
			// Definition of general values ​​for the creation of a menu associated 
			// with the module options. Example slug, page title and menu title

			$this->menuslug   = 'sz-google-admin-maps.php';
			$this->pagetitle  = ucwords(__('google maps','sz-google'));
			$this->menutitle  = ucwords(__('google maps','sz-google'));

			// Definition of sections that need to be made ​​in HTML
			// sections must be passed as an array of name = > title

			$this->sectionstabs = array(
				'01' => array('anchor' => 'general'    ,'description' => __('general'   ,'sz-google')),
				'02' => array('anchor' => 'shortcodes' ,'description' => __('shortcodes','sz-google')),
				'03' => array('anchor' => 'widgets'    ,'description' => __('widgets'   ,'sz-google')),
			);

			$this->sections = array(
				array('tab' => '01','section' => 'sz-google-admin-maps-language.php' ,'title' => ucwords(__('language'  ,'sz-google'))),
				array('tab' => '01','section' => 'sz-google-admin-maps-options.php'  ,'title' => ucwords(__('options'   ,'sz-google'))),
				array('tab' => '02','section' => 'sz-google-admin-maps-s-enable.php' ,'title' => ucwords(__('activation','sz-google'))),
				array('tab' => '02','section' => 'sz-google-admin-maps-s-options.php','title' => ucwords(__('options'   ,'sz-google'))),
				array('tab' => '03','section' => 'sz-google-admin-maps-w-enable.php' ,'title' => ucwords(__('activation','sz-google'))),
				array('tab' => '03','section' => 'sz-google-admin-maps-w-options.php','title' => ucwords(__('options'   ,'sz-google'))),
			);

			$this->sectionstitle   = $this->menutitle;
			$this->sectionsoptions = array('sz_google_options_maps');

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
				'01' => array('section' => 'sz_google_maps_language' ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-maps-language.php'),
				'02' => array('section' => 'sz_google_maps_options'  ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-maps-options.php'),
				'03' => array('section' => 'sz_google_maps_s_active' ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-maps-s-enable.php'),
				'04' => array('section' => 'sz_google_maps_s_options','title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-maps-s-options.php'),
				'05' => array('section' => 'sz_google_maps_w_active' ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-maps-w-enable.php'),
				'06' => array('section' => 'sz_google_maps_w_options','title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-maps-w-options.php'),
			);

			// General definition array containing a list of fields
			// All fields are added to the previously defined sections

			$this->sectionsfields = array(
				'01' => array(array('field' => 'maps_language'  ,'title' => ucfirst(__('select language'  ,'sz-google')),'callback' => array($this,'callback_maps_language')),),
				'02' => array(array('field' => 'maps_accesskey' ,'title' => ucwords(__('maps key'         ,'sz-google')),'callback' => array($this,'callback_maps_key')),
				              array('field' => 'maps_sensor'    ,'title' => ucwords(__('maps sensor'      ,'sz-google')),'callback' => array($this,'callback_maps_sensor')),
				              array('field' => 'maps_signin'    ,'title' => ucwords(__('maps signin'      ,'sz-google')),'callback' => array($this,'callback_maps_signin')),),
				'03' => array(array('field' => 'maps_s_enable'  ,'title' => ucfirst(__('shortcode'        ,'sz-google')),'callback' => array($this,'callback_maps_s_enable')),),
				'04' => array(array('field' => 'maps_s_width'   ,'title' => ucfirst(__('default width'    ,'sz-google')),'callback' => array($this,'callback_maps_s_width')),
				              array('field' => 'maps_s_height'  ,'title' => ucfirst(__('default height'   ,'sz-google')),'callback' => array($this,'callback_maps_s_height')),
				              array('field' => 'maps_s_lat'     ,'title' => ucfirst(__('default latitude' ,'sz-google')),'callback' => array($this,'callback_maps_s_lat')),
				              array('field' => 'maps_s_lng'     ,'title' => ucfirst(__('default longitude','sz-google')),'callback' => array($this,'callback_maps_s_lng')),
				              array('field' => 'maps_s_zoom'    ,'title' => ucfirst(__('default zoom'     ,'sz-google')),'callback' => array($this,'callback_maps_s_zoom')),
				              array('field' => 'maps_s_view'    ,'title' => ucfirst(__('default view'     ,'sz-google')),'callback' => array($this,'callback_maps_s_view')),
				              array('field' => 'maps_s_layer'   ,'title' => ucfirst(__('default layer'    ,'sz-google')),'callback' => array($this,'callback_maps_s_layer')),
				              array('field' => 'maps_s_wheel'   ,'title' => ucfirst(__('default wheel'    ,'sz-google')),'callback' => array($this,'callback_maps_s_wheel')),
				              array('field' => 'maps_s_marker'  ,'title' => ucfirst(__('default marker'   ,'sz-google')),'callback' => array($this,'callback_maps_s_marker')),
				              array('field' => 'maps_s_lazy'    ,'title' => ucfirst(__('lazy load'        ,'sz-google')),'callback' => array($this,'callback_maps_s_lazy')),
				),
				'05' => array(array('field' => 'maps_w_enable'  ,'title' => ucfirst(__('widget'           ,'sz-google')),'callback' => array($this,'callback_maps_w_enable')),),
				'06' => array(array('field' => 'maps_w_width'   ,'title' => ucfirst(__('default width'    ,'sz-google')),'callback' => array($this,'callback_maps_w_width')),
				              array('field' => 'maps_w_height'  ,'title' => ucfirst(__('default height'   ,'sz-google')),'callback' => array($this,'callback_maps_w_height')),
				              array('field' => 'maps_w_lat'     ,'title' => ucfirst(__('default latitude' ,'sz-google')),'callback' => array($this,'callback_maps_w_lat')),
				              array('field' => 'maps_w_lng'     ,'title' => ucfirst(__('default longitude','sz-google')),'callback' => array($this,'callback_maps_w_lng')),
				              array('field' => 'maps_w_zoom'    ,'title' => ucfirst(__('default zoom'     ,'sz-google')),'callback' => array($this,'callback_maps_w_zoom')),
				              array('field' => 'maps_w_view'    ,'title' => ucfirst(__('default view'     ,'sz-google')),'callback' => array($this,'callback_maps_w_view')),
				              array('field' => 'maps_w_layer'   ,'title' => ucfirst(__('default layer'    ,'sz-google')),'callback' => array($this,'callback_maps_w_layer')),
				              array('field' => 'maps_w_wheel'   ,'title' => ucfirst(__('default wheel'    ,'sz-google')),'callback' => array($this,'callback_maps_w_wheel')),
				              array('field' => 'maps_w_marker'  ,'title' => ucfirst(__('default marker'   ,'sz-google')),'callback' => array($this,'callback_maps_w_marker')),
				              array('field' => 'maps_w_lazy'    ,'title' => ucfirst(__('lazy load'        ,'sz-google')),'callback' => array($this,'callback_maps_w_lazy')),
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

		function callback_maps_language() 
		{
			$values = SZGoogleCommon::getLanguages();
			$this->moduleCommonFormSelect('sz_google_options_maps','maps_language',$values,'medium','');
			$this->moduleCommonFormDescription(__('specify the language code associated with your website, if you do not specify any value will be called the get_bloginfo(\'language\') and set the same language related to the theme of wordpress.','sz-google'));
		}

		function callback_maps_key() 
		{
			$this->moduleCommonFormText('sz_google_options_maps','maps_key','medium',__('insert API Key','sz-google'));
			$this->moduleCommonFormDescription(__('the key associated with the API call is not mandatory in an usage normal, but if it surpassed the 25,000 request per day or you want to keep track statistics about the views of maps, in this case you get a key issued by google.','sz-google'));
		}

		function callback_maps_sensor() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_maps','maps_sensor');
			$this->moduleCommonFormDescription(__('this parameter indicates whether you have to use the sensor location in order to obtain the correct position of the visitor. Enable this option only if you really need. User will be notified of the option is activated and will have to grant permission.','sz-google'));
		}

		function callback_maps_signin() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_maps','maps_signin');
			$this->moduleCommonFormDescription(__('when you enable sign-in with the Google Maps JavaScript API, the maps on your site will be tailored to your users. Users who are signed-in to their Google account will be able to save places for later viewing on the web or mobile devices.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)
		 */

		function callback_maps_s_enable() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_maps','maps_s_enable');
			$this->moduleCommonFormDescription(sprintf(__('if you enable this option you can use the shortcode %s and enter the corresponding component directly in your article or page. Normally in the shortcodes can be specified the options for customizations.','sz-google'),'[sz-maps]'));
		}

		function callback_maps_s_width()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_maps','maps_s_width','medium','auto');
			$this->moduleCommonFormDescription(__('with this field you can set the width of the container iframe that will be used by defaul, when not specified as a parameter of the shortcode, if you see a value equal "auto", the default size will be 100% and will occupy the entire space of parent container.','sz-google'));
		}

		function callback_maps_s_height()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_maps','maps_s_height','medium','auto');
			$this->moduleCommonFormDescription(__('with this field you can set the height in pixels of the container iframe that will be used by defaul, when not specified as a parameter of the shortcode, if you see a value equal "auto", will be used the default size of the plugin.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)
		 */

		function callback_maps_s_lat()
		{
			$this->moduleCommonFormText('sz_google_options_maps','maps_s_lat','medium',__('enter the latitude','sz-google'));
			$this->moduleCommonFormDescription(__('specify the default value for the latitude of the map. This value will be used if no value is specified on the option dedicated. Make sure that the value is exactly and with (.) as a separator. (english mode)','sz-google'));
		}

		function callback_maps_s_lng()
		{
			$this->moduleCommonFormText('sz_google_options_maps','maps_s_lng','medium',__('enter the longitude','sz-google'));
			$this->moduleCommonFormDescription(__('specify the default value for longitude of the map. This value will be used if no value is specified on the option dedicated. Make sure that the value is exactly and with (.) as a separator. (english mode)','sz-google'));
		}

		function callback_maps_s_zoom()
		{
			$values = array('01'=>'1','02'=>'2','03'=>'3','04'=>'4','05'=>'5','06'=>'6','07'=>'7','08'=>'8','09'=>'9','10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18','19'=>'19','20'=>'20');
			$this->moduleCommonFormSelect('sz_google_options_maps','maps_s_zoom',$values,'medium','');
			$this->moduleCommonFormDescription(__('specify the value of ZOOM default to be applied to the component when you do not specify any value. This value controls the magnification of the map when it appears. The allowed value is a number from 1 to 20.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)
		 */

		function callback_maps_s_view()
		{
			$values = array('ROADMAP'=>SZGOOGLE_UPPER(__('roadmap','sz-google')),'SATELLITE'=>SZGOOGLE_UPPER(__('satellite','sz-google')),'HYBRID'=>SZGOOGLE_UPPER(__('hybrid','sz-google')),'TERRAIN'=>SZGOOGLE_UPPER(__('terrain','sz-google')));
			$this->moduleCommonFormSelect('sz_google_options_maps','maps_s_view',$values,'medium','');
			$this->moduleCommonFormDescription(__('the google map can be displayed in different ways. Specify the default map to be submitted in case you missed a more specific option. The possible values ​​are: ROADMAP, SATELLITE, HYBRID, TERRAIN. See documentation for more informations.','sz-google'));
		}

		function callback_maps_s_layer()
		{
			$values = array('NOTHING'=>SZGOOGLE_UPPER(__('nothing','sz-google')),'TRAFFIC'=>SZGOOGLE_UPPER(__('traffic','sz-google')),'TRANSIT'=>SZGOOGLE_UPPER(__('transit','sz-google')),'BICYCLE'=>SZGOOGLE_UPPER(__('bicycle','sz-google')));
			$this->moduleCommonFormSelect('sz_google_options_maps','maps_s_layer',$values,'medium','');
			$this->moduleCommonFormDescription(__('if you use the maps with a specific layer, you can set this value by default. In this way it will be automatically added to your maps. The possible values ​​are NOTHING, TRAFFIC, TRANSIT and BICYCLE.','sz-google'));
		}

		function callback_maps_s_wheel()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_maps','maps_s_wheel');
			$this->moduleCommonFormDescription(__('with this option you can enable or disable the zoom function using the mouse wheel. In fact, many times the central maps can prevent the natural flow of a web page and agrees to disable the function.','sz-google'));
		}

		function callback_maps_s_marker()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_maps','maps_s_marker');
			$this->moduleCommonFormDescription(__('if this option is enabled will be added to the marker on the map, which corresponds to the specified coordinates. Will still be possible to change this behavior with the options in the shortcode or widget map.','sz-google'));
		}

		function callback_maps_s_lazy()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_maps','maps_s_lazy');
			$this->moduleCommonFormDescription(__('if you enable this option, the loading the map will be in a "lazy load", the code will be executed only when the map will be displayed on the page. This option improves performance but may have problems with some browsers.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)
		 */

		function callback_maps_w_enable() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_maps','maps_w_enable');
			$this->moduleCommonFormDescription(__('if you enable this option you will find the widget required in the administration menu of your widget and you can plug it into any sidebar defined in your theme. If you disable this option, remember not to leave the widget connected to existing sidebar.','sz-google'));
		}

		function callback_maps_w_width()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_maps','maps_w_width','medium','auto');
			$this->moduleCommonFormDescription(__('with this field you can set the width of the container iframe that will be used by defaul, when not specified as a parameter of the shortcode, if you see a value equal "auto", the default size will be 100% and will occupy the entire space of parent container.','sz-google'));
		}

		function callback_maps_w_height()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_maps','maps_w_height','medium','auto');
			$this->moduleCommonFormDescription(__('with this field you can set the height in pixels of the container iframe that will be used by defaul, when not specified as a parameter of the shortcode, if you see a value equal "auto", will be used the default size of the plugin.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)
		 */

		function callback_maps_w_lat()
		{
			$this->moduleCommonFormText('sz_google_options_maps','maps_w_lat','medium',__('enter the latitude','sz-google'));
			$this->moduleCommonFormDescription(__('specify the default value for the latitude of the map. This value will be used if no value is specified on the option dedicated. Make sure that the value is exactly and with (.) as a separator. (english mode)','sz-google'));
		}

		function callback_maps_w_lng()
		{
			$this->moduleCommonFormText('sz_google_options_maps','maps_w_lng','medium',__('enter the longitude','sz-google'));
			$this->moduleCommonFormDescription(__('specify the default value for longitude of the map. This value will be used if no value is specified on the option dedicated. Make sure that the value is exactly and with (.) as a separator. (english mode)','sz-google'));
		}

		function callback_maps_w_zoom()
		{
			$values = array('01'=>'1','02'=>'2','03'=>'3','04'=>'4','05'=>'5','06'=>'6','07'=>'7','08'=>'8','09'=>'9','10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18','19'=>'19','20'=>'20');
			$this->moduleCommonFormSelect('sz_google_options_maps','maps_w_zoom',$values,'medium','');
			$this->moduleCommonFormDescription(__('specify the value of ZOOM default to be applied to the component when you do not specify any value. This value controls the magnification of the map when it appears. The allowed value is a number from 1 to 20.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)
		 */

		function callback_maps_w_view()
		{
			$values = array('ROADMAP'=>SZGOOGLE_UPPER(__('roadmap','sz-google')),'SATELLITE'=>SZGOOGLE_UPPER(__('satellite','sz-google')),'HYBRID'=>SZGOOGLE_UPPER(__('hybrid','sz-google')),'TERRAIN'=>SZGOOGLE_UPPER(__('terrain','sz-google')));
			$this->moduleCommonFormSelect('sz_google_options_maps','maps_w_view',$values,'medium','');
			$this->moduleCommonFormDescription(__('the google map can be displayed in different ways. Specify the default map to be submitted in case you missed a more specific option. The possible values ​​are: ROADMAP, SATELLITE, HYBRID, TERRAIN. See documentation for more informations.','sz-google'));
		}

		function callback_maps_w_layer()
		{
			$values = array('NOTHING'=>SZGOOGLE_UPPER(__('nothing','sz-google')),'TRAFFIC'=>SZGOOGLE_UPPER(__('traffic','sz-google')),'TRANSIT'=>SZGOOGLE_UPPER(__('transit','sz-google')),'BICYCLE'=>SZGOOGLE_UPPER(__('bicycle','sz-google')));
			$this->moduleCommonFormSelect('sz_google_options_maps','maps_w_layer',$values,'medium','');
			$this->moduleCommonFormDescription(__('if you use the maps with a specific layer, you can set this value by default. In this way it will be automatically added to your maps. The possible values ​​are NOTHING, TRAFFIC, TRANSIT and BICYCLE.','sz-google'));
		}

		function callback_maps_w_wheel()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_maps','maps_w_wheel');
			$this->moduleCommonFormDescription(__('with this option you can enable or disable the zoom function using the mouse wheel. In fact, many times the central maps can prevent the natural flow of a web page and agrees to disable the function.','sz-google'));
		}

		function callback_maps_w_marker()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_maps','maps_w_marker');
			$this->moduleCommonFormDescription(__('if this option is enabled will be added to the marker on the map, which corresponds to the specified coordinates. Will still be possible to change this behavior with the options in the shortcode or widget map.','sz-google'));
		}

		function callback_maps_w_lazy()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_maps','maps_w_lazy');
			$this->moduleCommonFormDescription(__('if you enable this option, the loading the map will be in a "lazy load", the code will be executed only when the map will be displayed on the page. This option improves performance but may have problems with some browsers.','sz-google'));
		}
	}
}