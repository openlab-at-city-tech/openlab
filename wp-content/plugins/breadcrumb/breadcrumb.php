<?php
/*
Plugin Name: Breadcrumb
Plugin URI: https://www.pickplugins.com/item/breadcrumb-awesome-breadcrumbs-style-navigation-for-wordpress/
Description: Awesome Breadcrumb for wordpress.
Version: 1.5.20
WC requires at least: 3.0.0
WC tested up to: 5.1
Author: PickPlugins
Author URI: http://pickplugins.com
Text Domain: breadcrumb
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


class BreadcrumbMain{
	
	public function __construct(){
		
		define('breadcrumb_plugin_url', plugins_url('/', __FILE__)  );
		define('breadcrumb_plugin_dir', plugin_dir_path( __FILE__ ) );
		define('breadcrumb_plugin_name', 'Breadcrumb' );
		define('breadcrumb_plugin_version', '1.5.20' );


        require_once( breadcrumb_plugin_dir . 'includes/class-settings-tabs.php');
		require_once( breadcrumb_plugin_dir . 'includes/functions.php');
        require_once( breadcrumb_plugin_dir . 'includes/functions-settings.php');

		require_once( breadcrumb_plugin_dir . 'includes/themes-css.php');
		
		require_once( breadcrumb_plugin_dir . 'includes/class-shortcodes.php');
		require_once( breadcrumb_plugin_dir . 'includes/class-settings.php');
        require_once( breadcrumb_plugin_dir . 'includes/functions-hooks.php');


        add_action( 'wp_enqueue_scripts', array( $this, '_front_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, '_admin_scripts' ) );
		add_filter('widget_text', 'do_shortcode');
		add_action( 'plugins_loaded', array( $this, 'breadcrumb_load_textdomain' ));
		
		}
	
	public function breadcrumb_load_textdomain() {

        $locale = apply_filters( 'plugin_locale', get_locale(), 'breadcrumb' );
        load_textdomain('breadcrumb', WP_LANG_DIR .'/breadcrumb/breadcrumb-'. $locale .'.mo' );

        load_plugin_textdomain( 'breadcrumb', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );




		}
		
	
	public function _front_scripts(){


	
	}
	
	
	public function _admin_scripts(){

        $screen = get_current_screen();

        wp_register_style('font-awesome-5', breadcrumb_plugin_url.'assets/admin/css/fontawesome.css');

        wp_register_style('settings-tabs', breadcrumb_plugin_url.'assets/settings-tabs/settings-tabs.css');
        wp_register_script('settings-tabs', breadcrumb_plugin_url.'assets/settings-tabs/settings-tabs.js'  , array( 'jquery' ));

        wp_register_script('jquery.lazy', breadcrumb_plugin_url.'assets/admin/js/jquery.lazy.js', array('jquery'));

        if($screen->id =='toplevel_page_breadcrumb_settings'){
            $settings_tabs_field = new settings_tabs_field();
            $settings_tabs_field->admin_scripts();
        }




	}
	
	
	

}

new BreadcrumbMain();

