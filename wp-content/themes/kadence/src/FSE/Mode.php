<?php
/**
 * Kadence\FSE\Mode class
 *
 * @package kadence
 */

namespace Kadence\FSE;

/**
 * Reads and writes the per-site Full Site Editing mode.
 *
 * @since 1.6.0
 */
final class Mode {

	/**
	 * Option that stores the mode.
	 *
	 * @since 1.6.0
	 */
	private const OPTION = 'kadence_fse_mode';

	/**
	 * Option value when the mode is on.
	 *
	 * @since 1.6.0
	 */
	private const ENABLED = 'enabled';

	/**
	 * Option value when the mode was turned off.
	 *
	 * @since 1.6.0
	 */
	private const DISABLED = 'disabled';

	/**
	 * Oldest WordPress the mode runs on, for the responsive block styles it builds on.
	 *
	 * @since 1.6.0
	 */
	private const MINIMUM_WP_VERSION = '7.1';

	/**
	 * Whether this site's WordPress can run the mode.
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	public function is_supported(): bool {
		return is_wp_version_compatible( self::MINIMUM_WP_VERSION );
	}

	/**
	 * Oldest WordPress version the mode runs on.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	public function minimum_wp_version(): string {
		return self::MINIMUM_WP_VERSION;
	}

	/**
	 * Whether the mode is on for this site.
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		return $this->is_supported() && self::ENABLED === get_option( self::OPTION );
	}

	/**
	 * Whether the site has turned the mode on at some point, whether or not it is on now.
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	public function was_enabled(): bool {
		return in_array( get_option( self::OPTION ), [ self::ENABLED, self::DISABLED ], true );
	}

	/**
	 * Turns the mode on.
	 *
	 * @since 1.6.0
	 *
	 * @return bool True when the mode is on after the call.
	 */
	public function enable(): bool {
		if ( ! $this->is_supported() ) {
			return false;
		}

		if ( self::ENABLED === get_option( self::OPTION ) ) {
			return true;
		}

		if ( ! update_option( self::OPTION, self::ENABLED, true ) ) {
			return false;
		}

		/**
		 * Fires after Full Site Editing mode is turned on.
		 *
		 * Runs right after the setting is saved, and only when it changes. Kadence\is_fse_mode()
		 * already returns the new mode, while wp_is_block_theme() and the hooks the theme added
		 * for the old mode stay until the next request.
		 *
		 * @since 1.6.0
		 */
		do_action( 'kadence_fse_mode_enabled' );

		return true;
	}

	/**
	 * Turns the mode off.
	 *
	 * @since 1.6.0
	 *
	 * @return bool True when the mode is off after the call.
	 */
	public function disable(): bool {
		if ( self::ENABLED !== get_option( self::OPTION ) ) {
			return true;
		}

		if ( ! update_option( self::OPTION, self::DISABLED, true ) ) {
			return false;
		}

		/**
		 * Fires after Full Site Editing mode is turned off.
		 *
		 * Runs right after the setting is saved, and only when it changes. Kadence\is_fse_mode()
		 * already returns the new mode, while wp_is_block_theme() and the hooks the theme added
		 * for the old mode stay until the next request.
		 *
		 * @since 1.6.0
		 */
		do_action( 'kadence_fse_mode_disabled' );

		return true;
	}
}
