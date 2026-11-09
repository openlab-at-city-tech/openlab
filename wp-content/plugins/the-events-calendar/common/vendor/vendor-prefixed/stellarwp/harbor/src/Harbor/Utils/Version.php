<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Utils;

use TEC\Common\LiquidWeb\Harbor\Config;
use TEC\Common\LiquidWeb\Harbor\Harbor;

/**
 * Cross-instance version leadership utility.
 *
 * When multiple vendor-prefixed copies of Harbor are active, only the
 * highest version should own shared responsibilities (admin page, REST
 * routes, etc.). This class centralizes that check using the global
 * _lw_harbor_instance_registry() function as the cross-copy registry.
 *
 * @since 1.0.0
 */
class Version {

	/**
	 * Whether this instance has claimed at least one leadership responsibility.
	 *
	 * Set to true the first time should_handle() succeeds. Stored as a static
	 * on this (Strauss-prefixed) class, so each vendor copy tracks its own state.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	private static $claimed_leadership = false;

	/**
	 * Determines whether this Harbor instance is the highest active version.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_highest(): bool {
		return self::is_highest_among( array_keys( _lw_harbor_instance_registry() ) );
	}

	/**
	 * Determines whether this Harbor instance is the highest among the given versions.
	 *
	 * @since 1.0.0
	 *
	 * @param string[] $versions All registered version strings.
	 *
	 * @return bool
	 */
	public static function is_highest_among( array $versions ): bool {
		$highest = array_reduce(
			$versions,
			static function ( string $carry, string $v ): string {
				return version_compare( $v, $carry, '>' ) ? $v : $carry;
			},
			Harbor::VERSION
		);

		return ! version_compare( Harbor::VERSION, $highest, '<' );
	}

	/**
	 * Determines whether this Harbor instance should handle the given
	 * action, and if so, claims it so no other instance can.
	 *
	 * @since 1.0.0
	 *
	 * @param string $action A short, unique identifier for the responsibility
	 *                       (e.g. 'admin_page', 'rest_routes').
	 *
	 * @return bool True if this instance should handle the action.
	 */
	public static function should_handle( string $action ): bool {
		if ( ! self::is_highest() ) {
			return false;
		}

		$hook = 'lw-harbor/handled/' . $action;

		if ( did_action( $hook ) ) {
			return false;
		}

		do_action( $hook );

		self::$claimed_leadership = true;

		return true;
	}

	/**
	 * Returns whether this instance has claimed at least one leadership responsibility.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_leader(): bool {
		return self::$claimed_leadership;
	}

	/**
	 * Registers an admin_footer hook to print leader debug info when WP_DEBUG is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function register_debug_info(): void {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			add_action(
				'admin_footer',
				static function () {
					if ( ! Version::is_leader() ) {
						return;
					}
					$data = [
						'lw-harbor' => [
							'leader'  => Config::get_plugin_basename(),
							'version' => Harbor::VERSION,
						],
					];

					echo '<script>console.log(' . wp_json_encode( $data ) . ');</script>';
				}
			);
		}
	}
}
