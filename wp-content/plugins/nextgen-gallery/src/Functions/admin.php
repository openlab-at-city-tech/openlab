<?php
/**
 * Admin Functions
 *
 * @since 3.5.0
 *
 * @package NextGen Gallery
 */

/**
 * Helper Method to load admin Partials
 *
 * @since 3.5.0
 *
 * @param string $template Template to load.
 *
 * @return bool
 */
function nextgen_load_admin_partial( $template ) {
	$dir = trailingslashit( trailingslashit( NGG_PLUGIN_DIR ) . 'src/Admin/Views' );

	if ( file_exists( $dir . $template . '.php' ) ) {

		require_once $dir . $template . '.php';
		return true;
	}

	return false;
}

/**
 * Helper method to check if starter, plus or pro is active.
 *
 * @since 3.5.0
 *
 * @return bool
 */
function nextgen_is_plus_or_pro_enabled() {
	return defined( 'NGG_PRO_PLUGIN_BASENAME' )
		|| defined( 'NGG_PLUS_PLUGIN_BASENAME' )
		|| defined( 'NGG_STARTER_PLUGIN_BASENAME' )
		|| is_multisite();
}


/**
 * Helper Method to Detect NGG Admin Page
 *
 * @since 3.5.0
 *
 * @return bool
 */
function is_nextgen_admin_page() {

	global $current_screen;

	if ( ! is_admin() ) {
		return false;
	}

	$keys = [ 'ngg', 'nggallery', 'nextgen-gallery', 'nextgen' ];

	foreach ( $keys as $key ) {
		$is_modern_page = str_contains( $current_screen->id, $key );
		if ( $is_modern_page ) {
			return true;
		}
	}

	return false;
}
