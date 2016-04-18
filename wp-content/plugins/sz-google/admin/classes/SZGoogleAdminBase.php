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

if (!class_exists('SZGoogleAdminBase'))
{
	class SZGoogleAdminBase extends SZGoogleAdmin
	{
		/**
		 * Definition the constructor function, which is called
		 * at the time of the creation of an instance of this class
		 */

		function __construct()
		{
			$this->moduleSetClassName(__CLASS__);
			$this->moduleSetOptionSet('sz_google_options_base');

			// Checking if I have to perform operations that affect
			// the configuration and allocation of Google API

			$this->moduleCheckRequestAPI();

			// Calling up the function of the parent class to process the
			// variables that contain the values ​​of configuration section

			parent::__construct();

			// Add the link of settings below the title of the plugin
			// In the administration panel after activation and deactivation

			if (is_admin()) {
				add_filter("plugin_action_links_".plugin_basename(SZ_PLUGIN_GOOGLE_MAIN),array($this,'AddPluginSetting'));
			}

			// Add the style sheet and javascript in the 
			// administration pages that use the same plugin

			add_action('admin_enqueue_scripts',array($this,'moduleAdminAddStyles'));
			add_action('admin_enqueue_scripts',array($this,'moduleAdminAddScripts'));

			// Control options of modules to load and recall
			// the file administration is necessary if active

			$options = $this->getOptions();

			if ($options->plus          == '1') new SZGoogleAdminPlus();
			if ($options->analytics     == '1') new SZGoogleAdminAnalytics();
			if ($options->authenticator == '1') new SZGoogleAdminAuthenticator();
			if ($options->calendar      == '1') new SZGoogleAdminCalendar();
			if ($options->drive         == '1') new SZGoogleAdminDrive();
			if ($options->fonts         == '1') new SZGoogleAdminFonts();
			if ($options->groups        == '1') new SZGoogleAdminGroups();
			if ($options->hangouts      == '1') new SZGoogleAdminHangouts();
			if ($options->maps          == '1') new SZGoogleAdminMaps();
			if ($options->panoramio     == '1') new SZGoogleAdminPanoramio();
			if ($options->translate     == '1') new SZGoogleAdminTranslate();
			if ($options->recaptcha     == '1') new SZGoogleAdminRecaptcha();
			if ($options->youtube       == '1') new SZGoogleAdminYoutube();
			if ($options->documentation == '1') new SZGoogleAdminDocumentation();
			if ($options->tinymce       == '1') new SZGoogleAdminTinyMCE();
		}

		/**
		 * Creating the menu on the admin panel using values ​​
		 * such as configuration variables object (parent function)
		 */

		function moduleAddMenu()
		{
			// Aggiungo il menu principale dove verranno aggiunti tutti i moduli
			// del plugin aggiuntivi con la funzione add_submenu_page()

			add_menu_page('Google for WordPress','WP & Google','manage_options',
				'sz-google-admin.php',array($this,'moduleCallbackStart'));

			// Definition of general values ​​for the creation of a menu associated 
			// with the module options. Example slug, page title and menu title

			$this->menuslug   = 'sz-google-admin.php';
			$this->pagetitle  = ucwords(__('configuration','sz-google'));
			$this->menutitle  = ucwords(__('configuration','sz-google'));

			// Definition of sections that need to be made ​​in HTML
			// sections must be passed as an array of name = > title

			$this->sectionstabs = array(
				'01' => array('anchor' => 'modules','description' => __('modules','sz-google')),
				'02' => array('anchor' => 'api'    ,'description' => __('request API','sz-google')),
			);

			$this->sections = array(
				array('tab' => '01','section' => 'sz-google-admin.php'    ,'title' => ucwords(__('activation','sz-google'))),
				array('tab' => '02','section' => 'sz-google-admin-api.php','title' => ucwords(__('google API','sz-google'))),
			);

			$this->sectionstitle   = ucfirst(__('configuration version','sz-google').'&nbsp;'.SZ_PLUGIN_GOOGLE_VERSION);
			$this->sectionsoptions = array('sz_google_options_base');

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
				'01' => array('section' => 'sz_google_base_section','title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin.php'),
				'02' => array('section' => 'sz_google_base_api'    ,'title' => $this->null,'callback' => $this->callbacksection,'slug' => 'sz-google-admin-api.php'),
			);

			// General definition array containing a list of fields
			// All fields are added to the previously defined sections

			$this->sectionsfields = array(
				'01' => array(array('field' => 'plus'             ,'title' => ucwords(__('google+'             ,'sz-google')),'callback' => array($this,'callback_base_plus')),
				              array('field' => 'analytics'        ,'title' => ucwords(__('google analytics'    ,'sz-google')),'callback' => array($this,'callback_base_analytics')),
				              array('field' => 'authenticator'    ,'title' => ucwords(__('google authenticator','sz-google')),'callback' => array($this,'callback_base_authenticator')),
				              array('field' => 'calendar'         ,'title' => ucwords(__('google calendar'     ,'sz-google')),'callback' => array($this,'callback_base_calendar')),
				              array('field' => 'drive'            ,'title' => ucwords(__('google drive'        ,'sz-google')),'callback' => array($this,'callback_base_drive')),
				              array('field' => 'fonts'            ,'title' => ucwords(__('google fonts'        ,'sz-google')),'callback' => array($this,'callback_base_fonts')),
				              array('field' => 'groups'           ,'title' => ucwords(__('google groups'       ,'sz-google')),'callback' => array($this,'callback_base_groups')),
				              array('field' => 'hangouts'         ,'title' => ucwords(__('google hangouts'     ,'sz-google')),'callback' => array($this,'callback_base_hangouts')),
				              array('field' => 'maps'             ,'title' => ucwords(__('google maps'         ,'sz-google')),'callback' => array($this,'callback_base_maps')),
				              array('field' => 'panoramio'        ,'title' => ucwords(__('google panoramio'    ,'sz-google')),'callback' => array($this,'callback_base_panoramio')),
				              array('field' => 'reCAPTCHA'        ,'title' => ucwords(__('google reCAPTCHA'    ,'sz-google')),'callback' => array($this,'callback_base_recaptcha')),
				              array('field' => 'translate'        ,'title' => ucwords(__('google translate'    ,'sz-google')),'callback' => array($this,'callback_base_translate')),
				              array('field' => 'youtube'          ,'title' => ucwords(__('google youtube'      ,'sz-google')),'callback' => array($this,'callback_base_youtube')),
				              array('field' => 'documentation'    ,'title' => ucwords(__('documentation'       ,'sz-google')),'callback' => array($this,'callback_base_documentation')),
				              array('field' => 'tinymce'          ,'title' => ucwords(__('tinyMCE shortcodes'  ,'sz-google')),'callback' => array($this,'callback_base_tinymce')),),
				'02' => array(array('field' => 'API_enable'       ,'title' => ucwords(__('API enable'          ,'sz-google')),'callback' => array($this,'callback_base_api_enable')),
				            //array('field' => 'API_client_ID'    ,'title' => ucwords(__('API Client ID'       ,'sz-google')),'callback' => array($this,'callback_base_api_client_id')),
				            //array('field' => 'API_client_secret','title' => ucwords(__('API Client secret'   ,'sz-google')),'callback' => array($this,'callback_base_api_client_secret')),
				),
			);

			// Calling up the function of the parent class to process the 
			// variables that contain the values ​​of configuration section

			parent::moduleAddFields();
		}

		/**
		 * Creating additional links to insert into the snippets plugin
		 * present in the admin panel after active and deactive
		 */

		function AddPluginSetting($links)
		{
			$links[] = '<a href="'.menu_page_url('sz-google-admin.php',false).'">'.ucfirst(__('settings','sz-google')).'</a>'; 
			return $links; 
		}

		/**
		 * Add the style sheet and javascript in the 
		 * administration pages that use the same plugin
		 */

		function moduleAdminAddStyles() 
		{
			$pagenow   = $this->moduleAdminGetPageNow();
			$adminpage = $this->moduleAdminGetAdminPage();

			// Registration of CSS files and JavaScript files to be
			// loaded in the page according to the required function

			$CSS = plugin_dir_url(SZ_PLUGIN_GOOGLE_MAIN).'admin/files/css/sz-google-style-admin.css';
			wp_register_style('sz-google-style-admin',$CSS,array(),SZ_PLUGIN_GOOGLE_VERSION);

			// Control the loading of the file based on the
			// needs of the plugin when viewing admin pages

			if ($pagenow == 'widgets.php' or $pagenow == 'customize.php') 
				$widgets = true; else $widgets = false;

			if ($pagenow == 'profile.php' or ($pagenow == 'admin.php' && preg_match('#^sz-google#',$adminpage) === 1))
				$optionpage = true; else $optionpage = false;

			// Control in which I am the admin page for load
			// components CSS and javascript only when needed

			if ($widgets or $optionpage) {
				wp_enqueue_style('sz-google-style-admin');
			}
		}

		/**
		 * Add the style sheet and javascript in the 
		 * administration pages that use the same plugin
		 */

		function moduleAdminAddScripts() 
		{
			$pagenow   = $this->moduleAdminGetPageNow();
			$adminpage = $this->moduleAdminGetAdminPage();

			// Registration of CSS files and JavaScript files to be
			// loaded in the page according to the required function

			$JS1 = plugin_dir_url(SZ_PLUGIN_GOOGLE_MAIN).'admin/files/js/jquery.szgoogle.widgets.js';
			$JS2 = plugin_dir_url(SZ_PLUGIN_GOOGLE_MAIN).'admin/files/js/jquery.szgoogle.pages.js';

			wp_register_script('sz-google-javascript-widgets',$JS1);
			wp_register_script('sz-google-javascript-pages',$JS2);

			// Control the loading of the file based on the
			// needs of the plugin when viewing admin pages

			if ($pagenow == 'widgets.php' or $pagenow == 'customize.php') 
				$widgets = true; else $widgets = false;

			if ($pagenow == 'admin.php' && preg_match('#^sz-google#',$adminpage) === 1) 
				$optionpage = true; else $optionpage = false;

			// Control in which I am the admin page for load
			// components CSS and javascript only when needed

			if ($widgets) {
				if (!did_action('wp_enqueue_media')) wp_enqueue_media();
				wp_enqueue_script('sz-google-javascript-widgets');
			}

			if ($optionpage) {
				wp_enqueue_script('sz-google-javascript-pages');
			}
		}

		/**
		 * Check via URL when you are on an authentication request
		 * for the configuration of the panel Google API and its token
		 */

		private function moduleCheckRequestAPI() 
		{
			$pagenow   = $this->moduleAdminGetPageNow();
			$adminpage = $this->moduleAdminGetAdminPage();
			$adminURI  = admin_url('admin.php?page=sz-google-admin.php');

			// Before carrying out checks on URL for special operations, regarding
			// API requests and any redirects, check if they are on the plugin page

			if ($pagenow == 'admin.php' && preg_match('#^sz-google#',$adminpage) === 1) 
			{
				// If it exists on the URL variable sz-google-request-auth, it means that 
				// you have requested a call to Google to authenticate the user and get a token

				if (isset($_GET['sz-google-request-auth']) and $_GET['sz-google-request-auth']=='ok') 
				{
					$token = $this->getOptionsAPI('API_token_refresh');

					// I run revoke the current token before making a new request
					// for authorization OAuth2 and request a new token

					if (!empty($token)) {
						$this->setOptionsAPI('API_token','');
						$this->setOptionsAPI('API_token_access','');
						$this->setOptionsAPI('API_token_refresh','');
						wp_remote_get('https://accounts.google.com/o/oauth2/revoke?token='.$token);
					}

					// Defining array with the values ​​to be sent to the
					// authorization page OAuth2 for services that relate to Google

					$options = $this->getOptions();

					$scope  = 'https://www.googleapis.com/auth/drive ';
					$scope .= 'https://www.googleapis.com/auth/plus.me';

					$params = array(
						'client_id'       => $options->API_client_ID,
						'scope'           => $scope,
						'redirect_uri'    => $adminURI,
						'response_type'   => 'code',
						'state'           => 'token',
						'access_type'     => 'offline',
						'approval_prompt' => 'force'
					);

					// Request authentication via OAuth2 service call Google
					// after this request is redirected to "redirect_uri"

					header('Location: https://accounts.google.com/o/oauth2/auth?'.http_build_query($params));
					exit();
				}

				// When he returns to redirect the request for authorization, if I
				// receive a valid token will be the variable "code" otherwise "error"

				if (isset($_GET['state']) and $_GET['state'] == 'token' and isset($_GET['code'])) 
				{
					$options = $this->getOptions();

					// Defining array with the values ​​to be sent to the
					// authorization page OAuth2 for services that relate to Google

					$this->setOptionsAPI('API_token',$_GET['code']);

					$post_vars = array(
						'code'          => $_GET['code'],
						'client_id'     => $options->API_client_ID,
						'client_secret' => $options->API_client_secret,
						'redirect_uri'  => $adminURI,
						'grant_type'    => 'authorization_code'
					);

					// Google Calling Service to request a token refresh and 
					// an access token that must be recreated at maturity

					$result = wp_remote_post('https://accounts.google.com/o/oauth2/token', 
						array('timeout'=>25,'method'=>'POST','body'=>$post_vars));

					// In case of an error, do not run the assignment of the token  
					// and send an error message on the console wordpress

					if (is_wp_error($result)) {
						header('Location: '.$adminURI.'&state=error#api'); exit();
					}

					// Check if the response has a body JSON and if 
					// contains a refresh token and an access token

					$json_values = json_decode($result['body'], true);

					if (isset($json_values['refresh_token'])) {
						$this->setOptionsAPI('API_token_refresh',$json_values['refresh_token']);
					}

					if (isset($json_values['access_token'])) {
						$this->setOptionsAPI('API_token_access',$json_values['access_token']);
					}

					// If I get a valid token run update option API
					// otherwise I perform a redirect with the error flag

					if (isset($json_values['access_token'])) { header('Location: '.$adminURI.'&state=success#api'); exit(); } 
						else { header('Location: '.$adminURI.'&state=error#api'); exit(); }
				}

				// When he returns to redirect the request for authorization, if I
				// receive a valid token will be the variable "code" otherwise "error"

				if (isset($_GET['state']) and $_GET['state'] == 'token' and !isset($_GET['code'])) {
					header('Location: '.$adminURI.'&state=error#api'); exit();
				}

				// Checking the status URL for successful execution or error
				// I add a notification message on standard section wordpress

				if (isset($_GET['state']) and $_GET['state'] == 'success') {
					add_action('admin_notices',array($this,'addAdminMessageSuccess'));
				}

				if (isset($_GET['state']) and $_GET['state'] == 'error') {
					add_action('admin_notices',array($this,'addAdminMessageError'));
				}
			}




/* SE 22222222222222222222 */
/* SE 22222222222222222222 */


		if (isset($_GET['state']) and $_GET['state'] == 'revoke') 
		{
			$token  = $this->getOptionsAPI('API_token_refresh');
			$ignore = wp_remote_get('https://accounts.google.com/o/oauth2/revoke?token='.$token);
			$this->setOptionsAPI('API_token_refresh','');
		}




		}

		/**
		 * Function to indicate the message after the authentication 
		 * request oAuth2 terms of the services of google
		 */

		function addAdminMessageSuccess()
		{
			echo '<div class="updated"><p>(<b>sz-google</b>) - ';
			echo ucfirst(__('google API configuration was successful.','sz-google'));
			echo '</p></div>';
		}

		/**
		 * Function to indicate the message after the authentication 
		 * request oAuth2 terms of the services of google
		 */

		function addAdminMessageError()
		{
			echo '<div class="error"><p>(<b>sz-google</b>) - ';
			echo ucfirst(__('google API configuration is terminated with error.','sz-google'));
			echo '</p></div>';
		}

		/**
		 * Storage and playback options related to the 
		 * configuration of API and tokens allowed
		 */

		private function getOptionsAPI($name) {
			$options = get_option('sz_google_options_api');
			if (isset($options[$name])) return $options[$name];
				else return '';
		}

		/**
		 * Storage and playback options related to the 
		 * configuration of API and tokens allowed
		 */

		private function setOptionsAPI($name,$value) {
			$options = get_option('sz_google_options_api');
			$options[$name] = $value;
			update_option('sz_google_options_api',$options);
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)
		 */

		function callback_base_plus() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_base','plus');
			$this->moduleCommonFormDescription(__('with this module you can manage some widgets in the social network google+, for example, we can insert badge of the profiles, badge of the pages, badge of the community, buttons follow, buttons share, buttons +1, comments system and much more.','sz-google'));
		}

		function callback_base_analytics() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_base','analytics');
			$this->moduleCommonFormDescription(__('activating this module can handle the tracking code present in google analytics, so as to store the access statistics related to our website. Once you have entered the tracking code, you can view hundreds of statistics from the admin panel of google analytics.','sz-google'));
		}

		function callback_base_authenticator() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_base','authenticator');
			$this->moduleCommonFormDescription(__('with this module you can enable two-factor authentication to be added to the standard wordpress. Before starting this option carefully read the documentation. Each authorized user must synchronize the code with a mobile device.','sz-google'));
		}

		function callback_base_calendar()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_base','calendar');
			$this->moduleCommonFormDescription(__('activating this module you can get a widget and shortcode to insert one of the components of google calendar on your wordpress. You will find several options in the configuration screen to customize your best result.','sz-google'));
		}

		function callback_base_drive()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_base','drive');
			$this->moduleCommonFormDescription(__('through this module you can insert into wordpress some features of google drive, you will find widgets and shortcodes to help you with this task. Obviously many functions can only work if you login with a valid account on google.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)
		 */

		function callback_base_fonts()
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_base','fonts');
			$this->moduleCommonFormDescription(__('with this module you can load into your wordpress theme fonts made ​​available on Google CDN. Simply select the desired font and HTML parts concerned. The plugin will automatically add all the necessary parts of the code.','sz-google'));
		}

		function callback_base_groups() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_base','groups');
			$this->moduleCommonFormDescription(__('enabling this module you get a widget and a shortcode to perform embed on google groups. Then you can insert into a wordpress page or in a sidebar content navigable for a group. You can specify various customization options.','sz-google'));
		}

		function callback_base_hangouts() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_base','hangouts');
			$this->moduleCommonFormDescription(__('activating this module you can use the functions for the hangouts of google. For example, you can insert the buttons for the start of hangout directly in the page of your site. You can also create a widget to put on your sidebar.','sz-google'));
		}

		function callback_base_maps() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_base','maps');
			$this->moduleCommonFormDescription(__('activating this module you can use the google maps and put them in an article or in a widget on the sidebar of wordpress. Read the documentation to learn about all the configuration options and the features available.','sz-google'));
		}

		function callback_base_panoramio() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_base','panoramio');
			$this->moduleCommonFormDescription(__('through this module you can insert some features of photos panoramio, you will find widgets and shortcodes to help you with this task and use the functions in your favorite theme. You can also specify parameters for selecting user, group, tag etc.','sz-google'));
		}

		function callback_base_recaptcha() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_base','recaptcha');
			$this->moduleCommonFormDescription(__('with this form you can activate the functions of reCAPTCHA on some components present in wordpress. Just ask for the activation keys to google and configure the module in the configuration panel.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)
		 */

		function callback_base_translate() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_base','translate');
			$this->moduleCommonFormDescription(__('with this module you can place the widget for automatic content translate on your website made ​​available by google translate tools. The widget can be placed in the context of a post or a sidebar defined in your theme.','sz-google'));
		}

		function callback_base_youtube() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_base','youtube');
			$this->moduleCommonFormDescription(__('with this module can be inserted in wordpress a video youtube, you can also use a widget to the inclusion of video in the sidebar on your theme. Through the options in the shortcode you can configure many parameters to customize the embed code.','sz-google'));
		}

		function callback_base_documentation() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_base','documentation');
			$this->moduleCommonFormDescription(__('activating this option you can see the documentation in the main menu of this plugin with the parameters to be used in [shortcodes] or PHP functions provided. There is a series of boxes in alphabetical order.','sz-google'));
		}

		function callback_base_tinymce() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_base','tinymce');
			$this->moduleCommonFormDescription(__('activating this option all shortcodes plugin that are active will be present in the editor of wordpress when inserting a post or page. Using the drop-down menu that you will find in the editor you can specify all the options shortcode via a pop-up window.','sz-google'));
		}

		/**
		 * Definition functions for the creation of the various options that should be included 
		 * in the general form of configuration and saved on a database of wordpress (options)
		 */

		function callback_base_api_enable() 
		{
			$this->moduleCommonFormCheckboxYesNo('sz_google_options_base','API_enable');
			$this->moduleCommonFormDescription(__('enable this option if you want to use the features of the plugin that need a personal authorization code from google. If you do not enable this option and you do not configure the authentication phase, some functions of the plugin will be disabled.','sz-google'));
		}

		function callback_base_api_client_id() {
			$this->moduleCommonFormText('sz_google_options_base','API_client_ID','large',__('insert your client ID','sz-google'));
			$this->moduleCommonFormDescription(__('enter the value (Client ID) that you can find in the project that you created in the official page of Google APIs console. If you are not familiar with this procedure, carefully read the guide written just for this operation.','sz-google'));
		}

		function callback_base_api_client_secret() {
			$this->moduleCommonFormText('sz_google_options_base','API_client_secret','large',__('insert your client secret','sz-google'));


$a  = '<a href="'.menu_page_url('sz-google-admin.php',false).'&amp;sz-google-request-auth=ok">aaa</a><br>';
$a .= 'Token '        .$this->getOptionsAPI('API_token').'<br>';
$a .= 'Token access ' .$this->getOptionsAPI('API_token_access').'<br>';
$a .= 'Token refresh '.$this->getOptionsAPI('API_token_refresh').'<br>';

$a .= admin_url('wp-admin/admin.php?page=sz-google-admin.php');


//update_option('TEST_API_token','');
//update_option('TEST_API_token_access','');
//update_option('TEST_API_token_refresh','');

			$this->moduleCommonFormDescription(__($a,'sz-google'));
		}
	}
}