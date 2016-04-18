<?php
/*
Plugin Name: OpenLab Grade Comments
Version: 1.0
Description: Grades and private comments for WordPress blog posts. Built for the City Tech OpenLab.
Author: Boone Gorges
Author URI: http://boone.gorg.es
Plugin URI: http://openlab.citytech.cuny.edu
Text Domain: openlab-grade-comments
Domain Path: /languages
*/

// This plugin is now a stub that activates wp-grade-comments.
function olgc_check_active() {
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	}

	if ( ! is_plugin_active( 'wp-grade-comments/wp-grade-comments.php' ) ) {
		activate_plugin( 'wp-grade-comments/wp-grade-comments.php' );

		// Good bye.
		deactivate_plugins( 'openlab-grade-comments/openlab-grade-comments.php' );
	}
}
add_action( 'init', 'olgc_check_active' );
