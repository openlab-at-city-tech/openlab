<?php

/*
Plugin Name: Multipage
Plugin URI: http://wordpress.org/plugins/sgr-nextpage-titles/
Description: Multipage Plugin for WordPress will give you the ability to order a post in multiple subpages, giving each subpage a title and having a table of contents.
Author: Envire Web Solutions
Version: 1.4.4
Author URI: https://www.envire.it
Text Domain: sgr-nextpage-titles
Domain Path: /languages/
License: GPL v3
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'MPP_VERSION', '1.4.4' );
define( 'MPP__MINIMUM_WP_VERSION', '3.9' );
define( 'MPP__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MPP__PLUGIN_FILE', __FILE__ );

/**
 * The main function.
 *
 * @return Multipage instance.
 */
function multipage() {
	return Multipage::instance();
}

require_once( MPP__PLUGIN_DIR . 'class-mpp.php' );

// Start
multipage();