<?php
/**
 * Plugin Name: Easy Custom Sidebars
 * Plugin URI: http://www.titaniumthemes.com/wordpress-sidebar-plugin
 * Description: A simple and easy way to add custom sidebars/widget areas to your WordPress theme.
 * Version: 1.0.9
 * Author: Titanium Themes
 * Author URI: http://www.titaniumthemes.com
 * Text Domain: easy-custom-sidebars
 * License: GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * 
 */

/**
 * Easy Custom Sidebars Initialisation
 *
 * This file is responsible for enabling the easy custom
 * sidebars plugin. It load all of the classes and methods
 * required for this plugin to function.
 * 
 * @package     Easy_Custom_Sidebars
 * @author      Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license     GPL-2.0+
 * @copyright   Copyright (c) 2015, Titanium Themes
 * @version     1.0.9
 * 
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Include Class Files
 *
 * Loads required classes for this plugin to function.
 *
 * Codex functions used:
 * {@link http://codex.wordpress.org/Function_Reference/plugin_dir_path} 	plugin_dir_path()
 *
 * @since 1.0.1
 * @version 1.0.9
 * 
 */
require_once( plugin_dir_path( __FILE__ ) . 'class-easy-custom-sidebars.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/frontend/class-ecs-posttype.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/frontend/class-ecs-widget-areas.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/frontend/class-ecs-frontend.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/admin/walker/class-ecs-walker-edit.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/admin/walker/class-ecs-walker-checklist.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/admin/class-ecs-admin.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/admin/class-ecs-admin-controller.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/admin/class-ecs-ajax.php' );


/**
 * Initialise Class Instances
 *
 * Creates new class instances when the 'plugins-loaded'
 * action is fired. Only runs admin specific functionality
 * when the user is in the admin area for performance.
 *
 * Codex functions used: 
 * {@link http://codex.wordpress.org/Function_Reference/add_action} 	add_action()
 *
 * @since 1.0.1
 * @version 1.0.9
 * 
 */
add_action( 'plugins_loaded', array( 'Easy_Custom_Sidebars', 'get_instance' ) );
add_action( 'plugins_loaded', array( 'ECS_Posttype', 'get_instance' ) );
add_action( 'plugins_loaded', array( 'ECS_Widget_Areas', 'get_instance' ) );
add_action( 'plugins_loaded', array( 'ECS_Frontend', 'get_instance' ) );

// Load plugin text domain
add_action( 'plugins_loaded', array( 'Easy_Custom_Sidebars', 'load_text_domain' ) );

if ( is_admin() ) {
	add_action( 'plugins_loaded', array( 'ECS_Admin', 'get_instance' ) );
	add_action( 'plugins_loaded', array( 'ECS_Ajax', 'get_instance' ) );	
}


/**
 * Register Activation/Deactivation Hooks
 * 
 * Register hooks that are fired when this plugin is 
 * activated or deactivated. When the plugin is deleted, 
 * the uninstall.php file is loaded.
 *
 * Codex functions used: 
 * {@link http://codex.wordpress.org/Function_Reference/register_activation_hook} 		register_activation_hook()
 * {@link http://codex.wordpress.org/Function_Reference/register_deactivation_hook} 	register_deactivation_hook()
 * 
 * @since 1.0.1
 * @version 1.0.9
 * 
 */
register_activation_hook( __FILE__, array( 'Easy_Custom_Sidebars', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Easy_Custom_Sidebars', 'deactivate' ) );
