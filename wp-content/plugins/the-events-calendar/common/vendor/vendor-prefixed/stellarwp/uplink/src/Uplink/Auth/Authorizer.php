<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Auth;

use TEC\Common\StellarWP\Uplink\Config;

/**
 * Determines if the current site will allow the user to use the authorize button.
 */
final class Authorizer {

	/**
	 * Checks if the current user can perform an action.
	 *
	 * @throws \RuntimeException
	 *
	 * @return bool
	 */
	public function can_auth(): bool {
		/**
		 * Filters if the current user can perform an action.
		 *
		 * @since 2.0.0
		 *
		 * @param bool $can_auth Whether the current user can perform an action.
		 */
		return (bool) apply_filters(
			'stellarwp/uplink/' . Config::get_hook_prefix() . '/auth/can_auth',
			is_super_admin()
		);
	}

}
