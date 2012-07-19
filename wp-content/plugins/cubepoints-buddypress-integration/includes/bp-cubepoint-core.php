<?php

/**
 * BUDDYPRESS CUBEPOINTS CORE
 * Handles the overall operations of the plugin
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

// Love, The BuddyPress-Media Team ... ;) *** Ahh, thanks ;)
// ------------------------------------------------

define ( 'BP_CUBEPOINT_IS_INSTALLED', 1 );
define ( 'BP_CUBEPOINT_DB_VERSION', '1.9.8' );

load_textdomain( 'bp-cubepoint', dirname( __FILE__ ) . '/languages/bp-cubepoint-' . get_locale() . '.mo' );
 
// require ( dirname( __FILE__ ) . '/bp-cubepoint-classes.php' ); // This not being used at the moment. Maybe later..
require ( dirname( __FILE__ ) . '/bp-cubepoint-screens.php' );
require ( dirname( __FILE__ ) . '/bp-cubepoint-cssjs.php' );
// require ( dirname( __FILE__ ) . '/bp-cubepoint-templatetags.php' ); // This not being used at the moment. Maybe later..
require ( dirname( __FILE__ ) . '/bp-cubepoint-functions.php' );
require ( dirname( __FILE__ ) . '/bp-cubepoint-filters.php' );


/**
 * bp_cubepoint_setup_globals()
 *
 * Sets up BuddyPress CubePoint's global variables.
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function bp_cubepoint_setup_globals() {
    
	global $bp, $wpdb;

	$bp->cubepoint->id = 'cubepoint';
	$bp->cubepoint->table_name = $wpdb->base_prefix . 'cubepoints';
	$bp->cubepoint->table_name = $wpdb->prefix . 'cubepoints';

	$bp->cubepoint->slug = get_option( 'bp_slug_cp_bp' );
	
	$bp->cubepoint->points_slug = 'points';
	$bp->cubepoint->table_slug = 'table';
	$bp->cubepoint->earnpoints_slug = 'earnpoints';
	$bp->cubepoint->awards_slug = 'awards';
	$bp->cubepoint->bp_cubepoint_per_page = get_option('bp_points_logs_per_page_cp_bp');
	
}
add_action( 'bp_setup_globals', 'bp_cubepoint_setup_globals', 2 );
add_action( 'bp_setup_admin_bar', 'bp_cubepoint_setup_globals', 2 );


/**
 * bp_cubepoint_add_admin_menu()
 *
 * Adds the BuddyPress CubePoints admin menu to the wordpress "Site" admin menu
 * 
 *  @version 1.9.8.2
 *  @since 1.0
 */
function bp_cubepoint_add_admin_menu() {

	global $bp;

	if ( !$bp->loggedin_user->is_super_admin ){
	    
		return false;
	}

	require_once('bp-cubepoint-admin.php');

	add_submenu_page('cp_admin_manage', 'Buddypress Integration - ' .__('CubePoints','cp_buddypress'), __('BuddyPress','cp_buddypress'), 8, 'cubebp-settings', 'cubebp_admin');
	
}
add_action( 'admin_menu', 'bp_cubepoint_add_admin_menu' );
add_action( 'network_admin_menu', 'bp_cubepoint_add_admin_menu' );


/**
 * bp_cubepoint_setup_nav()
 *
 * Sets up the user profile navigation items for the component. This adds the top level nav
 * item and all the sub level nav items to the navigation array. This is then
 * rendered in the template.
 * 
 *  @version 1.9.8.2
 *  @since 1.0
 */
