<?php
/*
Plugin Name: P3 (Plugin Performance Profiler)
Plugin URI: http://support.godaddy.com/godaddy/wordpress-p3-plugin/
Description: See which plugins are slowing down your site.  Create a profile of your WordPress site's plugins' performance by measuring their impact on your site's load time.
Author: GoDaddy.com
Version: 1.5.3.9
Author URI: http://www.godaddy.com/
Text Domain: p3-profiler
Domain Path: /languages
*/

define( 'P3_VERSION', '1.5.3.9' );

// Make sure it's wordpress
if ( !defined( 'ABSPATH') )
	die( 'Forbidden' );

/**************************************************************************/
/**        PACKAGE CONSTANTS                                             **/
/**************************************************************************/

// Shortcut for knowing our path
define( 'P3_PATH',  realpath( dirname( __FILE__ ) ) );
load_plugin_textdomain( 'p3-profiler', false, plugin_basename( P3_PATH ) . '/languages/' );

// Plugin slug
define( 'P3_PLUGIN_SLUG', 'p3-profiler' );

/**************************************************************************/
/**        AUTOLOADING                                                   **/
/**************************************************************************/

// Autoload classes, if possible
if ( function_exists( 'spl_autoload_register') ) {
	spl_autoload_register( 'p3_profiler_autoload' );
} else {
	require_once( P3_PATH . '/classes/class.p3-profiler-reader.php' );
	require_once( P3_PATH . '/classes/class.p3-profiler-table-sorter.php' );
	require_once( P3_PATH . '/classes/class.p3-profiler-table.php' );
	require_once( P3_PATH . '/classes/class.p3-profiler-plugin.php' );
	require_once( P3_PATH . '/classes/class.p3-profiler-plugin-admin.php' );
	require_once( P3_PATH . '/exceptions/class.p3-profiler-no-data-exception.php' );
}

/**************************************************************************/
/**        START PROFILING                                               **/
/**************************************************************************/

// Start profiling.  If it's already been started, this line won't do anything
require_once P3_PATH . '/start-profile.php';

/**************************************************************************/
/**        PLUGIN HOOKS                                                  **/
/**************************************************************************/

// Ajax actions
if ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ) {

	add_action( 'admin_init', array( 'P3_Profiler_Plugin_Admin', 'set_path' ) );
	add_action( 'wp_ajax_p3_start_scan', array( 'P3_Profiler_Plugin_Admin', 'ajax_start_scan' ) );
	add_action( 'wp_ajax_p3_stop_scan', array( 'P3_Profiler_Plugin_Admin', 'ajax_stop_scan' ) );
	add_action( 'wp_ajax_p3_send_results', array( 'P3_Profiler_Plugin_Admin', 'ajax_send_results' ) );
	add_action( 'wp_ajax_p3_save_settings', array( 'P3_Profiler_Plugin_Admin', 'ajax_save_settings' ) );

// Admin hooks
} elseif ( is_admin() ) {

	// Show the 'Profiler' option under the 'Plugins' menu
	add_action( 'admin_menu', array( 'P3_Profiler_Plugin', 'tools_menu' ) );

	// Show the 'Profile now' link on the plugins table
	add_action( 'plugin_action_links', array( 'P3_Profiler_Plugin', 'add_settings_link'), 10, 2 );

	if ( isset( $_REQUEST['page'] ) && P3_PLUGIN_SLUG == $_REQUEST['page'] ) {

		// Pre-processing of actions
		add_action( 'admin_init', array( 'P3_Profiler_Plugin_Admin', 'set_path' ) );
		add_action( 'admin_init', array( 'P3_Profiler_Plugin_Admin', 'init' ) );

		// Show any notices
		add_action( 'admin_notices', array( 'P3_Profiler_Plugin_Admin', 'show_notices' ) );
	}

	function p3_plugin_disclaimers( $profile ) {
		$disclaimed_plugins = array(
			'jetpack',
			'wordpress-seo',
		);

		if ( $detected = array_intersect( $disclaimed_plugins, $profile->get_raw_plugin_list() ) ) {
			?>
			<div class="updated inline">
				<p><?php printf( __( 'Some plugins may show artificially high results.  <a href="%s">More info</a>', 'p3-profiler' ), admin_url( 'tools.php?page=p3-profiler&p3_action=help#q17' ) ); ?></p>
				<ul style="list-style: initial; margin-left: 1.5em;">
				<?php foreach ( $detected as $plugin ) : ?>
					<li><?php echo $profile->get_plugin_name( $plugin ); ?></li>
				<?php endforeach; ?>
				</ul>
			</div>
			<?php
		}
	}
	add_action( 'p3_runtime_by_plugin_notifications', 'p3_plugin_disclaimers' );

// Remove the admin bar when in profiling mode
} elseif ( defined( 'WPP_PROFILING_STARTED' ) || isset( $_GET['P3_HIDE_ADMIN_BAR'] ) ) {
	add_action( 'plugins_loaded', array( 'P3_Profiler_Plugin_Admin', 'remove_admin_bar' ) );
}

// Install / uninstall hooks
register_activation_hook( P3_PATH . DIRECTORY_SEPARATOR . 'p3-profiler.php', array( 'P3_Profiler_Plugin', 'activate' ) );
register_deactivation_hook( P3_PATH . DIRECTORY_SEPARATOR . 'p3-profiler.php', array( 'P3_Profiler_Plugin', 'deactivate' ) );
if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	add_action( 'wpmu_delete_blog', array( 'P3_Profiler_Plugin', 'delete_blog' ) );
}

/**
 * Autoloader ... very little logic needed
 * @param string $className
 * @return
 */
function p3_profiler_autoload( $className ) {
	switch ( $className ) {
		case 'P3_Profiler_Reader' :
			require_once( P3_PATH . '/classes/class.p3-profiler-reader.php' );
			break;
		case 'P3_Profiler_Table_Sorter' :
			require_once( P3_PATH . '/classes/class.p3-profiler-table-sorter.php' );
			break;
		case 'P3_Profiler_Table' :
			require_once( P3_PATH . '/classes/class.p3-profiler-table.php' );
			break;
		case 'P3_Profiler_Plugin' :
			require_once( P3_PATH . '/classes/class.p3-profiler-plugin.php' );
			break;
		case 'P3_Profiler_Plugin_Admin' :
			require_once( P3_PATH . '/classes/class.p3-profiler-plugin-admin.php' );
			break;
		case 'P3_Profiler_No_Data_Exception' :
			require_once( P3_PATH . '/exceptions/class.p3-profiler-no-data-exception.php' );
			break;
	}
}
