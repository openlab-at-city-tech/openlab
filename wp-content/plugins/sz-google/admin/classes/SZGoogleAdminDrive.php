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

if (!class_exists('SZGoogleAdminDrive'))
{
	class SZGoogleAdminDrive extends SZGoogleAdmin
	{
		/**
		 * Creating the menu on the admin panel using values ​​
		 * such as configuration variables object (parent function)
		 */

		function moduleAddMenu()
		{
			// Definition of general values ​​for the creation of a menu associated 
			// with the module options. Example slug, page title and menu title

			$this->menuslug   = 'sz-google-admin-drive.php';
			$this->pagetitle  = ucwords(__('google drive','sz-google'));
			$this->menutitle  = ucwords(__('google drive','sz-google'));

			// Definition of sections that need to be made ​​in HTML
			// sections must be passed as an array of name = > title

			$this->sectionstabs = array(
				'01' => array('anchor' => 'general' ,'description' => __('general','sz-google')),
				'02' => array('anchor' => 'embed'   ,'description' => __('embed'  ,'sz-google')),
				'03' => array('anchor' => 'viewer'  ,'description' => __('viewer' ,'sz-google')),
			);

			$this->sections = array(
				array('tab' => '01','section' => 'sz-google-admin-drive.php'                  ,'title' => ucwords(__('settings'        ,'sz-google'))),
				array('tab' => '01','section' => 'sz-google-admin-drive-savebutton-enable.php','title' => ucwords(__('save to drive'   ,'sz-google'))),
				array('tab' => '02','section' => 'sz-google-admin-drive-embed-enable-s.php'   ,'title' => ucwords(__('embed shortcode' ,'sz-google'))),
				array('tab' => '02','section' => 'sz-google-admin-drive-embed-enable-w.php'   ,'title' => ucwords(__('embed widget'    ,'sz-google'))),
				array('tab' => '03','section' => 'sz-google-admin-drive-viewer-enable-s.php'  ,'title' => ucwords(__('viewer shortcode','sz-google'))),
				array('tab' => '03','section' => 'sz-google-admin-drive-viewer-enable-w.php'  ,'title' => ucwords(__('viewer widget'   ,'sz-google'))),
			);

			$this->sectionstitle   = $this->menutitle;
			$this->sectionsoptions = array('sz_google_options_drive');

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
				'01' => array('section' => 'sz_google_drive_section'   ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-drive.php'),
				'02' => array('section' => 'sz_google_drive_savebutton','title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-drive-savebutton-enable.php'),
				'03' => array('section' => 'sz_google_drive_embed_s'   ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-drive-embed-enable-s.php'),
				'04' => array('section' => 'sz_google_drive_embed_w'   ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-drive-embed-enable-w.php'),
				'05' => array('section' => 'sz_google_drive_viewer_s'  ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-drive-viewer-enable-s.php'),
				'06' => array('section' => 'sz_google_drive_viewer_w'  ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-drive-viewer-enable-w.php'),
			);

			// General definition array containing a list of fields
			// All fields are added to the previously defined sections

			$this->sectionsfields = array(
				'01' => array(array('field' => 'drive_sitename'            ,'title' => ucfirst(__('site name'          ,'sz-google')),'callback' => array($this,'callback_drive_sitename')),),
				'02' => array(array('field' => 'drive_savebutton_shortcode','title' => ucfirst(__('shortcode'          ,'sz-google')),'callback' => array($this,'callback_drive_savebutton_shortcode')),
				              array('field' => 'drive_savebutton_widget'   ,'title' => ucfirst(__('widget'             ,'sz-google')),'callback' => array($this,'callback_drive_savebutton_widget')),),
				'03' => array(array('field' => 'drive_embed_shortcode'     ,'title' => ucfirst(__('shortcode'          ,'sz-google')),'callback' => array($this,'callback_drive_embed_shortcode')),
				              array('field' => 'drive_embed_s_width'       ,'title' => ucfirst(__('default width'      ,'sz-google')),'callback' => array($this,'callback_drive_embed_s_width')),
				              array('field' => 'drive_embed_s_height'      ,'title' => ucfirst(__('default height'     ,'sz-google')),'callback' => array($this,'callback_drive_embed_s_height')),
				              array('field' => 'drive_embed_s_height_p'    ,'title' => ucfirst(__('presentation height','sz-google')),'callback' => array($this,'callback_drive_embed_s_height_p')),
				              array('field' => 'drive_embed_s_height_v'    ,'title' => ucfirst(__('video height'       ,'sz-google')),'callback' => array($this,'callback_drive_embed_s_height_v')),),
				'04' => array(array('field' => 'drive_embed_widget'        ,'title' => ucfirst(__('widget'             ,'sz-google')),'callback' => array($this,'callback_drive_embed_widget')),
				              array('field' => 'drive_embed_w_width'       ,'title' => ucfirst(__('default width'      ,'sz-google')),'callback' => array($this,'callback_drive_embed_w_width')),
				              array('field' => 'drive_embed_w_height'      ,'title' => ucfirst(__('default height'     ,'sz-google')),'callback' => array($this,'callback_drive_embed_w_height')),
				              array('field' => 'drive_embed_w_height_p'    ,'title' => ucfirst(__('presentation height','sz-google')),'callback' => array($this,'callback_drive_embed_w_height_p')),
				              array('field' => 'drive_embed_w_height_v'    ,'title' => ucfirst(__('video height'       ,'sz-google')),'callback' => array($this,'callback_drive_embed_w_height_v')),),
				'05' => array(array('field' => 'drive_viewer_shortcode'    ,'title' => ucfirst(__('shortcode'          ,'sz-google')),'callback' => array($this,'callback_drive_viewer_shortcode')),
				              array('field' => 'drive_viewer_s_width'      ,'title' => ucfirst(__('default width'      ,'sz-google')),'callback' => array($this,'callback_drive_viewer_s_width')),
				              array('field' => 'drive_viewer_s_height'     ,'title' => ucfirst(__('default height'     ,'sz-google')),'callback' => array($this,'callback_drive_viewer_s_height')),
				              array('field' => 'drive_viewer_s_t_position' ,'title' => ucfirst(__('title position'     ,'sz-google')),'callback' => array($this,'callback_drive_viewer_s_t_position')),
				              array('field' => 'drive_viewer_s_t_align'    ,'title' => ucfirst(__('title alignment'    ,'sz-google')),'callback' => array($this,'callback_drive_viewer_s_t_align')),),
				'06' => array(array('field' => 'drive_viewer_widget'       ,'title' => ucfirst(__('widget'             ,'sz-google')),'callback' => array($this,'callback_drive_viewer_widget')),
				              array('field' => 'drive_viewer_w_width'      ,'title' => ucfirst(__('default width'      ,'sz-google')),'callback' => array($this,'callback_drive_viewer_w_width')),
				              array('field' => 'drive_viewer_w_height'     ,'title' => ucfirst(__('default height'     ,'sz-google')),'callback' => array($this,'callback_drive_viewer_w_height')),
				              array('field' => 'drive_viewer_w_t_position' ,'title' => ucfirst(__('title position'     ,'sz-google')),'callback' => array($this,'callback_drive_viewer_w_t_position')),
				              array('field' => 'drive_viewer_w_t_align'    ,'title' => ucfirst(__('title alignment'    ,'sz-google')),'callback' => array($this,'callback_drive_viewer_w_t_align')),),
			);

			// Calling up the function of the parent class to process the 
			// variables that contain the values ​​of configuration section

			parent::moduleAddFields();
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_drive_sitename() 
		{
			$this->moduleCommonFormText('sz_google_options_drive','drive_sitename','large',__('insert your site name','sz-google'));
			$this->moduleCommonFormDescription(__('some functions google drive require the information of the name of the site where the operation took place, you can use this field to customize the name, otherwise it will use the default value in wordpress. See general setting in wordpress admin panel.','sz-google'));
		}

		function callback_drive_embed_shortcode() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_drive','drive_embed_shortcode');
			$this->moduleCommonFormDescription(sprintf(__('if you enable this option you can use the shortcode %s and enter the corresponding component directly in your article or page. Normally in the shortcodes can be specified the options for customizations.','sz-google'),'[sz-drive-embed]'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_drive_embed_s_width()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_drive','drive_embed_s_width','medium','auto');
			$this->moduleCommonFormDescription(__('with this field you can set the width of the container iframe that will be used by defaul, when not specified as a parameter of the shortcode, if you see a value equal "auto", the default size will be 100% and will occupy the entire space of parent container.','sz-google'));
		}

		function callback_drive_embed_s_height()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_drive','drive_embed_s_height','medium','auto');
			$this->moduleCommonFormDescription(__('with this field you can set the height in pixels of the container iframe that will be used by defaul, when not specified as a parameter of the shortcode, if you see a value equal "auto", will be used the default size of the plugin.','sz-google'));
		}

		function callback_drive_embed_s_height_p()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_drive','drive_embed_s_height_p','medium','auto');
			$this->moduleCommonFormDescription(__('with this field you can set the height in pixels of the container iframe that will be used by defaul, when not specified as a parameter of the shortcode, if you see a value equal "auto", will be used the default size of the plugin.','sz-google'));
		}

		function callback_drive_embed_s_height_v()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_drive','drive_embed_s_height_v','medium','auto');
			$this->moduleCommonFormDescription(__('with this field you can set the height in pixels of the container iframe that will be used by defaul, when not specified as a parameter of the shortcode, if you see a value equal "auto", will be used the default size of the plugin.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_drive_embed_widget() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_drive','drive_embed_widget');
			$this->moduleCommonFormDescription(__('if you enable this option you will find the widget required in the administration menu of your widget and you can plug it into any sidebar defined in your theme. If you disable this option, remember not to leave the widget connected to existing sidebar.','sz-google'));
		}

		function callback_drive_embed_w_width()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_drive','drive_embed_w_width','medium','auto');
			$this->moduleCommonFormDescription(__('with this field you can set the width of the container iframe that will be used by defaul, when not specified as a parameter of the shortcode, if you see a value equal "auto", the default size will be 100% and will occupy the entire space of parent container.','sz-google'));
		}

		function callback_drive_embed_w_height()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_drive','drive_embed_w_height','medium','auto');
			$this->moduleCommonFormDescription(__('with this field you can set the height in pixels of the container iframe that will be used by defaul, when not specified as a parameter of the shortcode, if you see a value equal "auto", will be used the default size of the plugin.','sz-google'));
		}

		function callback_drive_embed_w_height_p()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_drive','drive_embed_w_height_p','medium','auto');
			$this->moduleCommonFormDescription(__('with this field you can set the height in pixels of the container iframe that will be used by defaul, when not specified as a parameter of the shortcode, if you see a value equal "auto", will be used the default size of the plugin.','sz-google'));
		}

		function callback_drive_embed_w_height_v()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_drive','drive_embed_w_height_w','medium','auto');
			$this->moduleCommonFormDescription(__('with this field you can set the height in pixels of the container iframe that will be used by defaul, when not specified as a parameter of the shortcode, if you see a value equal "auto", will be used the default size of the plugin.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_drive_viewer_shortcode() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_drive','drive_viewer_shortcode');
			$this->moduleCommonFormDescription(sprintf(__('if you enable this option you can use the shortcode %s and enter the corresponding component directly in your article or page. Normally in the shortcodes can be specified the options for customizations.','sz-google'),'[sz-drive-viewer]'));
		}

		function callback_drive_viewer_s_width()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_drive','drive_viewer_s_width','medium','auto');
			$this->moduleCommonFormDescription(__('with this field you can set the width of the container iframe that will be used by defaul, when not specified as a parameter of the shortcode, if you see a value equal "auto", the default size will be 100% and will occupy the entire space of parent container.','sz-google'));
		}

		function callback_drive_viewer_s_height()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_drive','drive_viewer_s_height','medium','auto');
			$this->moduleCommonFormDescription(__('with this field you can set the height in pixels of the container iframe that will be used by defaul, when not specified as a parameter of the shortcode, if you see a value equal "auto", will be used the default size of the plugin.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_drive_viewer_s_t_position()
		{
			$values = array('top'=>__('top','sz-google'),'bottom'=>__('bottom','sz-google'));
			$this->moduleCommonFormSelect('sz_google_options_drive','drive_viewer_s_t_position',$values,'medium','');
			$this->moduleCommonFormDescription(__('this option indicates the position of the title, just in case this option is specified as "title". We can enter the title of the widget display before or soon after. To use a customized CSS class specified in the wrapper.','sz-google'));
		}

		function callback_drive_viewer_s_t_align()
		{
			$values = array(
				'none'  =>__('none'  ,'sz-google'),
				'left'  =>__('left'  ,'sz-google'),
				'center'=>__('center','sz-google'),
				'right' =>__('right' ,'sz-google'),
			);

			$this->moduleCommonFormSelect('sz_google_options_drive','drive_viewer_s_t_align',$values,'medium','');
			$this->moduleCommonFormDescription(__('this option indicates the type of alignment to apply to the title, is used only when the "title" attribute is specified, you can specify the following special values​​: "none", "left", "center" and "right". Use this value together with that of position.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_drive_viewer_widget() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_drive','drive_viewer_widget');
			$this->moduleCommonFormDescription(__('if you enable this option you will find the widget required in the administration menu of your widget and you can plug it into any sidebar defined in your theme. If you disable this option, remember not to leave the widget connected to existing sidebar.','sz-google'));
		}

		function callback_drive_viewer_w_width()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_drive','drive_viewer_w_width','medium','auto');
			$this->moduleCommonFormDescription(__('with this field you can set the width of the container iframe that will be used by defaul, when not specified as a parameter of the shortcode, if you see a value equal "auto", the default size will be 100% and will occupy the entire space of parent container.','sz-google'));
		}

		function callback_drive_viewer_w_height()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_drive','drive_viewer_w_height','medium','auto');
			$this->moduleCommonFormDescription(__('with this field you can set the height in pixels of the container iframe that will be used by defaul, when not specified as a parameter of the shortcode, if you see a value equal "auto", will be used the default size of the plugin.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_drive_viewer_w_t_position()
		{
			$values = array('top'=>__('top','sz-google'),'bottom'=>__('bottom','sz-google'));
			$this->moduleCommonFormSelect('sz_google_options_drive','drive_viewer_w_t_position',$values,'medium','');
			$this->moduleCommonFormDescription(__('this option indicates the position of the title, just in case this option is specified as "title". We can enter the title of the widget display before or soon after. To use a customized CSS class specified in the wrapper.','sz-google'));
		}

		function callback_drive_viewer_w_t_align()
		{
			$values = array(
				'none'  =>__('none'  ,'sz-google'),
				'left'  =>__('left'  ,'sz-google'),
				'center'=>__('center','sz-google'),
				'right' =>__('right' ,'sz-google'),
			);

			$this->moduleCommonFormSelect('sz_google_options_drive','drive_viewer_w_t_align',$values,'medium','');
			$this->moduleCommonFormDescription(__('this option indicates the type of alignment to apply to the title, is used only when the "title" attribute is specified, you can specify the following special values​​: "none", "left", "center" and "right". Use this value together with that of position.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_drive_savebutton_shortcode() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_drive','drive_savebutton_shortcode');
			$this->moduleCommonFormDescription(sprintf(__('if you enable this option you can use the shortcode %s and enter the corresponding component directly in your article or page. Normally in the shortcodes can be specified the options for customizations.','sz-google'),'[sz-drive-save]'));
		}

		function callback_drive_savebutton_widget() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_drive','drive_savebutton_widget');
			$this->moduleCommonFormDescription(__('if you enable this option you will find the widget required in the administration menu of your widget and you can plug it into any sidebar defined in your theme. If you disable this option, remember not to leave the widget connected to existing sidebar.','sz-google'));
		}
	}
}