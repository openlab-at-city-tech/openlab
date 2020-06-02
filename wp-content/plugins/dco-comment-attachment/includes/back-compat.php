<?php
/**
 * Functions to ensure compatibility with WordPress 4.6 and higher.
 *
 * @package DCO_Comment_Attachment
 * @author Denis Yanchevskiy
 * @copyright 2019
 * @license GPLv2+
 *
 * @since 1.0.0
 */

if ( ! function_exists( 'wp_get_additional_image_sizes' ) ) {
	// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound, WordPress.WP.GlobalVariablesOverride.Prohibited
	/**
	 * Retrieve additional image sizes.
	 *
	 * @since 4.7.0
	 *
	 * @global array $_wp_additional_image_sizes
	 *
	 * @return array Additional images size data.
	 */
	function wp_get_additional_image_sizes() {
		global $_wp_additional_image_sizes;
		if ( ! $_wp_additional_image_sizes ) {
			$_wp_additional_image_sizes = array();
		}
		return $_wp_additional_image_sizes;
	}
	// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound, WordPress.WP.GlobalVariablesOverride.Prohibited
}
