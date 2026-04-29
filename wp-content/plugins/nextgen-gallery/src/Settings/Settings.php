<?php

namespace Imagely\NGG\Settings;

// phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound
use Imagely\NGG\Util\Serializable;

/**
 * Settings manager for blog-level settings.
 */
class Settings extends ManagerBase {

	/**
	 * Singleton instance.
	 *
	 * @var Settings|null
	 */
	protected static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return Settings
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new Settings();

			// This setting must be an option handler as it cannot be static.
			self::$instance->add_option_handler( '\Imagely\NGG\Settings\Ajax_URL_Option_Handler', [ 'ajax_url' ] );
		}
		return self::$instance;
	}

	/**
	 * Save settings.
	 *
	 * @return bool Whether the save was successful.
	 */
	public function save() {
		return \update_option( self::$option_name, $this->to_array() );
	}

	/**
	 * Load settings from database.
	 */
	public function load() {
		$this->_options = \get_option( self::$option_name, [] );

		if ( ! $this->_options ) {
			$this->_options = [];
		} elseif ( is_string( $this->_options ) ) {
			try {

				$this->_options = Serializable::unserialize( $this->_options );
			} catch ( \Exception $exception ) {
				$this->_options = [];
			}
		}
	}

	/**
	 * Delete settings from database.
	 */
	public function destroy() {
		\delete_option( self::$option_name );
	}
}

/**
 * Option handler for AJAX URL settings.
 *
 * Provides dynamic AJAX URL handling for the settings system.
 */
class Ajax_URL_Option_Handler {
 // phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound

	/**
	 * Get the AJAX URL.
	 *
	 * @param string $key The option key.
	 * @param mixed  $default_value The default value.
	 * @return mixed The option value.
	 */
	public function get( $key, $default_value = null ) {
		$retval = $default_value;

		if ( 'ajax_url' == $key ) {
			$retval = site_url( '/index.php?' . NGG_AJAX_SLUG . '=1' );
			if ( is_ssl() && strpos( $retval, 'https' ) === false ) {
				$retval = str_replace( 'http', 'https', $retval );
			}
		}

		return $retval;
	}
}
