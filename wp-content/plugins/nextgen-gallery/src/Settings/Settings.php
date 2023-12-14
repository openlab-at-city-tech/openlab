<?php

namespace Imagely\NGG\Settings;

use Imagely\NGG\Util\Serializable;

class Settings extends ManagerBase {

	protected static $instance = null;

	/**
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

	public function save() {
		return \update_option( self::$option_name, $this->to_array() );
	}

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

	public function destroy() {
		\delete_option( self::$option_name );
	}
}

class Ajax_URL_Option_Handler {

	public function get( $key, $default = null ) {
		$retval = $default;

		if ( $key == 'ajax_url' ) {
			$retval = site_url( '/index.php?' . NGG_AJAX_SLUG . '=1' );
			if ( is_ssl() && strpos( $retval, 'https' ) === false ) {
				$retval = str_replace( 'http', 'https', $retval );
			}
		}

		return $retval;
	}
}
