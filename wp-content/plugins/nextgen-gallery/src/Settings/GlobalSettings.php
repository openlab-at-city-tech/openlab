<?php

namespace Imagely\NGG\Settings;

use Imagely\NGG\Util\Serializable;

class GlobalSettings extends ManagerBase {

	public static $_instance = null;

	/**
	 * @return GlobalSettings
	 */
	public static function get_instance() {
		if ( \is_null( self::$_instance ) ) {
			self::$_instance = new GlobalSettings();
		}
		return self::$_instance;
	}

	public function save() {
		return \update_site_option( self::$option_name, $this->to_array() );
	}

	public function load() {
		$this->_options = \get_site_option( self::$option_name, $this->to_array() );

		if ( ! $this->_options ) {
			$this->_options = [];
		} elseif ( \is_string( $this->_options ) ) {
			$this->_options = Serializable::unserialize( $this->_options );
		}
	}

	public function destroy() {
		return \delete_site_option( self::$option_name );
	}
}
