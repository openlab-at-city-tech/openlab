<?php
/**
 * BP Classic Loader.
 *
 * @package bp-classic\inc
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loader function.
 *
 * @since 1.0.0
 *
 * @param string $plugin_dir The plugin root directory.
 */
function bp_classic_includes( $plugin_dir = '' ) {
	$path = trailingslashit( $plugin_dir );

	// Core is always required.
	require $path . '/core/functions.php';
	require $path . '/core/widgets.php';

	// Members is always active.
	require $path . '/members/functions.php';
	require $path . '/members/widgets.php';

	if ( is_admin() ) {
		require $path . '/core/admin/functions.php';
		require $path . '/core/admin/slugs.php';

		// Members is always active.
		require $path . '/members/admin/functions.php';
	}

	require $path . '/core/filters.php';

	if ( bp_is_active( 'activity' ) ) {
		if ( is_admin() ) {
			require $path . '/activity/admin/functions.php';
		}

		require $path . '/activity/filters.php';
	}

	if ( bp_is_active( 'blogs' ) ) {
		require $path . '/blogs/functions.php';
		require $path . '/blogs/widgets.php';

		if ( is_admin() ) {
			require $path . '/blogs/admin/functions.php';
		}
	}

	if ( bp_is_active( 'friends' ) ) {
		require $path . '/friends/widgets.php';
	}

	if ( bp_is_active( 'groups' ) ) {
		require $path . '/groups/functions.php';
		require $path . '/groups/widgets.php';

		if ( is_admin() ) {
			require $path . '/groups/admin/functions.php';
		}
	}

	if ( bp_is_active( 'messages' ) ) {
		require $path . '/messages/widgets.php';
	}
}
add_action( '_bp_classic_includes', 'bp_classic_includes', 1, 1 );

/**
 * Only include the specific Template Pack file if the Theme does not support BuddyPress.
 *
 * @since 1.0.0
 */
function bp_classic_template_pack_includes() {
	if ( current_theme_supports( 'buddypress' ) ) {
		return;
	}

	// Do make sure BP Nouveau is the active BP Template Pack.
	if ( 'nouveau' === bp_get_theme_package_id() && function_exists( 'bp_nouveau' ) ) {
		require trailingslashit( plugin_dir_path( __FILE__ ) ) . 'templates/nouveau.php';
	}
}
add_action( 'bp_after_setup_theme', 'bp_classic_template_pack_includes', 1 );

/**
 * Specific compatibility functions for bbPress.
 *
 * @since 1.4.0
 */
function bp_classic_forums_includes() {
	require trailingslashit( plugin_dir_path( __FILE__ ) ) . 'forums/functions.php';
}
add_action( 'bbp_buddypress_loaded', 'bp_classic_forums_includes' );
