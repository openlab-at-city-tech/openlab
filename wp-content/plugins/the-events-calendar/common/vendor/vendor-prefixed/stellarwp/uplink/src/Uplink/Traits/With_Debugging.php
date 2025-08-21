<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Traits;

trait With_Debugging {

	/**
	 * Determine if WP_DEBUG is enabled.
	 *
	 * @return bool
	 */
	protected function is_wp_debug(): bool {
		return defined( 'WP_DEBUG' ) && WP_DEBUG;
	}

}
