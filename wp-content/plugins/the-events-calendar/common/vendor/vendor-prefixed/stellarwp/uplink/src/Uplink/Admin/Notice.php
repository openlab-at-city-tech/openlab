<?php

namespace TEC\Common\StellarWP\Uplink\Admin;

use TEC\Common\StellarWP\Uplink\Config;
use TEC\Common\StellarWP\Uplink\Messages\Expired_Key;
use TEC\Common\StellarWP\Uplink\Messages\Unlicensed;

class Notice {

	const INVALID_KEY = 'invalid_key';
	const UPGRADE_KEY = 'upgrade_key';
	const EXPIRED_KEY = 'expired_key';
	const STORE_KEY   = 'stellarwp_uplink_key_notices';

	/**
	 * @var array<mixed>
	 */
	protected $saved_notices = [];

	/**
	 * @var array<mixed>
	 */
	protected $notices = [];

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup_notices() {
		if ( empty( $this->notices ) ) {
			return;
		}

		foreach ( $this->notices as $notice_type => $plugin ) {
			$message = null;

			switch ( $notice_type ) {
				case self::EXPIRED_KEY:
					$message = new Expired_Key();
					break;
				case self::INVALID_KEY:
					$message = new Unlicensed();
					break;
			}

			if ( empty( $message ) ) {
				continue;
			}

			echo $message;
		}
	}

	/**
	 * @since 1.0.0
	 *
	 * @param string $plugin_name
	 *
	 * @param string $notice_type
	 *
	 * @return void
	 */
	public function add_notice( string $notice_type, string $plugin_name ) {
		$this->clear_notices( $plugin_name, true );
		$this->notices[ $notice_type ][ $plugin_name ] = true;
		$this->save_notices();
	}

	/**
	 * Removes any notifications for the specified plugin.
	 *
	 * Useful when a valid license key is detected for a plugin, where previously
	 * it might have been included under a warning notification.
	 *
	 * If the optional second param is set to true then this change will not
	 * immediately be committed to storage (useful if we know this will happen in
	 * any case later on in the same request).
	 *
	 * @param string $plugin_name
	 * @param bool   $defer_saving_change = false
	 *
	 * @return void
	 */
	public function clear_notices( string $plugin_name, bool $defer_saving_change = false ) {
		foreach ( $this->notices as $notice_type => &$list_of_plugins ) {
			unset( $list_of_plugins[ $plugin_name ] );
		}

		if ( ! $defer_saving_change ) {
			$this->save_notices();
		}
	}

	/**
	 * Saves any license key notices already added.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function save_notices() {
		update_option( self::STORE_KEY, $this->notices );

		/**
		 * Fires after PUE license key notices have been saved.
		 *
		 * @param array $current_notices
		 * @param array $previously_saved_notices
		 */
		do_action( 'stellarwp/uplink/' . Config::get_hook_prefix() . '/notices_save_notices', $this->notices, $this->saved_notices );
	}

}
