<?php 

/**
 * BUDDYPRESS CUBEPOINTS SCREEN FUNCTIONS
 * 
 * Screen functions are the controllers of BuddyPress. They will execute when their
 * specific URL is caught. They will first save or manipulate data using business
 * functions, then pass on the user to a template file.
 * 
 * @version 0.1.9.8
 * @since 1.0                
 * @package BuddyPress CubePoints
 * @subpackage Main
 * @license GPL v2.0
 * @link http://wordpress.org/extend/plugins/cubepoints-buddypress-integration/
 *
 * ========================================================================================================
 */

/**
 * bp_cubepoint_screen_points()
 *
 * A cubepoint page
 * 
 * @version 1.9.8
 * @since 1.0
 */
function bp_cubepoint_screen_points() {

	do_action( 'bp_cubepoint_screen_points' );
	
	bp_core_load_template( apply_filters( 'bp_cubepoint_template_screen_points', 'cubepoint/points' ) );
}

/**
 * bp_cubepoint_screen_table()
 *
 * Sets up and displays the screen output for the sub nav item "example/table"
 * 
 * @version 1.9.8
 * @since 1.0
 */
function bp_cubepoint_screen_table() {
    
	global $bp;
	
	do_action( 'bp_cubepoint_screen_table' );
	
	bp_core_load_template( apply_filters( 'bp_cubepoint_template_screen_points', 'cubepoint/table' ) );
}

/**
 * bp_cubepoint_screen_table()
 *
 * How to earn points
 * 
 * @version 1.9.8
 * @since 1.0
 */
function bp_cubepoint_screen_earnpoints() {
    
	global $bp;
	
	do_action( 'bp_cubepoint_screen_earnpoints' );
	
	bp_core_load_template( apply_filters( 'bp_cubepoint_screen_earnpoints', 'cubepoint/earnpoints' ) );
}

/**
 * bp_cubepoint_screen_awards()
 *
 * Awards Page
 * 
 * @version 1.9.8
 * @since 1.0
 */
function bp_cubepoint_screen_awards() {
    
	global $bp;
	
	do_action( 'bp_cubepoint_screen_earnpoints' );
	
	bp_core_load_template( apply_filters( 'bp_cubepoint_screen_awards', 'cubepoint/awards' ) );
}

?>