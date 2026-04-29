<?php

namespace Imagely\NGG\Settings;

use Imagely\NGG\Util\Serializable;

/**
 * Global settings manager for network-level settings.
 */
class GlobalSettings extends ManagerBase {

	/**
	 * Singleton instance.
	 *
	 * @var GlobalSettings|null
	 */
	public static $_instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return GlobalSettings
	 */
	public static function get_instance() {
		if ( \is_null( self::$_instance ) ) {
			self::$_instance = new GlobalSettings();
		}
		return self::$_instance;
	}

	/**
	 * Save settings to the database.
	 *
	 * @return bool Whether the save was successful.
	 */
	public function save() {
		return \update_site_option( self::$option_name, $this->to_array() );
	}

	/**
	 * Load settings from the database.
	 */
	public function load() {
		$this->_options = \get_site_option( self::$option_name, $this->to_array() );

		if ( ! $this->_options ) {
			$this->_options = [];
		} elseif ( \is_string( $this->_options ) ) {
			$this->_options = Serializable::unserialize( $this->_options );
		}
	}

	/**
	 * Delete settings from the database.
	 *
	 * @return bool Whether the delete was successful.
	 */
	public function destroy() {
		return \delete_site_option( self::$option_name );
	}
}
