<?php
/*
Plugin Name: BP System Report [WDS MODIFIED - DO NOT UPGRADE]
Plugin URI: http://teleogistic.net/code/buddypress/bp-system-report
Description: Records regular summaries of BuddyPress-related systemwide information
Version: 0.1
Author: Boone Gorges
Author URI: http://teleogistic.net
*/


function bp_system_report_more_reccurences() {
	return array(
		'weekly' => array('interval' => 604800, 'display' => 'Once Weekly'), 
		'twominutes' => array('interval' => 3000, 'display' => 'Every Two Minutes' )
	);
}
add_filter('cron_schedules', 'bp_system_report_more_reccurences');

register_activation_hook(__FILE__, 'bp_system_report_activation');
function bp_system_report_activation() {
	wp_schedule_event( time() + 30, 'twicedaily', 'bp_system_report_pseudo_cron_hook' );
}

register_deactivation_hook(__FILE__, 'bp_system_report_deactivation' );
function bp_system_report_deactivation() {
	wp_clear_scheduled_hook('bp_system_report_pseudo_cron_hook');
}



/* Only load the BuddyPress plugin functions if BuddyPress is loaded and initialized. */
function bp_system_report_init() {
	require( dirname( __FILE__ ) . '/bp-system-report-bp-functions.php' );
}
add_action( 'bp_init', 'bp_system_report_init' );

function bp_system_report_admin_init() {
	wp_register_style( 'bp-system-report-css', WP_PLUGIN_URL . '/bp-system-report/bp-system-report-css.css' );
}
add_action( 'admin_init', 'bp_system_report_admin_init' );

function bp_system_report_locale_init () {
	$plugin_dir = basename(dirname(__FILE__));
	$locale = get_locale();
	$mofile = WP_PLUGIN_DIR . "/bp-system-report/languages/bp-system-report-$locale.mo";
      
      if ( file_exists( $mofile ) )
      		load_textdomain( 'bp-system-report', $mofile );
}
add_action ('plugins_loaded', 'bp_system_report_locale_init');

?>