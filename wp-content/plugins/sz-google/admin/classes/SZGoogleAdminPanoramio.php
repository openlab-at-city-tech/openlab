<?php

/**
 * Module to the definition of the functions that relate to both the
 * widgets that shortcode, but also filters and actions that the module
 * can integrating with adding functionality into wordpress.
 *
 * @package SZGoogle
 * @subpackage Admin
 * @author Eugenio Petullà
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Before the definition of the class, check if there is a definition 
// with the same name or the same as previously defined in other script.

if (!class_exists('SZGoogleAdminPanoramio'))
{
	class SZGoogleAdminPanoramio extends SZGoogleAdmin
	{
		/**
		 * Creating the menu on the admin panel using values ​​
		 * such as configuration variables object (parent function)
		 */

		function moduleAddMenu()
		{
			// Definition of general values ​​for the creation of a menu associated 
			// with the module options. Example slug, page title and menu title

			$this->menuslug   = 'sz-google-admin-panoramio.php';
			$this->pagetitle  = ucwords(__('google panoramio','sz-google'));
			$this->menutitle  = ucwords(__('google panoramio','sz-google'));

			// General definition array containing a list of sections
			// On every section you have to define an array to list fields

			$this->sectionstabs = array(
				'01' => array('anchor' => 'shortcodes' ,'description' => __('shortcodes','sz-google')),
				'02' => array('anchor' => 'widgets'    ,'description' => __('widgets'   ,'sz-google')),
			);

			$this->sections = array(
				array('tab' => '01','section' => 'sz-google-admin-panoramio-s-enable.php' ,'title' => ucwords(__('activation','sz-google'))),
				array('tab' => '01','section' => 'sz-google-admin-panoramio-s-options.php','title' => ucwords(__('options'   ,'sz-google'))),
				array('tab' => '02','section' => 'sz-google-admin-panoramio-w-enable.php' ,'title' => ucwords(__('activation','sz-google'))),
				array('tab' => '02','section' => 'sz-google-admin-panoramio-w-options.php','title' => ucwords(__('options'   ,'sz-google'))),
			);

			$this->sectionstitle   = $this->menutitle;
			$this->sectionsoptions = array('sz_google_options_panoramio');

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
			// Definizione array generale contenente elenco delle sezioni
			// Su ogni sezione bisogna definire un array per elenco campi

			$this->sectionsmenu = array(
				'01' => array('section' => 'sz_google_panoramio_s_active' ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-panoramio-s-enable.php'),
				'02' => array('section' => 'sz_google_panoramio_s_options','title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-panoramio-s-options.php'),
				'03' => array('section' => 'sz_google_panoramio_w_active' ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-panoramio-w-enable.php'),
				'04' => array('section' => 'sz_google_panoramio_w_options','title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-panoramio-w-options.php'),
			);

			// General definition array containing a list of fields
			// All fields are added to the previously defined sections

			$this->sectionsfields = array(
				'01' => array(array('field' => 'panoramio_shortcode'    ,'title' => ucfirst(__('shortcode'          ,'sz-google')),'callback' => array($this,'callback_panoramio_shortcode')),),
				'02' => array(array('field' => 'panoramio_s_template'   ,'title' => ucfirst(__('default template'   ,'sz-google')),'callback' => array($this,'callback_panoramio_s_template')),
				              array('field' => 'panoramio_s_width'      ,'title' => ucfirst(__('default width'      ,'sz-google')),'callback' => array($this,'callback_panoramio_s_width')),
				              array('field' => 'panoramio_s_height'     ,'title' => ucfirst(__('default height'     ,'sz-google')),'callback' => array($this,'callback_panoramio_s_height')),
				              array('field' => 'panoramio_s_orientation','title' => ucfirst(__('default orientation','sz-google')),'callback' => array($this,'callback_panoramio_s_orientation')),
				              array('field' => 'panoramio_s_list_size'  ,'title' => ucfirst(__('default list size'  ,'sz-google')),'callback' => array($this,'callback_panoramio_s_list_size')),
				              array('field' => 'panoramio_s_position'   ,'title' => ucfirst(__('default position'   ,'sz-google')),'callback' => array($this,'callback_panoramio_s_position')),
				              array('field' => 'panoramio_s_paragraph'  ,'title' => ucfirst(__('enable paragraph'   ,'sz-google')),'callback' => array($this,'callback_panoramio_s_paragraph')),),
				'03' => array(array('field' => 'panoramio_widget'       ,'title' => ucfirst(__('widget'             ,'sz-google')),'callback' => array($this,'callback_panoramio_widget')),),
				'04' => array(array('field' => 'panoramio_w_template'   ,'title' => ucfirst(__('default template'   ,'sz-google')),'callback' => array($this,'callback_panoramio_w_template')),
				              array('field' => 'panoramio_w_width'      ,'title' => ucfirst(__('default width'      ,'sz-google')),'callback' => array($this,'callback_panoramio_w_width')),
				              array('field' => 'panoramio_w_height'     ,'title' => ucfirst(__('default height'     ,'sz-google')),'callback' => array($this,'callback_panoramio_w_height')),
				              array('field' => 'panoramio_w_orientation','title' => ucfirst(__('default orientation','sz-google')),'callback' => array($this,'callback_panoramio_w_orientation')),
				              array('field' => 'panoramio_w_list_size'  ,'title' => ucfirst(__('default list size'  ,'sz-google')),'callback' => array($this,'callback_panoramio_w_list_size')),
				              array('field' => 'panoramio_w_position'   ,'title' => ucfirst(__('default position'   ,'sz-google')),'callback' => array($this,'callback_panoramio_w_position')),),
			);

			// Calling up the function of the parent class to process the 
			// variables that contain the values ​​of configuration section

			parent::moduleAddFields();
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_panoramio_shortcode()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_panoramio','panoramio_shortcode');
			$this->moduleCommonFormDescription(sprintf(__('if you enable this option you can use the shortcode %s and enter the corresponding component directly in your article or page. Normally in the shortcodes can be specified the options for customizations.','sz-google'),'[sz-panoramio]'));
		}

		function callback_panoramio_widget()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_panoramio','panoramio_widget');
			$this->moduleCommonFormDescription(__('if you enable this option you will find the widget required in the administration menu of your widget and you can plug it into any sidebar defined in your theme. If you disable this option, remember not to leave the widget connected to existing sidebar.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_panoramio_s_template() 
		{
			$values = array('photo'=>'photo','slideshow'=>'slideshow','list'=>'list','photo_list'=>'photo_list'); 
			$this->moduleCommonFormSelect('sz_google_options_panoramio','panoramio_s_template',$values,'medium','');
			$this->moduleCommonFormDescription(__('photo for a single-photo widget - slideshow for a single-photo widget with a play/pause button that automatically advances to the next photo - list for a photo-list widget - photo_list for a combination of a single-photo widget and a photo-list widget.','sz-google'));
		}

		function callback_panoramio_s_width()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_panoramio','panoramio_s_width','medium','auto');
			$this->moduleCommonFormDescription(__('with this field you can set the width of the container iframe that will be used by defaul, when not specified as a parameter of the shortcode, if you see a value equal "auto", the default size will be 100% and will occupy the entire space of parent container.','sz-google'));
		}

		function callback_panoramio_s_height()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_panoramio','panoramio_s_height','medium','auto');
			$this->moduleCommonFormDescription(__('with this field you can set the height in pixels of the container iframe that will be used by defaul, when not specified as a parameter of the shortcode, if you see a value equal "auto", the default size will be 300 pixels.','sz-google'));
		}

		function callback_panoramio_s_orientation() 
		{
			$values = array('horizontal'=>'horizontal','vertical'=>'vertical'); 
			$this->moduleCommonFormSelect('sz_google_options_panoramio','panoramio_s_orientation',$values,'medium','');
			$this->moduleCommonFormDescription(__('the orientation of the list. Valid values are horizontal and vertical. This controls the position of the arrows, the scrolling direction, and how the photos are sorted. The shape of the list, grid is controlled by the rows and columns options.','sz-google'));
		}

		function callback_panoramio_s_list_size()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_panoramio','panoramio_s_list_size','medium','6');
			$this->moduleCommonFormDescription(__('How many photos to show in the list. This option can only be specified with the template photo_list, for the other template, the option will be ignored. The list can be positioned in different ways, set the parameter "position" to the required value.','sz-google'));
		}

		function callback_panoramio_s_position() 
		{
			$values = array('bottom'=>__('position bottom','sz-google'),'top'=>__('position top','sz-google'),'left'=>__('position left','sz-google'),'right'=>__('position right','sz-google')); 
			$this->moduleCommonFormSelect('sz_google_options_panoramio','panoramio_s_position',$values,'medium','');
			$this->moduleCommonFormDescription(__('Position of the photo list relative to the single-photo widget. Valid values are left, top, right and bottom. This option is valid only with the template photo_list, for the other template, the option will be ignored. The default value if not specified is "bottom".','sz-google'));
		}

		function callback_panoramio_s_paragraph() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_panoramio','panoramio_s_paragraph');
			$this->moduleCommonFormDescription(__('if you enable this option will add a paragraph at the end of the widget, this to be compatible with the theme and use the same features css defined for the block section. If you do not want spacing paragraph disable this option.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_panoramio_w_template() 
		{
			$values = array('photo'=>'photo','slideshow'=>'slideshow','list'=>'list','photo_list'=>'photo_list'); 
			$this->moduleCommonFormSelect('sz_google_options_panoramio','panoramio_w_template',$values,'medium','');
			$this->moduleCommonFormDescription(__('photo for a single-photo widget - slideshow for a single-photo widget with a play/pause button that automatically advances to the next photo - list for a photo-list widget - photo_list for a combination of a single-photo widget and a photo-list widget.','sz-google'));
		}

		function callback_panoramio_w_width()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_panoramio','panoramio_w_width','medium','auto');
			$this->moduleCommonFormDescription(__('with this field you can set the width of the container iframe that will be used by defaul, when not specified as a parameter of the widget, if you see a value equal "auto", the default size will be 100% and will occupy the entire space of parent container.','sz-google'));
		}

		function callback_panoramio_w_height()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_panoramio','panoramio_w_height','medium','auto');
			$this->moduleCommonFormDescription(__('with this field you can set the height in pixels of the container iframe that will be used by defaul, when not specified as a parameter of the widget, if you see a value equal "auto", the default size will be 300 pixels.','sz-google'));
		}

		function callback_panoramio_w_orientation() 
		{
			$values = array('horizontal'=>'horizontal','vertical'=>'vertical'); 
			$this->moduleCommonFormSelect('sz_google_options_panoramio','panoramio_w_orientation',$values,'medium','');
			$this->moduleCommonFormDescription(__('the orientation of the list. Valid values are horizontal and vertical. This controls the position of the arrows, the scrolling direction, and how the photos are sorted. The shape of the list, grid is controlled by the rows and columns options.','sz-google'));
		}

		function callback_panoramio_w_list_size()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_panoramio','panoramio_w_list_size','medium','6');
			$this->moduleCommonFormDescription(__('How many photos to show in the list. This option can only be specified with the template photo_list, for the other template, the option will be ignored. The list can be positioned in different ways, set the parameter "position" to the required value.','sz-google'));
		}

		function callback_panoramio_w_position() 
		{
			$values = array('bottom'=>__('position bottom','sz-google'),'top'=>__('position top','sz-google'),'left'=>__('position left','sz-google'),'right'=>__('position right','sz-google')); 
			$this->moduleCommonFormSelect('sz_google_options_panoramio','panoramio_w_position',$values,'medium','');
			$this->moduleCommonFormDescription(__('Position of the photo list relative to the single-photo widget. Valid values are left, top, right and bottom. This option is valid only with the template photo_list, for the other template, the option will be ignored. The default value if not specified is "bottom".','sz-google'));
		}
	}
}