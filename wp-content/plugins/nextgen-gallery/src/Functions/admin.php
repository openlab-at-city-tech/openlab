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
 * Build a human-readable, uppercased list of the image formats accepted for upload
 * on the current server.
 *
 * The list is derived from the same source used for validation
 * (NGG_DEFAULT_ALLOWED_FILE_TYPES via the ngg_allowed_file_types filter), so WebP is
 * only included when the server can actually process it (GD compiled with WebP support).
 *
 * @since 4.2.4
 *
 * @return string Comma-separated uppercase format list, e.g. "JPEG, JPG, PNG, GIF, WEBP".
 */
function ngg_get_allowed_formats_label() {
	$extensions = apply_filters( 'ngg_allowed_file_types', NGG_DEFAULT_ALLOWED_FILE_TYPES );

	// The ngg_allowed_file_types filter normally returns an array, but guard against
	// a raw comma-separated string in case the filter is short-circuited elsewhere.
	if ( ! is_array( $extensions ) ) {
		$extensions = explode( ',', (string) $extensions );
	}

	// Drop the internal "_backup" pseudo-extension and any empty entries.
	$extensions = array_filter(
		array_map( 'trim', $extensions ),
		function ( $ext ) {
			return '' !== $ext && '_backup' !== $ext;
		}
	);

	return strtoupper( implode( ', ', $extensions ) );
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

	if ( ! $current_screen || empty( $current_screen->id ) ) {
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
