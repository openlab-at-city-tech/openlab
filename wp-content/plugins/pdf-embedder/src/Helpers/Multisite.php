<?php

namespace PDFEmbedder\Helpers;

/**
 * Helpful methods around the WordPress multisite functionality.
 *
 * @since 4.7.0
 */
class Multisite {

	/**
	 * Check if the site is Multisite and the plugin is network activated.
	 *
	 * @since 4.7.0
	 */
	public static function is_network_activated(): bool {

		if ( ! is_multisite() ) {
			return false;
		}

		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		return is_plugin_active_for_network( plugin_basename( PDFEMB_PLUGIN_FILE ) );
	}
}
