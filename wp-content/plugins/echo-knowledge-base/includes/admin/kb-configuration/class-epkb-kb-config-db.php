<?php

/**
 * Manage plugin configuration FOR CORE in the database.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Config_DB {

	// Prefix for WP option name that stores KB configuration
	const KB_CONFIG_PREFIX =  'epkb_config_';
	const DEFAULT_KB_ID = 1;

	private $cached_settings = array();
	private $is_cached_all_kbs = false;

	/**
	 * Retrieve CONFIGURATION for all KNOWLEDGE BASES
	 * If none found then return default KB configuration.
	 *
	 * @param bool $skip_check - true if caller checks that values are valid and needs quick invocation
	 *
	 * @return array settings for all registered knowledge bases OR default config if none found
	 */
	public function get_kb_configs( $skip_check=false ) {

		// retrieve settings if already cached
		if ( ! empty( $this->cached_settings ) && $this->is_cached_all_kbs ) {
			if ( $skip_check ) {
				return $this->cached_settings;
			}
			$kb_options_checked = array();
			$data_valid = true;
			foreach( $this->cached_settings as $config ) {
				if ( empty( $config['id'] ) ) {
					$data_valid = false;
					break;
				}
				// use defaults for missing or empty fields
				$kb_id = $config['id'];
				$kb_options_checked[$kb_id] = wp_parse_args( $config, EPKB_KB_Config_Specs::get_default_kb_config( $kb_id ) );
			}
			if ( $data_valid && ! empty($kb_options_checked) && ! empty($kb_options_checked[self::DEFAULT_KB_ID]) ) {
				return $kb_options_checked;
			}
		}

		$kb_ids = $this->get_kb_ids( true );

		// unserialize options and use defaults if necessary
		$kb_options_checked = array();
		foreach ( $kb_ids as $kb_id ) {

			$kb_id = ( $kb_id === self::DEFAULT_KB_ID ) ? $kb_id : EPKB_Utilities::sanitize_get_id( $kb_id );
			if ( is_wp_error( $kb_id ) ) {
				continue;
			}

			$config = $this->get_wordpress_option( $kb_id );
			if ( empty( $config ) || ! is_array( $config ) || empty( $config['id'] ) ) {
				continue;
			}

			// use defaults for missing or empty fields
			$kb_options_checked[$kb_id] = wp_parse_args( $config, EPKB_KB_Config_Specs::get_default_kb_config( $kb_id ) );
			$kb_options_checked[$kb_id]['id'] = $kb_id;

			// filter kb config for Editor
			$kb_options_checked[$kb_id] = EPKB_Editor_Utilities::update_kb_from_editor_config( $kb_options_checked[$kb_id] );

			// cached the settings for future use
			$this->cached_settings[$kb_id] = $kb_options_checked[$kb_id];
		}

		$this->is_cached_all_kbs = ! empty( $kb_options_checked );

		// if no valid KB configuration found use default
		if ( empty( $kb_options_checked ) || ! isset( $kb_options_checked[self::DEFAULT_KB_ID] ) ) {
			$kb_options_checked[self::DEFAULT_KB_ID] = EPKB_KB_Config_Specs::get_default_kb_config( self::DEFAULT_KB_ID );
		}

		return $kb_options_checked;
	}

	/**
	 * Get IDs for all existing knowledge bases. If missing, return default KB ID
	 *
	 * @param bool $ignore_error
	 *
	 * @return array containing all existing KB IDs
	 */
	public function get_kb_ids( $ignore_error=false ) {
		/** @var $wpdb Wpdb */
		global $wpdb;

		// retrieve all KB option names for existing knowledge bases from WP Options table
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$kb_option_names = $wpdb->get_col( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE '" . self::KB_CONFIG_PREFIX . "%'" );
		if ( empty( $kb_option_names ) ) {
			if ( ! $ignore_error ) {
				EPKB_Logging::add_log( "Did not retrieve any kb config. Try to deactivate and active KB plugin to see if the issue will be fixed (11). Last error: " . $wpdb->last_error, $kb_option_names );
			}
		}

		$kb_ids = array();
		foreach ( $kb_option_names as $kb_option_name ) {

			if ( empty( $kb_option_name ) ) {
				continue;
			}

			$kb_id = str_replace( self::KB_CONFIG_PREFIX, '', $kb_option_name );
			$kb_id = EPKB_Utilities::sanitize_int( $kb_id, self::DEFAULT_KB_ID );
			$kb_ids[$kb_id] = $kb_id;
		}

		// at least include default KB ID
		if ( empty( $kb_ids ) || ! isset( $kb_ids[self::DEFAULT_KB_ID] ) ) {
			$kb_ids[self::DEFAULT_KB_ID] = self::DEFAULT_KB_ID;
		}

		return $kb_ids;
	}

	/**
	 * GET KB configuration from the WP Options table. If not found then return ERROR.
	 * Logs all errors so the caller does not need to.
	 *
	 * @param String $kb_id to get configuration for
	 * @param bool $return_error
	 * @return array|WP_Error return current KB configuration
	 */
	public function get_kb_config( $kb_id, $return_error=false ) {

		// always return error if kb_id invalid. we don't want to override stored KB config if there is an internal error that causes this
		$kb_id = ( $kb_id === self::DEFAULT_KB_ID ) ? $kb_id : EPKB_Utilities::sanitize_get_id( $kb_id );
		if ( is_wp_error( $kb_id ) ) {
			return $return_error ? $kb_id : EPKB_KB_Config_Specs::get_default_kb_config( self::DEFAULT_KB_ID );
		}
		/** @var int $kb_id */

		// retrieve settings if already cached
		if ( ! empty( $this->cached_settings[$kb_id] ) ) {
			$config = wp_parse_args( $this->cached_settings[$kb_id], EPKB_KB_Config_Specs::get_default_kb_config( $kb_id ) );
			$config['id'] = $kb_id;
			// filter kb config for Editor 
			return EPKB_Editor_Utilities::update_kb_from_editor_config( $config );
		}

		$config = $this->get_wordpress_option( $kb_id );

		// if KB configuration is missing then return error
		if ( empty( $config ) || ! is_array( $config ) ) {
			return $return_error ? new WP_Error('DB231', "Did not find KB configuration. Try to deactivate and reactivate KB plugin to see if this fixes the issue. " . EPKB_Utilities::contact_us_for_support() )
								 : EPKB_KB_Config_Specs::get_default_kb_config( self::DEFAULT_KB_ID );
		}

		// use defaults for missing or empty fields
		$config = wp_parse_args( $config, EPKB_KB_Config_Specs::get_default_kb_config( $kb_id ) );
		$config['id'] = $kb_id;

		// filter kb config for Editor
		$config = EPKB_Editor_Utilities::update_kb_from_editor_config( $config );

		// cached the settings for future use
		$this->cached_settings[$kb_id] = $config;

		return $config;
	}

	/**
	 * Get KB configuration
	 * @param $kb_id
	 * @return array|mixed|string|empty
	 */
	private function get_wordpress_option( $kb_id ) {
		/** @var $wpdb Wpdb */
		global $wpdb;

		$option_name = self::KB_CONFIG_PREFIX . $kb_id;

		// retrieve KB configuration from WP Options table
		$wp_config = get_option( $option_name );
		if ( empty( $wp_config ) || ! is_array( $wp_config ) || empty( $wp_config['id'] ) ) {
			$wp_config = false;
		}

		// return found KB configuration
		if ( ! empty( $wp_config ) ) {
			return $wp_config;
		}

		// fall back - retrieve KB settings directly from the database
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$config = $wpdb->get_var( "SELECT option_value FROM $wpdb->options WHERE option_name = '" . $option_name . "'" );
		if ( empty( $config ) ) {
			return [];
		}

		return maybe_unserialize( $config );
	}

	/**
	 * GET KB configuration from the WP Options table. If not found then return default.
	 *
	 * @param String $kb_id to get configuration for
	 * @return array return current KB configuration
	 */
	public function get_kb_config_or_default( $kb_id ) {

		$kb_config = $this->get_kb_config( $kb_id );
		if ( empty( $kb_config ) || ! is_array( $kb_config ) || is_wp_error( $kb_config ) ) {
			return EPKB_KB_Config_Specs::get_default_kb_config( $kb_id );
		}

		return $kb_config;
	}

	/**
	 * GET CURRENT KB CONFIGURATION from the WP Options table. Return default if not found.
	 *
	 * @return array - return current KB configuration or default if not found
	 */
	public function get_current_kb_configuration() {

		// get ID based on currently selected KB post type
		$kb_id = EPKB_KB_Handler::get_current_kb_id();
		if ( empty( $kb_id ) ) {
			return EPKB_KB_Config_Specs::get_default_kb_config( self::DEFAULT_KB_ID );
		}

		return self::get_kb_config( $kb_id );
	}

	/**
	 * Return specific value from the KB configuration. Values are automatically trimmed.
	 *
	 * @param string $kb_id
	 * @param $setting_name
	 * @param string $default
	 * @return string|array with value or specs $default value if this settings not found
	 */
	public function get_value( $kb_id, $setting_name, $default = '' ) {

		if ( empty( $setting_name ) ) {
			return $default;
		}

		$kb_config = empty( $kb_id ) ? $this->get_current_kb_configuration() : $this->get_kb_config( $kb_id );

		if ( isset( $kb_config[$setting_name] ) ) {
			return $kb_config[$setting_name];
		}

		$default_settings = EPKB_KB_Config_Specs::get_default_kb_config( self::DEFAULT_KB_ID );

		return isset( $default_settings[$setting_name] ) ? $default_settings[$setting_name] : $default;
	}

	/**
	 * Set specific value in KB Configuration
	 *
	 * @param $kb_id
	 * @param $key
	 * @param $value
	 * @return array|WP_Error
	 */
	public function set_value( $kb_id, $key, $value ) {

		$kb_config = $this->get_kb_config( $kb_id, true );
		if ( is_wp_error( $kb_config ) ) {
			return $kb_config;
		}

		$kb_config[$key] = $value;

		return $this->update_kb_configuration( $kb_id, $kb_config );
    }

	/**
	 * Update KB Configuration. Use default if config missing.
	 *
	 * @param int $kb_id is identification of the KB to update
	 * @param array $config contains KB configuration or empty if adding default configuration
	 *
	 * @return array|WP_Error configuration that was updated
	 */
	public function update_kb_configuration( $kb_id, array $config ) {

		$kb_id = ( $kb_id === self::DEFAULT_KB_ID ) ? $kb_id : EPKB_Utilities::sanitize_get_id( $kb_id );
		if ( is_wp_error( $kb_id ) ) {
			return $kb_id;
		}
		/** @var int $kb_id */

		$fields_specification = EPKB_KB_Config_Specs::get_fields_specification( $kb_id );
		$input_filter = new EPKB_Input_Filter();
		$sanitized_config = $input_filter->validate_and_sanitize_specs( $config, $fields_specification );
		if ( is_wp_error( $sanitized_config ) ) {
			return $sanitized_config;
		}

		// use defaults for missing configuration
		$sanitized_config = wp_parse_args( $sanitized_config, EPKB_KB_Config_Specs::get_default_kb_config( $kb_id ) );

		return $this->save_kb_config( $sanitized_config, $kb_id );
	}

	/**
	 * Insert or update KB configuration
	 *
	 * @param array $config
	 * @param $kb_id - assuming it is a valid ID (sanitized)
	 *
	 * @return array|WP_Error if configuration is missing or cannot be serialized
	 */
	private function save_kb_config( array $config, $kb_id ) {

		if ( empty( $config ) ) {
			return new WP_Error( 'save_kb_config', 'Configuration is empty' );
		}

		$config['id'] = $kb_id;  // ensure it is the same id
		$config = $this->save_wp_option( $kb_id, $config );
		if ( is_wp_error( $config ) ) {
			return $config;
		}

		// cached the settings for future use
		$this->cached_settings[$kb_id] = $config;

		return $config;
	}

	private function save_wp_option( $kb_id, $config ) {
		/** @var $wpdb Wpdb */
		global $wpdb;

		$option_name = self::KB_CONFIG_PREFIX . $kb_id;

		// return if no change in configuration detected
		$old_value = get_option( $option_name );
		if ( $config === $old_value || maybe_serialize( $config ) === maybe_serialize( $old_value ) ) {
			return $config;
		}

		// update configuration if possible
		$result = update_option( $option_name, $config );
		if ( $result !== false ) {
			return $config;
		}

		// return WP_Error on update_option() fail if WPML plugin or setting is active
		if ( EPKB_Utilities::is_wpml_plugin_active() || EPKB_Utilities::is_wpml_enabled( $config ) ) {
			return new WP_Error( 'save_kb_config', 'Configuration could not be saved' );
		}

		// add or update the option
		$serialized_config = maybe_serialize( $config );
		if ( empty( $serialized_config ) ) {
			return new WP_Error( 'save_kb_config', 'Failed to serialize kb config for kb_id ' . $kb_id );
		}

		$result = $wpdb->query( $wpdb->prepare( "INSERT INTO $wpdb->options (option_name, option_value, autoload) VALUES (%s, %s, %s)
 												 ON DUPLICATE KEY UPDATE option_name = VALUES(option_name), option_value = VALUES(option_value), autoload = VALUES(autoload)",
												$option_name, $serialized_config, 'no' ) );
		if ( $result === false ) {
			$wpdb_last_error = $wpdb->last_error;   // add_log changes last_error so store it first
			EPKB_Logging::add_log( 'Failed to update kb config for kb_id', $kb_id, 'Last DB ERROR: (' . $wpdb_last_error . ')' );
			return new WP_Error( 'save_kb_config', 'Failed to update kb config for kb_id ' . $kb_id . ' Last DB ERROR: (' . $wpdb_last_error . ')' );
		}

		return $config;
	}

	/**
	 * Multisite installation has to reset caching between installs.
	 */
	public function reset_cache() {
		$this->cached_settings = array();
		$this->is_cached_all_kbs = false;
	}
}