function bp_cubepoint_setup_nav() {
    
	global $bp;
	$cb_bp_sitewidemtitle = get_option('bp_sitewidemtitle_cp_bp');
	$cb_bp_earnpointtitle = get_option('bp_earnpoints_menutitle_cp_bp');
	$cb_bp_awardstitle = get_option('bp_awards_menutitle_cp_bp');

	if($bp->displayed_user->id){

		$cubepoint_link = $bp->displayed_user->domain . $bp->cubepoint->slug . '/';
		$cubepoint_link_title = bp_word_or_name( __( "My Points", 'cp_buddypress' ), __( "%s's points", 'cp_buddypress' ) ,false,false);
	}
	else {
		$cubepoint_link = $bp->loggedin_user->domain . $bp->cubepoint->slug . '/';
		$cubepoint_link_title = __( "My Points", 'cp_buddypress' );
	}


	// Add 'Points' to the main user profile navigation
	bp_core_new_nav_item( array(
		'name' => __( 'Points', 'cp_buddypress' ),
		'slug' => $bp->cubepoint->slug,
		'position' => 80,
		'screen_function' => 'bp_cubepoint_screen_points',
		'default_subnav_slug' => $bp->cubepoint->points_slug,
	) );
		
	bp_core_new_subnav_item( array(
		'name' => $cubepoint_link_title,
		'slug' => $bp->cubepoint->points_slug,
		'parent_slug' => $bp->cubepoint->slug,
		'parent_url' => $cubepoint_link,
		'screen_function' => 'bp_cubepoint_screen_points',
		'position' => 10
	) );

	if(get_option('bp_sitewide_menu_cp_bp')) {

		bp_core_new_subnav_item( array(
			'name' => __( $cb_bp_sitewidemtitle, 'cp_buddypress' ),
			'slug' => $bp->cubepoint->table_slug,
			'parent_slug' => $bp->cubepoint->slug,
			'parent_url' => $cubepoint_link,
			'screen_function' => 'bp_cubepoint_screen_table',
			'position' => 30,
		) );
	}
	
	if(get_option('bp_earnpoints_menu_cp_bp')) {

		bp_core_new_subnav_item( array(
			'name' => __( $cb_bp_earnpointtitle, 'cp_buddypress' ),
			'slug' => $bp->cubepoint->earnpoints_slug,
			'parent_slug' => $bp->cubepoint->slug,
			'parent_url' => $cubepoint_link,
			'screen_function' => 'bp_cubepoint_screen_earnpoints',
			'position' => 50,
		) );
	}
	
	if(get_option('bp_awards_menu_onoff_cp_bp')) {

		bp_core_new_subnav_item( array(
			'name' => __( $cb_bp_awardstitle, 'cp_buddypress' ),
			'slug' => $bp->cubepoint->awards_slug,
			'parent_slug' => $bp->cubepoint->slug,
			'parent_url' => $cubepoint_link,
			'screen_function' => 'bp_cubepoint_screen_awards',
			'position' => 70,
		) );	
	}
		
}
add_action( 'bp_setup_nav', 'bp_cubepoint_setup_nav', 2 );
add_action( 'bp_setup_admin_bar', 'bp_cubepoint_setup_nav', 2 );


/**
 * bp_cubepoint_load_template_filter()
 *
 * You can define a custom load template filter for your component. This will allow
 * you to store and load template files from your plugin directory.
 * 
 * @version 1.9.8
 * @since 1.0
 */
function bp_cubepoint_load_template_filter( $found_template, $templates ) {
    
	global $bp;

	if( $bp->current_component != $bp->cubepoint->slug ){

		return $found_template;
	}

	foreach( (array) $templates as $template ) {

		if ( file_exists( STYLESHEETPATH . '/' . $template ) ){

			$filtered_templates[] = STYLESHEETPATH . '/' . $template;
		}
		elseif( file_exists( TEMPLATEPATH . '/' . $template ) ){

			$filtered_templates[] = TEMPLATEPATH . '/' . $template;
		}
		else {
			$filtered_templates[] = dirname( __FILE__ ) . '/templates/' . $template;
		}

	}

	$found_template = $filtered_templates[0];

	return apply_filters( 'bp_cubepoint_load_template_filter', $found_template );
	
}
add_filter( 'bp_located_template', 'bp_cubepoint_load_template_filter', 10, 2 );


/**
 * bp_cubepoint_load_subtemplate()
 *
 * @version 1.9.8
 * @since 1.0
 */
function bp_cubepoint_load_subtemplate( $template_name ) {

	if ( file_exists(STYLESHEETPATH . '/' . $template_name . '.php')) {

		$located = STYLESHEETPATH . '/' . $template_name . '.php';
	}
	else if ( file_exists(TEMPLATEPATH . '/' . $template_name . '.php') ) {

		$located = TEMPLATEPATH . '/' . $template_name . '.php';
	}
	else{
		$located = dirname( __FILE__ ) . '/templates/' . $template_name . '.php';
	}

	include ($located);
	
}
?>