<?php
/**
 * Plugin Name: Mihdan: Ajax Edit Comments
 * Plugin URI: https://wordpress.org/plugins/wp-ajax-edit-comments/
 * Description: Ajax Edit Comments allows users to edit their comments for a period of time. Administrators have a lot more features, such as the ability to edit comments directly on a post or page.
 * Author: Mikhail Kobzarev
 * Version: 6.1
 * Requires at least: 3.1
 * Author URI: https://www.kobzarev.com
 * Contributors:  Ronald Huereca, Ajay Dsouza, Josh Benham  and Glenn Ansley
 * License: GPL2
 * Text Domain: ajaxEdit
 * Domain Path: /languages/
 *
 * @package ajaxEdit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPrapAjaxEditComments' ) ) {

	/**
	 * Class WPrapAjaxEditComments
	 */
	class WPrapAjaxEditComments {
		//public
		public $admin = false;	
		public $skip = false;
		public $upgrade = false;
		
		//private
		private $user_options = array();
		private $admin_options = array();
		private $errors = '';
		private $minutes = 5;
		private $version = '6.1';
		private $colorbox_params = array();
		private $plugin_url = '';
		private $plugin_dir = '';
		private $plugin_basename = '';
		
		//Variables for iThemes integration
		public $_defaults = array();
		public $_version = '';
		public $_var = '';


		public $_options = array();

		/**
		* PHP 5 Constructor
		*/		
		function __construct() {
			$this->admin_options = $this->get_admin_options();
			$this->plugin_url = rtrim( plugin_dir_url(__FILE__), '/' );
			$this->plugin_dir = rtrim( plugin_dir_path(__FILE__), '/' );
			$this->plugin_basename = plugin_basename( __FILE__  );
			
			//Include Classes
			include_once('lib/class.file.php');
			include_once('lib/class.actions.php');
			include_once('lib/class.admin.php');
			include_once('lib/class.ajax.php');
			include_once('lib/class.css.php');
			include_once('lib/class.js.php');
			include_once('lib/class.core.php');
			include_once('lib/class.dependencies.php');
			include_once('lib/class.filters.php');
			include_once('lib/class.utility.php');
			
			//For add-ons
			do_action('aec-addons-load');
			
			//When a comment is posted
			add_action('comment_post', array("AECActions", 'comment_posted'),100,1);
			
			
			//Custom actions for other plugin authors
			add_action('wp_ajax_comments_comment_edited', array('AECActions', 'edit_notification'),1,2);
			add_action('wp_ajax_comments_comment_edited', array('AECActions', 'comment_edited'),2,2);
			add_action('wp_ajax_comments_remove_content_filter', array('AECActions', 'comment_filter'));
			add_action( 'add_wp_ajax_comments_css_editor', array( 'AECDependencies', 'ajax_url' ) ); //This loads the ajaxurl variable on the frontend
			
			add_action('init', array(&$this, 'init'));
			
			
			//For disabling trackbacks/pingbacks
			if ($this->admin_options['disable_trackbacks'] == 'true') {
				add_filter('comments_array', array("AECFilters","filter_trackbacks"), 0);
				add_filter('get_comments_number', array("AECFilters",'filter_comments_number'));
			}
			
			//For disabling no-follow on links
			if ($this->admin_options['disable_nofollow'] == 'true') {
				add_filter('get_comment_author_link', array("AECFilters", 'remove_nofollow'));
				add_filter('comment_text', array("AECFilters", 'remove_nofollow'));
				add_filter('thesis_comment_text', array("AECFilters", 'remove_nofollow')); //For Thesis (todo - remove when necessary)
			}
			
			//For disabling self-pings
			if ($this->admin_options['disable_selfpings'] == 'true') {
				add_action( 'pre_ping', array("AECActions",'disable_selfpings'));
			}
			
			//For the plugin settings link
			add_filter('plugin_action_links_' . plugin_basename(__FILE__)
, array("AECFilters", 'add_settings_link'), -10);
			
		} //end constructor
		
		//Returns a network or localized admin option
		public function get_admin_option( $key = '' ) {			
			$admin_options = $this->get_admin_options( apply_filters( 'aec_network_option', false ) );
			if ( array_key_exists( $key, $admin_options ) ) {
				return $admin_options[ $key ];
			}
			return false;
		}
		public function reset_admin_options() {
			$this->admin_options = array();
			delete_site_option( 'WPAjaxEditComments20' );
			$this->admin_options = $this->get_admin_options();
			return $this->admin_options;
		} //end reset_admin_options
		
		public function get_all_admin_options() {
			return $this->admin_options;
		}
		//Returns an array of admin options
		private function get_admin_options( $network = false ) {
			if (empty($this->admin_options)) {
				$admin_options = array(
					'allow_editing' => 'true',
					'allow_editing_after_comment' => 'true',
					'minutes' => '5', 
					'edit_text' => '', 
					'show_timer' => 'true',
					'show_pages' => 'true',
					'spam_text' => __('Your edited comment was marked as spam.  If this is in error, please contact the admin.', 'ajaxEdit'),
					'email_edits' => 'false',
					'number_edits' => '0',
					'spam_protection' => 'akismet',
					'registered_users_edit' => 'false',
					'registered_users_name_edit' => 'true',
					'registered_users_email_edit' => 'false',
					'registered_users_url_edit' => 'true',
					'use_mb_convert' => 'true',
					'allow_name_editing' => 'true',
					'allow_email_editing' => 'false',
					'allow_url_editing' => 'true',
					'allow_css' => 'true',
					'allow_css_editor' => 'true',
					'clear_after' => 'true',
					'javascript_scrolling' => 'true',
					'comment_display_top' => 'false',
					'undo' => '',
					'icon_display' => 'dropdown',
					'icon_set' => 'circular',
					'use_rtl' => 'false',
					'affiliate_text' => '',
					'affiliate_show' => 'false',
					'scripts_in_footer' => 'false',
					'scripts_on_archive' => 'false',
					'allowed_archives' => array(),
					'compressed_scripts' => 'true',
					'drop_down' => array(),
					'classic' => array(),
					'disable_trackbacks' => 'false',
					'disable_nofollow' => 'false',
					'disable_selfpings'=> 'false',
					'delink_content' => 'false',
					'after_deadline_posts' => 'true',
					'after_deadline_popups' => 'true',
					'expand_popups' => 'true',
					'expand_posts' => 'true',
					'use_wpload' => 'false',
					'allow_registeredediting' => 'true',
					'atdlang' => 'en',
					'request_deletion_behavior' => 'request',
					'version' => '',
					'allow_editing_editors' => 'true',
					'overwrite_styles' => 'false',
					'WPLANG' => '',
					'WP_DEBUG' => '',
					'enable_colorbox' => 'true',
					'colorbox_width' => '580',
					'colorbox_height' => '560'				
				);
				$options = $this->is_multisite() ? get_site_option( 'WPAjaxEditComments20' ) : get_option( 'WPAjaxEditComments20' ) ;
				if ( !$options ) $options = get_option( 'WPAjaxEditComments20' ); //For upgrading and Multisite support
				if (!empty($options)) {
					foreach ($options as $key => $option) {
						if (array_key_exists($key, $admin_options)) {
							$admin_options[$key] = $option;
						}
					}
				}
				//Update the array for dropdown items
				$dropdown = array(
							'dropdownapprove' => array('id' => 'dropdownapprove','column' => '0', 'position' => '0', 'enabled' => '1', 'text' =>__('Approve', 'ajaxEdit')), 
							'dropdownmoderate' => array('id' => 'dropdownmoderate','column' => '0','position' => '1','enabled' =>'1','text' =>__('Moderate', 'ajaxEdit')),
							'dropdownspam' => array('id' => 'dropdownspam','column' => '0','position' => '2','enabled' =>'1','text' => __('Spam', 'ajaxEdit')),
							'dropdowndelete' => array('id' => 'dropdowndelete','column' => '0','position' => '3','enabled' =>'1','text' =>__('Delete', 'ajaxEdit')),
							'dropdowndelink' => array('id' => 'dropdowndelink','column' => '1','position' => '0','enabled' =>'1','text' =>__('De-link', 'ajaxEdit')),
							'dropdownmove' => array('id' => 'dropdownmove','column' => '1','position' => '1','enabled' =>'1','text' =>__('Move', 'ajaxEdit')),
							'dropdownemail' => array('id' => 'dropdownemail','column' => '1','position' => '2','enabled' =>'1','text' =>__('E-mail', 'ajaxEdit')),
							'dropdownblacklist' =>  array('id' => 'dropdownblacklist','column' => '1','position' => '3','enabled' =>'1','text' =>__('Blacklist', 'ajaxEdit')));
				foreach ($dropdown as $item => $value) {
					if (!array_key_exists($item, $admin_options['drop_down'])) {
						$admin_options['drop_down'][$item] = $value;
					}
				}
				foreach ($admin_options['drop_down'] as $item => $value) {
					if (!array_key_exists($item, $dropdown)) {
						unset($admin_options['drop_down'][$item]);
					}
				}
				//Update the array for classic items
				$classic = array(
							'edit' => array('id' => 'edit','column' => '0', 'enabled' => '1', 'text' =>__('Edit', 'ajaxEdit')),
							'approve' => array('id' => 'approve','column' => '1', 'enabled' => '1', 'text' =>__('Approve', 'ajaxEdit')), 
							'moderate' => array('id' => 'moderate','column' => '2','enabled' =>'1','text' =>__('Moderate', 'ajaxEdit')),
							'spam' => array('id' => 'spam','column' => '3','enabled' =>'1','text' => __('Spam', 'ajaxEdit')),
							'delete' => array('id' => 'delete','column' => '4','enabled' =>'1','text' =>__('Delete', 'ajaxEdit')),
							'delink' => array('id' => 'delink','column' => '5','enabled' =>'1','text' =>__('De-link', 'ajaxEdit')),
							'move' => array('id' => 'move','column' => '6','enabled' =>'1','text' =>__('Move', 'ajaxEdit')),
							'email' => array('id' => 'email','column' => '7','enabled' =>'1','text' =>__('E-mail', 'ajaxEdit')),
							'blacklist' =>  array('id' => 'blacklist','column' => '8','enabled' =>'1','text' =>__('Blacklist', 'ajaxEdit')));
				foreach ($classic as $item => $value) {
					if (!array_key_exists($item, $admin_options['classic'])) {
						$admin_options['classic'][$item] = $value;
					}
				}
				foreach ($admin_options['classic'] as $item => $value) {
					if (!array_key_exists($item, $classic)) {
						unset($admin_options['classic'][$item]);
					}
				}
				//Save the options
				$this->admin_options = $admin_options;
				$this->save_admin_options();								
			}
			//Serve uncompressed scripts when WP is in debug mode
			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG == true ) {
				$this->admin_options['compressed_scripts'] = 'false';
			}

			
			return $this->admin_options;
		} //end get_admin_options
		
		//Returns an array of "all" user options
		private function get_all_user_options() {
			if ( ! function_exists( "get_currentuserinfo" ) ) {
				return array();
			}
			if (empty($this->user_options)) {
				$user_email = AECUtility::get_user_email(); 
				$defaults = array(
				'comment_editing' => 'true', 
				'admin_editing' => 'false'
				);
				$this->user_options = $this->is_multisite() ? get_site_option( 'WPAjaxEditAuthoruser_options' ) : get_option( 'WPAjaxEditAuthoruser_options' );
				if ( !$this->user_options ) $this->user_options = get_option( 'WPAjaxEditAuthoruser_options' ); //For upgrading and Multisite support
				if (!isset($this->user_options)) {
					$this->user_options = array();
				}
				//See if an older version doesn't match the new defaults
				if (empty($this->user_options[$user_email])) {
					$this->user_options[$user_email] = $defaults;
				}	elseif(!is_array($this->user_options[$user_email])) {
					$this->user_options[$user_email] = $defaults;
				} else {
						foreach ($this->user_options[$user_email] as $key => $option) {
							$defaults[$key] = $option;								
						}
						$this->user_options[$user_email] = $defaults;
				}
				$this->save_admin_options();
			}
			return $this->user_options;
		} //end get_all_user_options
		
		//Returns a colorbox variable
		public function get_colorbox_param( $key = '' ) {
			if ( array_key_exists( $key, $this->colorbox_params ) ) {
				return $this->colorbox_params[ $key ];
			}
			return false;
		}
		/* get_error - Returns an error message based on the passed code
		Parameters - $code (the error code as a string)
		Returns an error message */
		public function get_error($code = '') {
			//Try to initialize errors if the object doesn't exist
			if (!is_object($this->errors)) {
				$this->errors = AECCore::initialize_errors();
				if (!is_object($this->errors))
					return __("Unknown error.", 'ajaxEdit');
			}
			//Get the error message and return it 
			$errorMessage = $this->errors->get_error_message($code);
			if ($errorMessage == null) {
				return __("Unknown error.", 'ajaxEdit');
			}
			return __($errorMessage, 'ajaxEdit'); 
		} //end get_error
		public function get_minutes() {
			return $this->minutes;
		}
		public function get_plugin_dir( $path = '' ) {
			$dir = $this->plugin_dir;
			if ( !empty( $path ) && is_string( $path) )
				$dir .= '/' . ltrim( $path, '/' );
			return $dir;		
		}
		public function get_plugin_path() {
			return $this->plugin_basename;
		}
		//Returns the plugin url
		public function get_plugin_url( $path = '' ) {
			$dir = $this->plugin_url;
			if ( !empty( $path ) && is_string( $path) )
				$dir .= '/' . ltrim( $path, '/' );
			return $dir;	
		}
		//Returns an array of an individual's options
		public function get_user_option( $key = '' ) {
			$options = $this->get_user_options();
			if ( array_key_exists( $key, $options ) ) {
				return $options[ $key ];
			}

			return array();
		}
		private function get_user_options() {
			if (empty($this->user_options)) { $this->user_options = $this->get_all_user_options(); }
			return $this->user_options[AECUtility::get_user_email()];
		} //end get_user_options
		public function get_version() {
			return $this->version;
		}
		/* init - Run upon WordPress initialization */
		public function init() {
			//If registered users can only comment and user is not logged in, skip loading the plugin.
			include_once(ABSPATH . WPINC . '/pluggable.php');
			if (get_option('comment_registration') == '1'){
				if (!is_user_logged_in()) {
					return;
				}
			}
					
			//Initialize Addons	
			do_action('aec-addons-init');
			
			$this->plugin_url = apply_filters('aec-addons-plugin-directory', $this->get_plugin_url());
			
			$this->colorbox_params['script_handler'] = apply_filters('aec-colorbox-script-name', 'colorbox');
			$this->colorbox_params['style_handler'] = apply_filters('aec-colorbox-style-name', 'colorbox');
			
			
			//If a user isn't logged in and has no comment cookie, don't load the plugin either
			if (!is_user_logged_in()) {
				$cookieloaded = false;
				foreach($_COOKIE as $value => $key){
					if (strstr($value,'WPAjaxEditCommentsComment')){         
						$cookieloaded = true; break;			
					}
				}
				if (!$cookieloaded) {
					add_action("wp_print_styles", array('AECDependencies','load_frontend_css'));
					add_action('wp_print_scripts', array('AECDependencies','load_frontend'),1000);
					return;
				}
			}
			
			add_action( 'wp_print_scripts', array( 'AECDependencies', 'ajax_url' ), 12);
			
			$this->errors = AECCore::initialize_errors();
	
			$this->skip = false;
			//css
			add_action("wp_print_styles", array('AECDependencies',"load_frontend_css"));
			add_action("wp_print_styles", array('AECDependencies',"add_css"));
			add_action('admin_print_styles', array('AECDependencies',"add_css")); 
			
			//JavaScript
			add_action('admin_print_scripts-index.php', array('AECDependencies','add_post_scripts'),1000); 
			add_action('admin_print_scripts-edit-comments.php', array('AECDependencies','add_post_scripts'),1000); 
			if ( !is_admin( ) ) add_action('wp_print_scripts', array('AECDependencies','add_post_scripts'),1000);			
			
			//Ajax stuff
			AECAjax::initialize_actions();
			
			
			//Admin options
			if ( AECCore::is_multisite() ) {
     			add_action( 'network_admin_menu', array("AECAdmin",'add_admin_pages') );
			} else {
   			  add_action( 'admin_menu', array("AECAdmin",'add_admin_pages') );
			}
			
			add_action( 'template_redirect', array( $this, 'comment_text' ) ); //front end
			add_action( 'auth_redirect', array( $this, 'comment_text' ) ); //admin panel
			
			
			//* Localization Code */
			load_plugin_textdomain( 'ajaxEdit', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			
		}//end function init
		
		public function comment_text() {
			if ( !is_feed() && !is_comment_feed() ) {
				//Yay, filters.
				add_filter('comment_excerpt', array("AECFilters", 'add_edit_links'), '1000');
				//Commented out because some ppl add issues with these screwing up their theme
				//add_filter('get_comment_date', array(&$this, 'add_date_spans'), '1000');
				//add_filter('get_comment_time', array(&$this, 'add_time_spans'), '1000');
				add_filter('comment_text', array("AECFilters", 'add_edit_links'), '1000'); //Low priority so other HTML can be added first
				add_filter('thesis_comment_text', array("AECFilters", 'add_edit_links'),'1000'); //For Thesis (todo - remove when necessary)
				add_filter('get_comment_author_link', array("AECFilters", 'add_author_spans'), '1000'); //Low priority so other HTML can be added first
			}
		} //end comment_text
		
		public function is_multisite() {
			//global $aecomments;
			$multisite_network = false;
			if ( ! function_exists( 'is_plugin_active_for_network' ) )  require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
			if ( is_plugin_active_for_network( plugin_basename( __FILE__  ) ) ) {
				$multisite_network = true;
			}
			return $multisite_network;
		}
		//For iThemes integration with the updater
		public function load() {
			$this->_options = $this->is_multisite() ? get_site_option( $this->_var ) : get_option( $this->_var );
			$options = array_merge( $this->_defaults, (array)$this->_options );
			
			if ( $options !== $this->_options ) {
				// Defaults existed that werent already in the options so we need to update their settings to include some new options.
				$this->_options = $options;
				$this->save();
			}
			
			return true;
		} //end load
		//for iThemes integration with updater
		public function save() {
			update_site_option($this->_var, $this->_options);
			return true;
		} //end save
		
		public function set_user_option( $key = '', $value = '' ) {
			if ( empty( $key ) ) return;
			$this->user_options[ $key ] = $value;
		}
		
		public function save_admin_option( $key = '', $value = '' ) {
			$this->admin_options[ $key ] = $value;
			$this->save_admin_options();
		}
		//Saves Ajax Edit Comments settings for admin and admin users
		public function save_admin_options( $admin_options = false, $user_options = false ){
			if (!empty($this->admin_options)) {
				if ( is_array( $admin_options ) ) {
					$this->admin_options = $admin_options;
				}
				if ( $this->is_multisite() ) {
					update_site_option( 'WPAjaxEditComments20', $this->admin_options);
				} else {
					update_option( 'WPAjaxEditComments20', $this->admin_options );
				}
			}
			if (!empty($this->user_options)) {
				if ( $this->is_multisite() ) {
					update_site_option( 'WPAjaxEditAuthoruser_options', $this->user_options);
				} else {
					update_option( 'WPAjaxEditAuthoruser_options', $this->user_options );
				}
			}
		} //end save_admin_options
		
		/**
		 * Wrapper for AECUtility::get_post_types(). Gets public default post types.
		 * @return array
		 */
		public function get_default_post_types() {
			return AECUtility::get_post_types();
		}

		/**
		 * Wrapper for AECUtility::get_post_types(). Gets public custom post types.
		 * @return array
		 */
		public function get_custom_post_types() {
			return AECUtility::get_post_types([
				'public' => true,
				'_builtin' => false
			]);
		}

		/**
		 * Wrapper for AECUtility::get_all_post_types(). Gets all public post types.
		 * @return array
		 */
		public function get_all_post_types() {
			$built_in = $this->get_default_post_types();
			$custom = $this->get_custom_post_types();
			return array_merge($built_in, $custom);
		}
    } //end class
}
//instantiate the class
global $aecomments;
if (class_exists('WPrapAjaxEditComments')) {
	if ( version_compare( get_bloginfo( 'version' ), '3.1', '>=' ) ) {
		add_action( 'plugins_loaded', 'aec_instantiate' );
	}
}
function aec_instantiate() {
	global $aecomments;
	$aecomments = new WPrapAjaxEditComments();
}
//Template redirection stuff
add_action('template_redirect', 'aec_load_pages');
add_filter('query_vars', 'aec_query_triggers');
function aec_load_pages() {
	AECCore::load_pages();
}
function aec_query_triggers($vars) {
	return AECCore::add_query_triggers($vars);
}
//Helper function for debugging
if ( !function_exists( 'wp_print_r' ) ) {
	function wp_print_r( $args, $die = true ) {
		$print_r = '<pre>' . print_r( $args, true ) . '</pre>';
		if ( $die ) die( $print_r );
		else echo $print_r;
	}
}
