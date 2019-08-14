<?php

if ( ! function_exists( 'openlab_get_filesystem' ) ) {
	/**
	 * Retrieve the instance of the WP_Filesystem.
	 *
	 * @todo Extract as utility function.
	 *
	 * @return mixed An instance of WP_Filesystem_* depending on method.
	 */
	function openlab_get_filesystem() {
		global $wp_filesystem;

		if ( ! $wp_filesystem ) {
			// Make sure the WP_Filesystem function exists.
			if ( ! function_exists( 'WP_Filesystem' ) ) {
				require_once untrailingslashit( ABSPATH ) . '/wp-admin/includes/file.php';
			}

			WP_Filesystem();
		}

		return $wp_filesystem;
	}
}
