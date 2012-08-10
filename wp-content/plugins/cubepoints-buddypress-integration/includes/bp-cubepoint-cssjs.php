<?php

/**
 * BUDDYPRESS CUBEPOINTSCSS & JS
 * Handles the CSS and JS used in BuddyPress CubePoints
 *
 * @version 0.1.9.8
 * @since 1.0
 * @package BuddyPress CubePoints
 * @subpackage CSS & JS
 * @license GPL v2.0
 * @link http://wordpress.org/extend/plugins/cubepoints-buddypress-integration/
 *
 * ========================================================================================================
 */

/**
 * NOTE: You should always use the wp_enqueue_script() and wp_enqueue_style() functions to include
 * javascript and css files.
 */

function bp_cubepoint_add_css() {
    
	global $bp;
	
	    // wp_enqueue_style( 'bp-cubepoint-css', dirname(__FILE__) . '/css/general.dev.css' );
	    wp_enqueue_style( 'bp-cubepoint-css', WP_PLUGIN_URL .'/cubepoints-buddypress-integration/includes/css/general.css' );
		
	    wp_print_styles();
}

add_action( 'wp_head', 'bp_cubepoint_add_css' );

?>