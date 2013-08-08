<?php
/*
Plugin Name: WP DPLA
Version: 0.1-alpha
Description: Display related items from the Digital Public Library of America on your WP posts
Author: Boone B Gorges
Author URI: http://boone.gorg.es
Text Domain: wp-dpla
*/

function wp_dpla() {
	include __DIR__ . '/includes/class-wp-dpla.php';
	$GLOBALS['wp_dpla'] = new WP_DPLA();
}
add_action( 'plugins_loaded', 'wp_dpla' );

