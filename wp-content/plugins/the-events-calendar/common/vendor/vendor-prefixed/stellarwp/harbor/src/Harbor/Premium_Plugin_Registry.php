<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor;

use TEC\Common\LiquidWeb\Harbor\Utils\Cast;

/**
 * Queries the cross-instance premium plugin existence registry.
 *
 * Premium plugins register a callback via the
 * lw_harbor/premium_plugin_exists filter that returns true when
 * they should be considered active and Harbor should be initialized.
 *
 * @since 1.2.0
 */
class Premium_Plugin_Registry {

	/**
	 * Whether at least one registered callback reports an active premium plugin.
	 *
	 * @since 1.2.0
	 *
	 * @return bool
	 */
	public function any(): bool {
		/**
		 * Filters whether a premium plugin exists.
		 *
		 * @since 1.2.0
		 *
		 * @return bool
		 */
		return Cast::to_bool( apply_filters( 'lw_harbor/premium_plugin_exists', false ) );
	}
}
