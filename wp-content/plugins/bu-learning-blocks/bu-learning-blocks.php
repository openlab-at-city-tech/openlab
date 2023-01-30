<?php
/**
 * Plugin Name: BU Learning Blocks
 * Plugin URI: https://github.com/bu-ist/bu-learning-blocks
 * Description: BU Learning Blocks — is a collection of tools to enable the easy creation of academic lessons with embedded self-assessment questions.
 * Requires at least: 5.3.2
 * Requires PHP: 7.0
 * Author: Boston University: Web Applications
 * Author URI: http://www.bu.edu/
 * Text Domain: bu-learning-blocks
 * Domain Path: /languages
 * Version: 1.1.4
 * License: GPL2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package BU Learning Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'BULB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'BULB_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'BULB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'BULB_PLUGIN_FILE_PATH', __FILE__ );

/**
 * Displays admin notice and prevents activation.
 *
 * Deactivates plugin if the function to register blocks does not exist
 * (meaning this is not a WP 5.0.0 install, or the site does not have the
 * Gutenberg plugin activated.
 *
 * @since    0.0.2
 */
function bulb_gutenberg_notice() {
	?>
		<div class="notice notice-error is-dismissible">
			<p>
				<?php esc_html_e( 'BULB Error: BU Learning Blocks requires either WordPress 5.0.0, or the Gutenberg plugin to be installed and activated on any version previous to 5.0.0.', 'bu-learning-blocks' ); ?>
			</p>
			<p>
				<?php esc_html_e( 'Please install and activate the Gutenberg plugin to use BU Learning Blocks.', 'bu-learning-blocks' ); ?>
			</p>
		</div>
	<?php
	deactivate_plugins( BULB_PLUGIN_BASENAME );
}

/**
 * BULB Activation Hook registration
 *
 * All the activation checks needed to ensure BULB is ready for use
 *
 * @since 0.0.6
 */
function bulb_activate() {
	if ( false === get_option( 'bulb_active', false ) ) {
		update_option( 'bulb_cpt_install_dialog', 1 );
	}
	update_option( 'bulb_active', 1 );
}
register_activation_hook( BULB_PLUGIN_FILE_PATH, 'bulb_activate' );

/**
 * BULB De-activation Hook registration
 *
 * All the de-activation checks needed to ensure BULB is properly de-activated
 *
 * @since 0.0.6
 */
function bulb_deactivate() {
	delete_option( 'bulb_active' );
	delete_option( 'bulb_cpt_install' );
}
register_deactivation_hook( BULB_PLUGIN_FILE_PATH, 'bulb_deactivate' );

/**
 * Initializes plugin on plugins_loaded.
 *
 * Waits for plugins_loaded hook to properly call function_exists.
 *
 * @since    0.0.2
 */
function bulb_init_plugin() {
	global $bu_navigation_plugin;

	// Include the BU Navigation core and widget, if BU Navigation isn't already available.
	if ( ! $bu_navigation_plugin ) {
		require __DIR__ . '/inc/bu-navigation-core-widget/src/class-navigation-widget.php';
		require __DIR__ . '/inc/bu-navigation-core-widget/src/data-active-section.php';
		require __DIR__ . '/inc/bu-navigation-core-widget/src/data-format.php';
		require __DIR__ . '/inc/bu-navigation-core-widget/src/data-get-urls.php';
		require __DIR__ . '/inc/bu-navigation-core-widget/src/data-model.php';
		require __DIR__ . '/inc/bu-navigation-core-widget/src/data-nav-labels.php';
		require __DIR__ . '/inc/bu-navigation-core-widget/src/data-widget.php';
		require __DIR__ . '/inc/bu-navigation-core-widget/src/filters.php';

		// At BU this is defined in wp-config.php, so it must be independently declared.
		define( 'BU_NAVIGATION_LINK_POST_TYPE', 'link' );

		add_action( 'widgets_init', function() {
			register_widget( 'BU\Plugins\Navigation\Navigation_Widget' );
		});

		/**
		 * If BU Navigation isn't loaded, then declare a bu_navigation_supported_post_types function
		 * that only returns the BULB custom post type
		 *
		 * The navigation widget will call this funtion to determine which posts should appear in the widget.
		 * Absent the full BU Navigation widget, the built in widget should only display BULB posts.
		 *
		 * @return array Array of one value, the BULB custom post type
		 */
		function bu_navigation_supported_post_types() {
			return [ 'bulb-learning-module' ];
		}
	}

	// Only targets WordPress versions before 5.0, that don't have gutenberg activated.
	if ( ! function_exists( 'register_block_type' ) ) {
		add_action( 'admin_notices', 'bulb_gutenberg_notice' );
		return;
	}

	/**
	 * Block Initializer.
	 */
	require_once BULB_PLUGIN_DIR_PATH . 'src/init.php';
}
add_action( 'plugins_loaded', 'bulb_init_plugin' );
