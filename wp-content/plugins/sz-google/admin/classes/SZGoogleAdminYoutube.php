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

if (!class_exists('SZGoogleAdminYoutube'))
{
	class SZGoogleAdminYoutube extends SZGoogleAdmin
	{
		/**
		 * Creating the menu on the admin panel using values ​​
		 * such as configuration variables object (parent function)
		 */

		function moduleAddMenu()
		{
			// Definition of general values ​​for the creation of a menu associated 
			// with the module options. Example slug, page title and menu title

			$this->menuslug   = 'sz-google-admin-youtube.php';
			$this->pagetitle  = ucwords(__('google youtube','sz-google'));
			$this->menutitle  = ucwords(__('google youtube','sz-google'));

			// Definition of sections that need to be made ​​in HTML
			// sections must be passed as an array of name = > title

			$this->sectionstabs = array(
				'01' => array('anchor' => 'general'   ,'description' => __('general'   ,'sz-google')),
				'02' => array('anchor' => 'shortcodes','description' => __('shortcodes','sz-google')),
				'03' => array('anchor' => 'widgets'   ,'description' => __('widgets'   ,'sz-google')),
				'04' => array('anchor' => 'setup'     ,'description' => __('setup'     ,'sz-google')),
			);

			$this->sections = array(
				array('tab' => '01','section' => 'sz-google-admin-youtube-config.php'  ,'title' => ucwords(__('settings'         ,'sz-google'))),
				array('tab' => '01','section' => 'sz-google-admin-youtube-advanced.php','title' => ucwords(__('advanced settings','sz-google'))),
				array('tab' => '02','section' => 'sz-google-admin-youtube-enable-s.php','title' => ucwords(__('activation'       ,'sz-google'))),
				array('tab' => '03','section' => 'sz-google-admin-youtube-enable-w.php','title' => ucwords(__('activation'       ,'sz-google'))),
				array('tab' => '04','section' => 'sz-google-admin-youtube-display.php' ,'title' => ucwords(__('display'          ,'sz-google'))),
				array('tab' => '04','section' => 'sz-google-admin-youtube-margins.php' ,'title' => ucwords(__('margins'          ,'sz-google'))),
			);

			$this->sectionstitle   = $this->menutitle;
			$this->sectionsoptions = array('sz_google_options_youtube');

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
				'01' => array('section' => 'sz_google_youtube_config'  ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-youtube-config.php'),
				'02' => array('section' => 'sz_google_youtube_advanced','title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-youtube-advanced.php'),
				'03' => array('section' => 'sz_google_youtube_active_s','title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-youtube-enable-s.php'),
				'04' => array('section' => 'sz_google_youtube_active_w','title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-youtube-enable-w.php'),
				'05' => array('section' => 'sz_google_youtube_display' ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-youtube-display.php'),
				'06' => array('section' => 'sz_google_youtube_margins' ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-youtube-margins.php'),
			);

			// General definition array containing a list of fields
			// All fields are added to the previously defined sections

			$this->sectionsfields = array(
				'01' => array(array('field' => 'youtube_channel'           ,'title' => ucfirst(__('channel name or ID'        ,'sz-google')),'callback' => array($this,'callback_youtube_channel')),),
				'02' => array(array('field' => 'youtube_force_ssl'         ,'title' => ucfirst(__('force SSL'                 ,'sz-google')),'callback' => array($this,'callback_youtube_force_ssl')),
				              array('field' => 'youtube_fullscreen'        ,'title' => ucfirst(__('enable fullscreen'         ,'sz-google')),'callback' => array($this,'callback_youtube_fullscreen')),
				              array('field' => 'youtube_disablekeyboard'   ,'title' => ucfirst(__('disable keyboard'          ,'sz-google')),'callback' => array($this,'callback_youtube_disablekeyboard')),
				              array('field' => 'youtube_disableiframe'     ,'title' => ucfirst(__('disable IFRAME and use API','sz-google')),'callback' => array($this,'callback_youtube_disableiframe')),
				              array('field' => 'youtube_analytics'         ,'title' => ucfirst(__('google analytics'          ,'sz-google')),'callback' => array($this,'callback_youtube_analytics')),
				              array('field' => 'youtube_delayed'           ,'title' => ucfirst(__('delayed loading'           ,'sz-google')),'callback' => array($this,'callback_youtube_delayed')),
				              array('field' => 'youtube_disablerelated'    ,'title' => ucfirst(__('disable related'           ,'sz-google')),'callback' => array($this,'callback_youtube_disablerelated')),),
				'03' => array(array('field' => 'youtube_shortcode'         ,'title' => ucfirst(__('youtube video'             ,'sz-google')),'callback' => array($this,'callback_youtube_shortcode')),
				              array('field' => 'youtube_shortcode_badge'   ,'title' => ucfirst(__('youtube badge'             ,'sz-google')),'callback' => array($this,'callback_youtube_shortcode_badge')),
				              array('field' => 'youtube_shortcode_button'  ,'title' => ucfirst(__('youtube button'            ,'sz-google')),'callback' => array($this,'callback_youtube_shortcode_button')),
				              array('field' => 'youtube_shortcode_link'    ,'title' => ucfirst(__('youtube link'              ,'sz-google')),'callback' => array($this,'callback_youtube_shortcode_link')),
				              array('field' => 'youtube_shortcode_playlist','title' => ucfirst(__('youtube playlist'          ,'sz-google')),'callback' => array($this,'callback_youtube_shortcode_playlist')),),
				'04' => array(array('field' => 'youtube_widget'            ,'title' => ucfirst(__('youtube video'             ,'sz-google')),'callback' => array($this,'callback_youtube_widget')),
				              array('field' => 'youtube_widget_badge'      ,'title' => ucfirst(__('youtube badge'             ,'sz-google')),'callback' => array($this,'callback_youtube_widget_badge')),				              
				              array('field' => 'youtube_widget_button'     ,'title' => ucfirst(__('youtube button'            ,'sz-google')),'callback' => array($this,'callback_youtube_widget_button')),
				              array('field' => 'youtube_widget_link'       ,'title' => ucfirst(__('youtube link'              ,'sz-google')),'callback' => array($this,'callback_youtube_widget_link')),
				              array('field' => 'youtube_widget_playlist'   ,'title' => ucfirst(__('youtube playlist'          ,'sz-google')),'callback' => array($this,'callback_youtube_widget_playlist')),),
				'05' => array(array('field' => 'youtube_responsive'        ,'title' => ucfirst(__('responsive mode'           ,'sz-google')),'callback' => array($this,'callback_youtube_responsive')),
				              array('field' => 'youtube_width'             ,'title' => ucfirst(__('default width'             ,'sz-google')),'callback' => array($this,'callback_youtube_width')),
				              array('field' => 'youtube_height'            ,'title' => ucfirst(__('default height'            ,'sz-google')),'callback' => array($this,'callback_youtube_height')),
				              array('field' => 'youtube_autoplay'          ,'title' => ucfirst(__('video autoplay'            ,'sz-google')),'callback' => array($this,'callback_youtube_autoplay')),
				              array('field' => 'youtube_loop'              ,'title' => ucfirst(__('video loop'                ,'sz-google')),'callback' => array($this,'callback_youtube_loop')),
				              array('field' => 'youtube_theme'             ,'title' => ucfirst(__('theme'                     ,'sz-google')),'callback' => array($this,'callback_youtube_theme')),
				              array('field' => 'youtube_cover'             ,'title' => ucfirst(__('cover'                     ,'sz-google')),'callback' => array($this,'callback_youtube_cover')),
				              array('field' => 'youtube_schemaorg'         ,'title' => ucfirst(__('schema.org'                ,'sz-google')),'callback' => array($this,'callback_youtube_schemaorg')),),
				'06' => array(array('field' => 'youtube_margin_top'        ,'title' => ucfirst(__('margin top'                ,'sz-google')),'callback' => array($this,'callback_youtube_margin_top')),
				              array('field' => 'youtube_margin_right'      ,'title' => ucfirst(__('margin right'              ,'sz-google')),'callback' => array($this,'callback_youtube_margin_right')),
				              array('field' => 'youtube_margin_bottom'     ,'title' => ucfirst(__('margin bottom'             ,'sz-google')),'callback' => array($this,'callback_youtube_margin_bottom')),
				              array('field' => 'youtube_margin_left'       ,'title' => ucfirst(__('margin left'               ,'sz-google')),'callback' => array($this,'callback_youtube_margin_left')),
				              array('field' => 'youtube_margin_unit'       ,'title' => ucfirst(__('margin unit'               ,'sz-google')),'callback' => array($this,'callback_youtube_margin_unit')),),
			);

			// Calling up the function of the parent class to process the 
			// variables that contain the values ​​of configuration section

			parent::moduleAddFields();
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_youtube_channel()
		{
			$this->moduleCommonFormText('sz_google_options_youtube','youtube_channel','large',__('insert your channel name or ID','sz-google'));
			$this->moduleCommonFormDescription(__('enter in this field the default name of your youtube channel. You can change the channel name using the shortcode or functions. if you do not specify anything the default channel will be "wpitalyplus". The channel\'s name is located in the your string URL.','sz-google'));
		}

		function callback_youtube_widget()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_youtube','youtube_widget');
			$this->moduleCommonFormDescription(__('if you enable this option you will find the widget required in the administration menu of your widget and you can plug it into any sidebar defined in your theme. If you disable this option, remember not to leave the widget connected to existing sidebar.','sz-google'));
		}

		function callback_youtube_widget_badge()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_youtube','youtube_widget_badge');
			$this->moduleCommonFormDescription(__('if you enable this option you will find the widget required in the administration menu of your widget and you can plug it into any sidebar defined in your theme. If you disable this option, remember not to leave the widget connected to existing sidebar.','sz-google'));
		}

		function callback_youtube_widget_button()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_youtube','youtube_widget_button');
			$this->moduleCommonFormDescription(__('if you enable this option you will find the widget required in the administration menu of your widget and you can plug it into any sidebar defined in your theme. If you disable this option, remember not to leave the widget connected to existing sidebar.','sz-google'));
		}

		function callback_youtube_widget_link()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_youtube','youtube_widget_link');
			$this->moduleCommonFormDescription(__('if you enable this option you will find the widget required in the administration menu of your widget and you can plug it into any sidebar defined in your theme. If you disable this option, remember not to leave the widget connected to existing sidebar.','sz-google'));
		}

		function callback_youtube_widget_playlist()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_youtube','youtube_widget_playlist');
			$this->moduleCommonFormDescription(__('if you enable this option you will find the widget required in the administration menu of your widget and you can plug it into any sidebar defined in your theme. If you disable this option, remember not to leave the widget connected to existing sidebar.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_youtube_shortcode() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_youtube','youtube_shortcode');
			$this->moduleCommonFormDescription(sprintf(__('if you enable this option you can use the shortcode %s and enter the corresponding component directly in your article or page. Normally in the shortcodes can be specified the options for customizations.','sz-google'),'[sz-ytvideo]'));
		}

		function callback_youtube_shortcode_badge() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_youtube','youtube_shortcode_badge');
			$this->moduleCommonFormDescription(sprintf(__('if you enable this option you can use the shortcode %s and enter the corresponding component directly in your article or page. Normally in the shortcodes can be specified the options for customizations.','sz-google'),'[sz-ytbadge]'));
		}

		function callback_youtube_shortcode_button() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_youtube','youtube_shortcode_button');
			$this->moduleCommonFormDescription(sprintf(__('if you enable this option you can use the shortcode %s and enter the corresponding component directly in your article or page. Normally in the shortcodes can be specified the options for customizations.','sz-google'),'[sz-ytbutton]'));
		}

		function callback_youtube_shortcode_link() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_youtube','youtube_shortcode_link');
			$this->moduleCommonFormDescription(sprintf(__('if you enable this option you can use the shortcode %s and enter the corresponding component directly in your article or page. Normally in the shortcodes can be specified the options for customizations.','sz-google'),'[sz-ytlink]'));
		}

		function callback_youtube_shortcode_playlist() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_youtube','youtube_shortcode_playlist');
			$this->moduleCommonFormDescription(sprintf(__('if you enable this option you can use the shortcode %s and enter the corresponding component directly in your article or page. Normally in the shortcodes can be specified the options for customizations.','sz-google'),'[sz-ytplaylist]'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_youtube_responsive()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_youtube','youtube_responsive');
			$this->moduleCommonFormDescription(__('activating this value, the size of the video will be managed with the technique of responsive design, so the size is automatically adjusted to the size of the window, for more information see the documentation on Wikipedia Responsive Web Design.','sz-google'));
		}

		function callback_youtube_width()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_youtube','youtube_width','medium','auto');
			$this->moduleCommonFormDescription(__('enter the default size of the video, if you do not specify a value in this field, the default size will be 600px. If you specified a value of "0" or is activated the responsive mode will be used the special value 100% which will occupy the entire space of the container.','sz-google'));
		}

		function callback_youtube_height()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_youtube','youtube_height','medium','auto');
			$this->moduleCommonFormDescription(__('Enter the default size of the video, if you do not specify a value in this field, the default size will be 400px. In the responsive version this value will be ignored, in fact the height will change automatically according to the width of the parent container.','sz-google'));
		}

		function callback_youtube_autoplay()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_youtube','youtube_autoplay');
			$this->moduleCommonFormDescription(__('enabling this option, the video will start automatically inserted when viewing the page without waiting for the user to press the play button. This behavior you can manage it even with the option of shortcode called "autoplay".','sz-google'));
		}

		function callback_youtube_loop()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_youtube','youtube_loop');
			$this->moduleCommonFormDescription(__('this option allows you to reinitiate the video after it was finished. The same function, you can obtain it using the special value [loop] in the shortcode without changing the default behavior. See official documentation Developer Youtube.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_youtube_theme()
		{
			$values = array(
				'dark'  => __('dark','sz-google'),
				'light' => __('light','sz-google'),
			);

			$this->moduleCommonFormSelect('sz_google_options_youtube','youtube_theme',$values,'medium','');
			$this->moduleCommonFormDescription(__('in this field specify the default theme to apply the player. At this time you can choose between "light" and "dark". To see if they added some additional theme controls the official documentation Developer Youtube.','sz-google'));
		}

		function callback_youtube_cover()
		{
			$values = array(
				'local'   => __('local','sz-google'),
				'youtube' => __('youtube','sz-google'),
			);

			$this->moduleCommonFormSelect('sz_google_options_youtube','youtube_cover',$values,'medium','');
			$this->moduleCommonFormDescription(__('in this field you must specify the type of cover for your video clip when you do not specify a custom value. If you specify "local" will be used to cover stored in the plugin, if you use the value "youtube" will be used to cover defaults on youtube.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_youtube_schemaorg()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_youtube','youtube_schemaorg');
			$this->moduleCommonFormDescription(__('enabling this option will be enabled meta commands relating to the resources of schema.org video. The values ​​of "meta" must be specified on shortcode or PHP function. For more information read the official documentation.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_youtube_margin_top()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_youtube','youtube_margin_top','medium',0);
			$this->moduleCommonFormDescription(__('enter the value of the margin to be applied to the container that will contain the iframe youtube video to display. If you do not specify any value will be used the value 0. This parameter indicates the top margin from the previous text.','sz-google'));
		}

		function callback_youtube_margin_right()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_youtube','youtube_margin_right','medium','auto');
			$this->moduleCommonFormDescription(__('enter the value of the margin to be applied to the container that will contain the iframe youtube video to display. If you do not specify any value for this field will be used the special value "auto". If you use responsive mode this value will be ignored.','sz-google'));
		}

		function callback_youtube_margin_bottom()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_youtube','youtube_margin_bottom','medium',0);
			$this->moduleCommonFormDescription(__('enter the value of the margin to be applied to the container that will contain the iframe youtube video to display. If you do not specify any value will be used the value 0. This parameter indicates the bottom margin from the text.','sz-google'));
		}

		function callback_youtube_margin_left()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_youtube','youtube_margin_left','medium','auto');
			$this->moduleCommonFormDescription(__('enter the value of the margin to be applied to the container that will contain the iframe youtube video to display. If you do not specify any value for this field will be used the special value "auto". If you use responsive mode this value will be ignored.','sz-google'));
		}

		function callback_youtube_margin_unit()
		{
			$values = array(
				'em' => __('em','sz-google'),
				'px' => __('px','sz-google'),
			);

			$this->moduleCommonFormSelect('sz_google_options_youtube','youtube_margin_unit',$values,'medium','');
			$this->moduleCommonFormDescription(__('this field is used to specify the unit of measure that must be applied to numeric values ​​that relate to the margins of the video container, the values ​​that can be specified are em = relative size or px = pixel size.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_youtube_force_ssl()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_youtube','youtube_force_ssl');
			$this->moduleCommonFormDescription(__('if this function is enabled the embed code to generate the video link is forced with the SSL protocol even if the URL string is specified otherwise. Enabling recommended if the web pages of the site are set by default with SSL.','sz-google'));
		}

		function callback_youtube_fullscreen()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_youtube','youtube_fullscreen');
			$this->moduleCommonFormDescription(__('enable this option to enter in the control bar of the video player icon that allows you to view fullscreen. More information can be found in the official documentation. This behavior you can manage it even with the option of shortcode called "fullscreen".','sz-google'));
		}

		function callback_youtube_disablekeyboard()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_youtube','youtube_disablekeyboard');
			$this->moduleCommonFormDescription(__('will disable the player keyboard controls. Keyboard controls are as follows: Spacebar: Play/Pause - Arrow Left: Jump back 10% in the current video - Arrow Right: Jump ahead 10% in the current video - Arrow Up: Volume up - Arrow Down: Volume Down.','sz-google'));
		}

		function callback_youtube_disableiframe()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_youtube','youtube_disableiframe');
			$this->moduleCommonFormDescription(__('normally to insert a youtube video on a webpage uses the iframe technique, use this parameter to change this way and use the JavaScript API provided by google. If you activate the option of google analytics for youtube this value will be ignored.','sz-google'));
		}

		function callback_youtube_analytics() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_youtube','youtube_analytics');
			$this->moduleCommonFormDescription(__('if you enable this option, you can check the requirements and the translation statistics directly to your google analytics account. Remember that to run this option you must specify the code assigned to your profile analytics.','sz-google'));
		}

		function callback_youtube_delayed() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_youtube','youtube_delayed');
			$this->moduleCommonFormDescription(__('by default the iframe code is loaded immediately, there may be cases where this can be poorly performing, or even when we want to customize the cover image, it would be better to load the code after the user executes the play button.','sz-google'));
		}

		function callback_youtube_disablerelated() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_youtube','youtube_disablerelated');
			$this->moduleCommonFormDescription(__('enabling this option disables the related videos that are presented to the player at the end of the video. activating this option in the final video should be made the cover of the video. Read the official documentation to Developer Guide.','sz-google'));
		}
	}
}