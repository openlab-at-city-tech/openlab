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

if (!class_exists('SZGoogleAdminPlus'))
{
	class SZGoogleAdminPlus extends SZGoogleAdmin
	{
		/**
		 * Definition the constructor function, which is called
		 * at the time of the creation of an instance of this class
		 */

		function __construct()
		{
			// Add the filter of user contacts to change array
			// containing the default fields of wordpress on profile

			add_filter('user_contactmethods',array($this,'AddContactMethods'),90,1);
			
			// Calling the function of the parent class to process
			// variables containing the configuration values ​​section

			parent::__construct();
 		}

		/**
		 * Creating the menu on the admin panel using values ​​
		 * such as configuration variables object (parent function)
		 */

		function moduleAddMenu()
		{
			// Definition of general values ​​for the creation of a menu associated 
			// with the module options. Example slug, page title and menu title

			$this->menuslug   = 'sz-google-admin-plus.php';
			$this->pagetitle  = ucwords(__('google+','sz-google'));
			$this->menutitle  = ucwords(__('google+','sz-google'));

			// Definition of sections that need to be made ​​in HTML
			// sections must be passed as an array of name = > title

			$this->sectionstabs = array(
				'01' => array('anchor' => 'general'   ,'description' => __('general'   ,'sz-google')),
				'02' => array('anchor' => 'shortcodes','description' => __('shortcodes','sz-google')),
				'03' => array('anchor' => 'widgets'   ,'description' => __('widgets'   ,'sz-google')),
				'04' => array('anchor' => 'comments'  ,'description' => __('comments'  ,'sz-google')),
				'05' => array('anchor' => 'author'    ,'description' => __('author'    ,'sz-google')),
			);

			$this->sections = array(
				array('tab' => '01','section' => 'sz-google-admin-plus.php'          ,'title' => ucwords(__('identification','sz-google'))),
				array('tab' => '01','section' => 'sz-google-admin-plus-language.php' ,'title' => ucwords(__('language'      ,'sz-google'))),
				array('tab' => '01','section' => 'sz-google-admin-plus-redirect.php' ,'title' => ucwords(__('custom URL'    ,'sz-google'))),
				array('tab' => '01','section' => 'sz-google-admin-plus-system.php'   ,'title' => ucwords(__('system'        ,'sz-google'))),
				array('tab' => '02','section' => 'sz-google-admin-plus-s-badges.php' ,'title' => ucwords(__('badges'        ,'sz-google'))),
				array('tab' => '02','section' => 'sz-google-admin-plus-s-buttons.php','title' => ucwords(__('buttons'       ,'sz-google'))), 
				array('tab' => '02','section' => 'sz-google-admin-plus-s-posts.php'  ,'title' => ucwords(__('posts'         ,'sz-google'))), 
				array('tab' => '03','section' => 'sz-google-admin-plus-w-badges.php' ,'title' => ucwords(__('badges'        ,'sz-google'))),
				array('tab' => '03','section' => 'sz-google-admin-plus-w-buttons.php','title' => ucwords(__('buttons'       ,'sz-google'))), 
				array('tab' => '03','section' => 'sz-google-admin-plus-w-posts.php'  ,'title' => ucwords(__('posts'         ,'sz-google'))), 
				array('tab' => '04','section' => 'sz-google-admin-plus-comments.php' ,'title' => ucwords(__('comments'      ,'sz-google'))), 
				array('tab' => '05','section' => 'sz-google-admin-plus-head.php'     ,'title' => ucwords(__('section HEAD'  ,'sz-google'))),
				array('tab' => '05','section' => 'sz-google-admin-plus-contacts.php' ,'title' => ucwords(__('profile fields','sz-google'))),
				array('tab' => '05','section' => 'sz-google-admin-plus-author.php'   ,'title' => ucwords(__('author badge'  ,'sz-google'))),
			);

			$this->sectionstitle   = $this->menutitle;
			$this->sectionsoptions = array('sz_google_options_plus');

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
				'01' => array('section' => 'sz_google_plus_section'  ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-plus.php'),
				'02' => array('section' => 'sz_google_plus_language' ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-plus-language.php'),
				'03' => array('section' => 'sz_google_plus_redirect' ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-plus-redirect.php'),
				'04' => array('section' => 'sz_google_plus_system'   ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-plus-system.php'),
				'05' => array('section' => 'sz_google_plus_s_badges' ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-plus-s-badges.php'),
				'06' => array('section' => 'sz_google_plus_s_buttons','title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-plus-s-buttons.php'),
				'07' => array('section' => 'sz_google_plus_s_posts'  ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-plus-s-posts.php'),
				'08' => array('section' => 'sz_google_plus_w_badges' ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-plus-w-badges.php'),
				'09' => array('section' => 'sz_google_plus_w_buttons','title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-plus-w-buttons.php'),
				'10' => array('section' => 'sz_google_plus_w_posts'  ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-plus-w-posts.php'),
				'11' => array('section' => 'sz_google_plus_comments' ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-plus-comments.php'),
				'12' => array('section' => 'sz_google_plus_head'     ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-plus-head.php'),
				'13' => array('section' => 'sz_google_plus_contacts' ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-plus-contacts.php'),
				'14' => array('section' => 'sz_google_plus_author'   ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-plus-author.php'),
			);

			// General definition array containing a list of fields
			// All fields are added to the previously defined sections

			$this->sectionsfields = array(
				'01' => array(array('field' => 'plus_profile'                     ,'title' => ucwords(__('google+ profile'        ,'sz-google')),'callback' => array($this,'callback_plus_profile')),
				              array('field' => 'plus_page'                        ,'title' => ucwords(__('google+ page'           ,'sz-google')),'callback' => array($this,'callback_plus_page')),
				              array('field' => 'plus_community'                   ,'title' => ucwords(__('google+ community'      ,'sz-google')),'callback' => array($this,'callback_plus_community')),),
				'02' => array(array('field' => 'plus_language'                    ,'title' => ucfirst(__('select language'        ,'sz-google')),'callback' => array($this,'callback_plus_language')),),
				'03' => array(array('field' => 'plus_redirect_sign'               ,'title' => ucfirst(__('redirect /+'            ,'sz-google')),'callback' => array($this,'callback_plus_redirect_sign')),
				              array('field' => 'plus_redirect_sign_url'           ,'title' => ucfirst(__('redirect /+ URL'        ,'sz-google')),'callback' => array($this,'callback_plus_redirect_sign_url')),
				              array('field' => 'plus_redirect_plus'               ,'title' => ucfirst(__('redirect /plus'         ,'sz-google')),'callback' => array($this,'callback_plus_redirect_plus')),
				              array('field' => 'plus_redirect_plus_url'           ,'title' => ucfirst(__('redirect /plus URL'     ,'sz-google')),'callback' => array($this,'callback_plus_redirect_plus_url')),
				              array('field' => 'plus_redirect_curl'               ,'title' => ucfirst(__('redirect URL'           ,'sz-google')),'callback' => array($this,'callback_plus_redirect_curl')),
				              array('field' => 'plus_redirect_curl_source'        ,'title' => ucfirst(__('redirect URL source'    ,'sz-google')),'callback' => array($this,'callback_plus_redirect_curl_source')),
				              array('field' => 'plus_redirect_curl_target'        ,'title' => ucfirst(__('redirect URL target'    ,'sz-google')),'callback' => array($this,'callback_plus_redirect_curl_target')),),
				'04' => array(array('field' => 'plus_enable_recommendations'      ,'title' => ucwords(__('recommendations mobile' ,'sz-google')),'callback' => array($this,'callback_plus_enable_recommendations')),
				              array('field' => 'plus_system_javascript'           ,'title' => ucwords(__('disable file javascript','sz-google')),'callback' => array($this,'callback_plus_system_javascript')),),
				'05' => array(array('field' => 'plus_shortcode_pr_enable'         ,'title' => ucwords(__('google+ profile'        ,'sz-google')),'callback' => array($this,'callback_plus_shortcode_profile')),
				              array('field' => 'plus_shortcode_pa_enable'         ,'title' => ucwords(__('google+ page'           ,'sz-google')),'callback' => array($this,'callback_plus_shortcode_page')),
				              array('field' => 'plus_shortcode_co_enable'         ,'title' => ucwords(__('google+ community'      ,'sz-google')),'callback' => array($this,'callback_plus_shortcode_community')),
				              array('field' => 'plus_shortcode_fl_enable'         ,'title' => ucwords(__('google+ followers'      ,'sz-google')),'callback' => array($this,'callback_plus_shortcode_followers')),
				              array('field' => 'plus_shortcode_size_portrait'     ,'title' => ucwords(__('width portrait'         ,'sz-google')),'callback' => array($this,'callback_plus_shortcode_size_portrait')),
				              array('field' => 'plus_shortcode_size_landscape'    ,'title' => ucwords(__('width landscape'        ,'sz-google')),'callback' => array($this,'callback_plus_shortcode_size_landscape')),),
				'06' => array(array('field' => 'plus_button_enable_plusone'       ,'title' => ucwords(__('google+ plusone'        ,'sz-google')),'callback' => array($this,'callback_plus_button_plusone')),
				              array('field' => 'plus_button_enable_sharing'       ,'title' => ucwords(__('google+ sharing'        ,'sz-google')),'callback' => array($this,'callback_plus_button_sharing')),
				              array('field' => 'plus_button_enable_follow'        ,'title' => ucwords(__('google+ follow'         ,'sz-google')),'callback' => array($this,'callback_plus_button_follow')),),
				'07' => array(array('field' => 'plus_post_enable_shortcode'       ,'title' => ucwords(__('google+ post'           ,'sz-google')),'callback' => array($this,'callback_plus_post_shortcode')),),
				'08' => array(array('field' => 'plus_widget_pr_enable'            ,'title' => ucwords(__('google+ profile'        ,'sz-google')),'callback' => array($this,'callback_plus_widget_profile')),
				              array('field' => 'plus_widget_pa_enable'            ,'title' => ucwords(__('google+ page'           ,'sz-google')),'callback' => array($this,'callback_plus_widget_page')),
				              array('field' => 'plus_widget_co_enable'            ,'title' => ucwords(__('google+ community'      ,'sz-google')),'callback' => array($this,'callback_plus_widget_community')),
				              array('field' => 'plus_widget_fl_enable'            ,'title' => ucwords(__('google+ followers'      ,'sz-google')),'callback' => array($this,'callback_plus_widget_followers')),
				              array('field' => 'plus_widget_size_portrait'        ,'title' => ucwords(__('width portrait'         ,'sz-google')),'callback' => array($this,'callback_plus_widget_size_portrait')),
				              array('field' => 'plus_widget_size_landscape'       ,'title' => ucwords(__('width landscape'        ,'sz-google')),'callback' => array($this,'callback_plus_widget_size_landscape')),),
				'09' => array(array('field' => 'plus_button_enable_widget_plusone','title' => ucwords(__('google+ plusone'        ,'sz-google')),'callback' => array($this,'callback_plus_widget_button_plusone')),
				              array('field' => 'plus_button_enable_widget_sharing','title' => ucwords(__('google+ sharing'        ,'sz-google')),'callback' => array($this,'callback_plus_widget_button_sharing')),
				              array('field' => 'plus_button_enable_widget_follow' ,'title' => ucwords(__('google+ follow'         ,'sz-google')),'callback' => array($this,'callback_plus_widget_button_follow')),),
				'10' => array(array('field' => 'plus_post_enable_widget'          ,'title' => ucwords(__('google+ post'           ,'sz-google')),'callback' => array($this,'callback_plus_post_widget')),),
				'11' => array(array('field' => 'plus_comments_gp_enable'          ,'title' => ucwords(__('g+ comments'            ,'sz-google')),'callback' => array($this,'callback_plus_comments_gp')),
				              array('field' => 'plus_comments_wp_enable'          ,'title' => ucwords(__('WP comments'            ,'sz-google')),'callback' => array($this,'callback_plus_comments_wp')),
				              array('field' => 'plus_comments_ac_enable'          ,'title' => ucwords(__('after content'          ,'sz-google')),'callback' => array($this,'callback_plus_comments_ac')),
				              array('field' => 'plus_comments_aw_enable'          ,'title' => ucwords(__('after WP system'        ,'sz-google')),'callback' => array($this,'callback_plus_comments_aw')),
				              array('field' => 'plus_comments_wd_enable'          ,'title' => ucwords(__('widget'                 ,'sz-google')),'callback' => array($this,'callback_plus_comments_wd')),
				              array('field' => 'plus_comments_sh_enable'          ,'title' => ucwords(__('shortcode'              ,'sz-google')),'callback' => array($this,'callback_plus_comments_sh')),
				              array('field' => 'plus_comments_dt_enable'          ,'title' => ucwords(__('date switch'            ,'sz-google')),'callback' => array($this,'callback_plus_comments_dt')),
				              array('field' => 'plus_comments_fixed_size'         ,'title' => ucwords(__('fixed size'             ,'sz-google')),'callback' => array($this,'callback_plus_comments_fixed_size')),
				              array('field' => 'plus_comments_title'              ,'title' => ucwords(__('title'                  ,'sz-google')),'callback' => array($this,'callback_plus_comments_title')),
				              array('field' => 'plus_comments_css_class_1'        ,'title' => ucwords(__('CSS class 1'            ,'sz-google')),'callback' => array($this,'callback_plus_comments_css_class_1')),
				              array('field' => 'plus_comments_css_class_2'        ,'title' => ucwords(__('CSS class 2'            ,'sz-google')),'callback' => array($this,'callback_plus_comments_css_class_2')),),
				'12' => array(array('field' => 'plus_enable_author'               ,'title' => ucwords(__('HEAD Author'            ,'sz-google')),'callback' => array($this,'callback_plus_enable_author')),
				              array('field' => 'plus_enable_publisher'            ,'title' => ucwords(__('HEAD Publisher'         ,'sz-google')),'callback' => array($this,'callback_plus_enable_publisher')),),
				'13' => array(array('field' => 'plus_usercontact_page'            ,'title' => ucwords(__('google+ page'           ,'sz-google')),'callback' => array($this,'callback_plus_usercontact_page')),
				              array('field' => 'plus_usercontact_community'       ,'title' => ucwords(__('google+ community'      ,'sz-google')),'callback' => array($this,'callback_plus_usercontact_community')),
				              array('field' => 'plus_usercontact_bestpost'        ,'title' => ucwords(__('google+ best post'      ,'sz-google')),'callback' => array($this,'callback_plus_usercontact_bestpost')),),
				'14' => array(array('field' => 'plus_author_badge'                ,'title' => ucwords(__('author badge'           ,'sz-google')),'callback' => array($this,'callback_plus_author_badge')),),
			);

			// Calling up the function of the parent class to process the 
			// variables that contain the values ​​of configuration section

			parent::moduleAddFields();
		}

		/**
		 * Wrapper function to get the configuration options
		 * included in the admin panel that covers the module
		 */

		function getOptions() {
			if (!$object = SZGoogleModule::getObject('SZGoogleModulePlus')) return false;
				else return $object->getOptions();
		}

		/**
		 * Function that reads the standard array of contacts and add those
		 * in the plugin after finding the standard called "googleplus"
		 */

		function AddContactMethods($usercontacts)
		{
			// Creating new array to fill it with the data I find 
			// in the original array more than those in the plugin

			$newcontacts = array();

			// I read sequentially numbered original of the array variable, 
			// How do I find the standard G+ add custom ones below

			foreach ($usercontacts as $key => $value) {
				$newcontacts[$key] = $value;
				if ($key == 'googleplus') $newcontacts = $this->AddContactMethodsPlus($newcontacts);
			}	

			// Calling the function for a second time as might exist
			// the case that some plugins have removed the standard range of G+

			return $this->AddContactMethodsPlus($newcontacts);
		}

		/**
		 * Function that reads the standard array of contacts and add those
		 * in the plugin after finding the standard called "googleplus"
		 */

		function AddContactMethodsPlus($usercontacts) 
		{
			if (!isset($usercontacts['googleplus'])) $usercontacts['googleplus'] = __('Google+','sz-google');

			if ($options = $this->getOptions()) {
				if (!isset($usercontacts['googlepluspage'])           && $options['plus_usercontact_page']      == '1') $usercontacts['googlepluspage']           = SZGOOGLE_UWORDS(__('google+ page'          ,'sz-google'));
				if (!isset($usercontacts['googlepluscommunity'])      && $options['plus_usercontact_community'] == '1') $usercontacts['googlepluscommunity']      = SZGOOGLE_UWORDS(__('google+ community'     ,'sz-google'));
				if (!isset($usercontacts['googleplusbestpost'])       && $options['plus_usercontact_bestpost']  == '1') $usercontacts['googleplusbestpost']       = SZGOOGLE_UWORDS(__('google+ best post'     ,'sz-google'));
				if (!isset($usercontacts['googleplusprofilephoto'])   && $options['plus_author_badge']          == '1') $usercontacts['googleplusprofilephoto']   = SZGOOGLE_UWORDS(__('google+ author photo'  ,'sz-google'));
				if (!isset($usercontacts['googleplusprofilecover'])   && $options['plus_author_badge']          == '1') $usercontacts['googleplusprofilecover']   = SZGOOGLE_UWORDS(__('google+ author cover'  ,'sz-google'));
				if (!isset($usercontacts['googleplusprofiletagline']) && $options['plus_author_badge']          == '1') $usercontacts['googleplusprofiletagline'] = SZGOOGLE_UWORDS(__('google+ author tagline','sz-google'));
			}

			// Return new array to be added to the original
			// with the new fields that affect google plus

			return $usercontacts;
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_plus_profile()
		{
			$this->moduleCommonFormText('sz_google_options_plus','plus_profile','medium',__('insert ID your profile','sz-google'));
			$this->moduleCommonFormDescription(__('enter the code that identifies the profile on google+, get to know the code of a profile just look at the profile link and copy the 21 digit number located on the URL string. For example a profile ID is 106189723444098348646.','sz-google'));
		}

		function callback_plus_page()
		{
			$this->moduleCommonFormText('sz_google_options_plus','plus_page','medium',__('insert ID your page','sz-google'));
			$this->moduleCommonFormDescription(__('enter the code that identifies the page on google+, get to know the code of a profile just look at the page link and copy the 21 digit number located on the URL string. For example a page ID is 117259631219963935481.','sz-google'));
		}

		function callback_plus_community()
		{
			$this->moduleCommonFormText('sz_google_options_plus','plus_community','medium',__('insert ID your community','sz-google'));
			$this->moduleCommonFormDescription(__('enter the code that identifies the community, get to know the code of a community just look at the link and copy the 21 digit number located on the URL string. For example a community ID is 109254048492234113886.','sz-google'));
		}

		function callback_plus_language()
		{
			$values = SZGoogleCommon::getLanguages();
			$this->moduleCommonFormSelect('sz_google_options_plus','plus_language',$values,'medium','');
			$this->moduleCommonFormDescription(__('specify the language code associated with your website, if you do not specify any value will be called the get_bloginfo(\'language\') and set the same language related to the theme of wordpress.','sz-google'));
		}

		function callback_plus_post_widget()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_post_enable_widget');
			$this->moduleCommonFormDescription(__('if you need to insert the component for embedded post to google+ in a sidebar you can activate this option and use the new widget that you will find in your admin panel, you specify the size or the way you use responsive design for automatic resize.','sz-google'));
		}

		function callback_plus_post_shortcode()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_post_enable_shortcode');
			$this->moduleCommonFormDescription(__('enabling this option will allow you to use the shortcode [sz-gplus-post] that will allow you to insert a box for embedded post to google plus in any part of your post or page standard wordpress.','sz-google'));
		}

		function callback_plus_enable_recommendations()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_enable_recommendations');
			$this->moduleCommonFormDescription(__('google+ content recommendations combines search with social data to greet mobile visitors with additional relevant recommended content on your site. You will add markup to link your web page to your Google+ Page and to load a JavaScript file.','sz-google'));
		}

		function callback_plus_system_javascript()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_system_javascript');
			$this->moduleCommonFormDescription(__('if you use some plugin that performs functions similar to sz-google for wordpress is possible that we might create a conflict retrieving files javascript google, enabling this option will be disabled loadings code javascript from our plugin.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_plus_widget_profile()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_widget_pr_enable');
			$this->moduleCommonFormDescription(__('enabling this option will be included in the admin panel a new widget that will allow the insertion of a badge for the user profiles present on google+. If you want to see the graphic result of badges provided by google read the official documentation.','sz-google'));
		}

		function callback_plus_widget_page()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_widget_pa_enable');
			$this->moduleCommonFormDescription(__('enabling this option will be included in the admin panel a new widget that will allow the insertion of a badge for the pages present on google+. If you want to see the graphic result of badges provided by google read the official documentation.','sz-google'));
		}

		function callback_plus_widget_community()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_widget_co_enable');
			$this->moduleCommonFormDescription(__('enabling this option will be included in the admin panel a new widget that will allow the insertion of a badge for the community present on google+. If you want to see the graphic result of badges provided by google read the official documentation.','sz-google'));
		}

		function callback_plus_widget_followers()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_widget_fl_enable');
			$this->moduleCommonFormDescription(__('enabling this option will be included in the admin panel a new widget that will allow the insertion of a badge for the followers present on google+. If you want to see the graphic result of badges provided by google read the official documentation.','sz-google'));
		}

		function callback_plus_widget_size_portrait()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_plus','plus_widget_size_portrait','medium','180');
			$this->moduleCommonFormDescription(__('this option is used to set a default width for use in widget when no size is set manually and is selected as the display mode portrait. If you do not specify a value for this field will be used the standard width of 180px and height will be calculated.','sz-google'));
		}

		function callback_plus_widget_size_landscape()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_plus','plus_widget_size_landscape','medium','275');
			$this->moduleCommonFormDescription(__('this option is used to set a default width for use in widget when no size is set manually and is selected as the display mode landscape. If you do not specify a value for this field will be used the standard width of 275px and height will be automatically.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_plus_shortcode_profile()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_shortcode_pr_enable');
			$this->moduleCommonFormDescription(__('enabling this option will be included in the admin panel a new shortcode that will allow the insertion of a badge for the user profiles present on google+. If you want to see the graphic result of badges read the official documentation.','sz-google'));
		}

		function callback_plus_shortcode_page()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_shortcode_pa_enable');
			$this->moduleCommonFormDescription(__('enabling this option will be included in the admin panel a new shortcode that will allow the insertion of a badge for the pages present on google+. If you want to see the graphic result of badges read the official documentation.','sz-google'));
		}

		function callback_plus_shortcode_community()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_shortcode_co_enable');
			$this->moduleCommonFormDescription(__('enabling this option will be included in the admin panel a new shortcode that will allow the insertion of a badge for the community present on google+. If you want to see the graphic result of badges read the official documentation.','sz-google'));
		}

		function callback_plus_shortcode_followers()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_shortcode_fl_enable');
			$this->moduleCommonFormDescription(__('enabling this option will be included in the admin panel a new shortcode that will allow the insertion of a badge for the followers present on google+. If you want to see the graphic result of badges read the official documentation.','sz-google'));
		}

		function callback_plus_shortcode_size_portrait()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_plus','plus_shortcode_size_portrait','medium','350');
			$this->moduleCommonFormDescription(__('this option is used to set a default width for use in widget when no size is set manually and is selected as the display mode portrait. If you do not specify a value for this field will be used the standard width of 350px and height will be calculated.','sz-google'));
		}

		function callback_plus_shortcode_size_landscape()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_plus','plus_shortcode_size_landscape','medium','350');
			$this->moduleCommonFormDescription(__('this option is used to set a default width for use in widget when no size is set manually and is selected as the display mode landscape. If you do not specify a value for this field will be used the standard width of 350px and height will be calculated.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_plus_widget_button_plusone()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_button_enable_widget_plusone');
			$this->moduleCommonFormDescription(__('with this option is activated widget that allows the insertion of a +1 button in our article or web page. The +1 button has the same function as the button like this on facebook. If you want to customize the position in the theme use the function PHP.','sz-google'));
		}

		function callback_plus_widget_button_sharing()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_button_enable_widget_sharing');
			$this->moduleCommonFormDescription(__('this option allows the activation of widget for sharing a link on social network google+. Using this function you can insert the button in an article or a page wordpress. If you want to customize the position in the theme use the function PHP.','sz-google'));
		}

		function callback_plus_widget_button_follow()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_button_enable_widget_follow');
			$this->moduleCommonFormDescription(__('this option allows the activation of widget for follow on social network google+. Using this function you can insert the button in an article or a page wordpress. If you want to customize the position in the theme use the function PHP.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_plus_button_plusone()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_button_enable_plusone');
			$this->moduleCommonFormDescription(__('with this option is activated shortcode that allows the insertion of a +1 button in our article or web page. The +1 button has the same function as the button like this on facebook. If you want to customize the position in the theme use the function PHP.','sz-google'));
		}

		function callback_plus_button_sharing()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_button_enable_sharing');
			$this->moduleCommonFormDescription(__('this option allows the activation of shortcode for sharing a link on social network google+. Using this function you can insert the button in an article or a page wordpress. If you want to customize the position in the theme use the function PHP.','sz-google'));
		}

		function callback_plus_button_follow()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_button_enable_follow');
			$this->moduleCommonFormDescription(__('this option allows the activation of shortcode for follow on social network google+. Using this function you can insert the button in an article or a page wordpress. If you want to customize the position in the theme use the function PHP.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_plus_comments_gp()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_comments_gp_enable');
			$this->moduleCommonFormDescription(__('if you enable this feature will be added to the new commenting system made ​​available on the social network google+. The widget will be placed in the standard location for comments to wordpress. For customizations use the function PHP.','sz-google'));
		}

		function callback_plus_comments_wp()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_comments_wp_enable');
			$this->moduleCommonFormDescription(__('activating this option you can activate the system\'s comments Wodpress same time as those of google+. To decide the position of the comments you have to set the fields to follow. You can choose whether to place comments after the content or last.','sz-google'));
		}

		function callback_plus_comments_ac()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_comments_ac_enable');
			$this->moduleCommonFormDescription(__('enabling this option, the comment system is generated immediately after the post content or web page, otherwise it is inserted at the point that the standard function is called of the comments of wordpress in the file of the active theme.','sz-google'));
		}

		function callback_plus_comments_aw()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_comments_aw_enable');
			$this->moduleCommonFormDescription(__('enabling this option, the comment system is generated immediately after standard comments, otherwise it is inserted at the point that the standard function is called of the comments of wordpress in the file of the active theme.','sz-google'));
		}

		function callback_plus_comments_wd()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_comments_wd_enable');
			$this->moduleCommonFormDescription(__('if you need to insert the component for comments to google+ in a sidebar you can activate this option and use the new widget that you will find in your admin panel, you specify the size or the way you use responsive design for automatic resize.','sz-google'));
		}

		function callback_plus_comments_sh()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_comments_sh_enable');
			$this->moduleCommonFormDescription(__('enabling this option will allow you to use the shortcode [sz-gplus-comments] that will allow you to insert a box for comments to google plus in any part of your post or page standard wordpress. For greater customization uses function PHP.','sz-google'));
		}

		function callback_plus_comments_fixed_size()
		{
			$this->moduleCommonFormNumberStep1('sz_google_options_plus','plus_comments_fixed_size','medium',__('responsive design','sz-google'));
			$this->moduleCommonFormDescription(__('if you do not specify a value for this option, the size of the container of the comments will be performed in responsive mode, otherwise it is applied to a fixed size specified in this field. Use this option on wordpress themes with fixed size.','sz-google'));
		}

		function callback_plus_comments_title()
		{
			$this->moduleCommonFormText('sz_google_options_plus','plus_comments_title','large',__('<h3>{title}</h3>','sz-google'));
			$this->moduleCommonFormDescription(__('Set this field to a string that identifies the creation of a first title of the widget for comments to google+. You can use html code and insert the variable with the name of {title}. If not given the title value for this field will be ignored and not printed.','sz-google'));
		}

		function callback_plus_comments_css_class_1()
		{
			$this->moduleCommonFormText('sz_google_options_plus','plus_comments_css_class_1','large',__('name for CSS class (1)','sz-google'));
			$this->moduleCommonFormDescription(__('the widget comments has two wraps, each of these we can define the CSS style class that allow us to adapt the graphics of the comments to that of wordpress theme installed. Leave blank to not add any classes to the container of the comments.','sz-google'));
		}

		function callback_plus_comments_css_class_2()
		{
			$this->moduleCommonFormText('sz_google_options_plus','plus_comments_css_class_2','large',__('name for CSS class (2)','sz-google'));
			$this->moduleCommonFormDescription(__('the widget comments has two wraps, each of these we can define the CSS style class that allow us to adapt the graphics of the comments to that of wordpress theme installed. Leave blank to not add any classes to the container of the comments.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_plus_comments_dt()
		{
			$this->moduleCommonFormCheckboxYesNo(
				'sz_google_options_plus','plus_comments_dt_enable'
			);

			// Creazione delle select per l'indicazione della data
	
			$date_format = __('MDA','sz-google');

			// Creazione delle select per l'indicazione della data

			if ($date_format == 'MDA') {
				$this->callback_plus_comments_dt_month(); 
				$this->callback_plus_comments_dt_day(); 
				$this->callback_plus_comments_dt_year(); 
				echo '<span class="fieldtext">'.__('(month / day / year)','sz-google').'</span>';
			}

			if ($date_format == 'DMA') {
				$this->callback_plus_comments_dt_day(); 
				$this->callback_plus_comments_dt_month(); 
				$this->callback_plus_comments_dt_year(); 
				echo '<span class="fieldtext">'.__('(day / month / year)','sz-google').'</span>';
			}

			if ($date_format == 'AMD') {
				$this->callback_plus_comments_dt_year(); 
				$this->callback_plus_comments_dt_month(); 
				$this->callback_plus_comments_dt_day(); 
				echo '<span class="fieldtext">'.__('(year / month / day)','sz-google').'</span>';
			}

			$this->moduleCommonFormDescription(__('enabling this option you can activate the commenting system only posts that are inserted after a certain date. This function is useful for managing two different systems of comments in reference to a period of time.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_plus_comments_dt_day()
		{
			$options = get_option('sz_google_options_plus');

			if (!isset($options['plus_comments_dt_day']))   $options['plus_comments_dt_day']   = sprintf('%02d',date('d'));
			if (!isset($options['plus_comments_dt_month'])) $options['plus_comments_dt_month'] = sprintf('%02d',date('m')); 
			if (!isset($options['plus_comments_dt_year']))  $options['plus_comments_dt_year']  = sprintf('%04d',date('Y')); 

			echo '<select name="sz_google_options_plus[plus_comments_dt_day]">';

			foreach (range(1,31) as $key) {
				$selected = ($options['plus_comments_dt_day'] == sprintf('%02d',$key)) ? ' selected = "selected"' : '';
				echo '<option value="'.sprintf('%02d',$key).'"'.$selected.'>'.sprintf('%02d',$key).'</option>';
			}

			echo '</select>';
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_plus_comments_dt_month()
		{
			$options = get_option('sz_google_options_plus');

			if (!isset($options['plus_comments_dt_day']))   $options['plus_comments_dt_day']   = sprintf('%02d',date('d'));
			if (!isset($options['plus_comments_dt_month'])) $options['plus_comments_dt_month'] = sprintf('%02d',date('m')); 
			if (!isset($options['plus_comments_dt_year']))  $options['plus_comments_dt_year']  = sprintf('%04d',date('Y')); 

			echo '<select name="sz_google_options_plus[plus_comments_dt_month]">';

			foreach (range(1,12) as $key) {
				$selected = ($options['plus_comments_dt_month'] == sprintf('%02d',$key)) ? ' selected = "selected"' : '';
				echo '<option value="'.sprintf('%02d',$key).'"'.$selected.'>'.sprintf('%02d',$key).'</option>';
			}

			echo '</select>';
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_plus_comments_dt_year()
		{
			$options = get_option('sz_google_options_plus');

			if (!isset($options['plus_comments_dt_day']))   $options['plus_comments_dt_day']   = sprintf('%02d',date('d'));
			if (!isset($options['plus_comments_dt_month'])) $options['plus_comments_dt_month'] = sprintf('%02d',date('m')); 
			if (!isset($options['plus_comments_dt_year']))  $options['plus_comments_dt_year']  = sprintf('%04d',date('Y')); 

			echo '<select name="sz_google_options_plus[plus_comments_dt_year]">';

			foreach (array_reverse(range(2000,date('Y')+1)) as $key) {
				$selected = ($options['plus_comments_dt_year'] == sprintf('%04d',$key)) ? ' selected = "selected"' : '';
				echo '<option value="'.sprintf('%04d',$key).'"'.$selected.'>'.sprintf('%04d',$key).'</option>';
			}

			echo '</select>';
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_plus_enable_author()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_enable_author');
			$this->moduleCommonFormDescription(__('enabling this option will be placed in the HEAD section of the code necessary indication of the author connected to the current website and is generated string rel=author with the attribute href=author address.','sz-google'));
		}

		function callback_plus_enable_publisher()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_enable_publisher');
			$this->moduleCommonFormDescription(__('enabling this option will be placed in the HEAD section of the code necessary indication of the publisher connected to the current website and is generated string rel=publisher with the attribute href=publisher address.','sz-google'));
		}

		function callback_plus_usercontact_page()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_usercontact_page');
			$this->moduleCommonFormDescription(__('in the standard user profile wordpress is already a field that identifies the URL of your Google+ profile. By enabling this option you can add additional information that relates to Google+ and use it in your badges author present in your current theme.','sz-google'));
		}

		function callback_plus_usercontact_community()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_usercontact_community');
			$this->moduleCommonFormDescription(__('in the standard user profile wordpress is already a field that identifies the URL of your Google+ profile. By enabling this option you can add additional information that relates to Google+ and use it in your badges author present in your current theme.','sz-google'));
		}

		function callback_plus_usercontact_bestpost()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_usercontact_bestpost');
			$this->moduleCommonFormDescription(__('in the standard user profile wordpress is already a field that identifies the URL of your Google+ profile. By enabling this option you can add additional information that relates to Google+ and use it in your badges author present in your current theme.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)		
		 */

		function callback_plus_redirect_sign()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_redirect_sign');
			$this->moduleCommonFormDescription(__('with this option you can enable a rewrite rules that allows you to get a web address personalized pointing to the corresponding page on google plus such as mydomain.com/+. Activate this option and enter the complete link of the destination.','sz-google'));
		} 

		function callback_plus_redirect_sign_url()
		{
			$this->moduleCommonFormText('sz_google_options_plus','plus_redirect_sign_url','large',__('destination URL','sz-google'));
			$this->moduleCommonFormDescription(__('in this field you must enter the full URL for the landing page that describes the connection on google plus. In fact you can enter any URL even if the rewrite is designed for integration with google plus. Please make use of the most useful for your needs.','sz-google'));
		} 

		function callback_plus_redirect_plus()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_redirect_plus');
			$this->moduleCommonFormDescription(__('with this option you can enable a rewrite rules that allows you to get a web address personalized pointing to the corresponding page on google plus such as mydomain.com/plus. Activate this option and enter the complete link of the destination.','sz-google'));
		} 

		function callback_plus_redirect_plus_url()
		{
			$this->moduleCommonFormText('sz_google_options_plus','plus_redirect_plus_url','large',__('destination URL','sz-google'));
			$this->moduleCommonFormDescription(__('in this field you must enter the full URL for the landing page that describes the connection on google plus. In fact you can enter any URL even if the rewrite is designed for integration with google plus. Please make use of the most useful for your needs.','sz-google'));
		} 

		function callback_plus_redirect_curl()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_redirect_curl');
			$this->moduleCommonFormDescription(__('with this option you can enable a rewrite rules that allows you to get a web address personalized pointing to the corresponding page on google plus such as mydomain.com/origin. Activate this option and enter source and destination page.','sz-google'));
		} 

		function callback_plus_redirect_curl_source()
		{
			$this->moduleCommonFormText('sz_google_options_plus','plus_redirect_curl_dir','large',__('source path URL for redirect','sz-google'));
			$this->moduleCommonFormDescription(__('in this field you must enter the source partial URL of your domain on which to perform the rewrite rule. For example you can use as a value source URL string community/+ and associate the destination on a community present on google+.','sz-google'));
		}

		function callback_plus_redirect_curl_target()
		{
			$this->moduleCommonFormText('sz_google_options_plus','plus_redirect_curl_url','large',__('destination URL','sz-google'));
			$this->moduleCommonFormDescription(__('in this field you must enter the full URL for the landing page that describes the connection on google plus. In fact you can enter any URL even if the rewrite is designed for integration with google plus. Please make use of the most useful for your needs.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)
		 */

		function callback_plus_author_badge()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_plus','plus_author_badge');
			$this->moduleCommonFormDescription(__('activating this option are generated widget and shortcode one that can generate a badge linked to the author of the post currently displayed. You can specify various customization options, see the documentation for the plugin.','sz-google'));
		}
	}
}