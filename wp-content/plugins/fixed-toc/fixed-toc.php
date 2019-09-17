<?php
/*
 * Plugin Name: Fixed TOC
 * Plugin URI: https://codecanyon.net/item/fixed-toc-wordpress-plugin/7264676?ref=wphigh
 * Description: Generate a table of contents automatically from content of a post. Fixing in the page, user-friendly view.
 * Author: wphigh
 * Author URI: https://codecanyon.net/user/wphigh?ref=wphigh
 * Version: 3.1.14
 * Created: 26 March 14
 * Last Update: 02 July 19
 * Text Domain: fixedtoc
 * License: See http://codecanyon.net/licenses
 */

/**
 * Prevent access directly.
 *
 * @since 3.1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Define the plugin absolute root directory.
 *
 * @since 3.0.0
 */
define( 'FTOC_ROOTDIR', plugin_dir_path( __FILE__ ) );

/**
 * Define the plugin absolute root file.
 *
 * @since 3.1.0
 */
define( 'FTOC_ROOTFILE', __FILE__ );

/**
 * Functions
 *
 * @since 3.0.0
 */
require_once 'inc/functions.php';

/**
 * Initialization
 *
 * @since 3.1.0
 */
require_once 'inc/init.php';
new Fixedtoc_Init();

/**
 * Conditions
 *
 * @since 3.0.0
 */
require_once 'inc/class-conditions.php';

/**
 * Admin control
 *
 * @since 3.0.0
 */
require_once 'admin/class-admin-control.php';
new Fixedtoc_Admin_Control();

/**
 * Frontend control
 *
 * @since 3.0.0
 */
if ( ! is_admin() ) {
	require_once 'frontend/class-frontend-control.php';
	new Fixedtoc_Frontend_Control();
}
	