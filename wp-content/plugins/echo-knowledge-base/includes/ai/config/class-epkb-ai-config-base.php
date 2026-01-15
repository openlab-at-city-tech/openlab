<?php defined( 'ABSPATH' ) || exit();

/**
 * Base Configuration Class
 * 
 * Abstract base class for all configuration specifications in the plugin.
 * Provides common functionality for configuration management including:
 * - Default value retrieval
 * - Configuration caching
 * - Database operations
 * - Validation handling
 */
abstract class EPKB_AI_Config_Base {

	/**
	 * Option name for storing configuration in database
	 * Must be defined by child classes
	 */
	const OPTION_NAME = '';
	
	/**
	 * Cached configuration data
	 * @var array|null
	 */
	protected static $cached_config = array();

	/**
	 * Get configuration field specifications
	 * Must be implemented by child classes
	 *
	 * @return array Field specifications
	 */
	abstract public static function get_config_fields_specifications();

	/**
	 * Get default configuration values
	 *
	 * @return array Default configuration
	 */
	public static function get_default_config() {
		$default_config = array();

		$specs = static::get_config_fields_specifications();
		foreach ( $specs as $field_name => $field_spec ) {
			$default_config[ $field_name ] = static::get_field_default( $field_name );
		}
		
		return $default_config;
	}

	/**
	 * Get default value for a specific field
	 *
	 * @param string $field_name Configuration field name
	 * @return mixed Default value for the field or empty string if not defined
	 */
	public static function get_field_default( $field_name ) {
		$specs = static::get_config_fields_specifications();
		return isset( $specs[ $field_name ]['default'] ) ? $specs[ $field_name ]['default'] : '';
	}

	/**
	 * Get a specific configuration value
	 *
	 * @param string $field_name Configuration field name
	 * @param mixed $default Default value if not found or default from specs
	 * @return mixed
	 */
	public static function get_config_value( $field_name, $default = null ) {
		$config = static::get_config();
		
		// If field exists in config, return it
		if ( isset( $config[ $field_name ] ) ) {
			return $config[ $field_name ];
		}
		
		// If no default was supplied, get default from field specifications
		if ( $default === null ) {
			return static::get_field_default( $field_name );
		}
		
		return $default;
	}

	/**
	 * Get configuration from database
	 *
	 * @return array
	 */
	public static function get_config() {
		
		$option_name = static::OPTION_NAME;
		
		// Validate option name is defined
		if ( empty( $option_name ) ) {
			return static::get_default_config();
		}
		
		// Return cached configuration if available
		if ( isset( self::$cached_config[ $option_name ] ) && self::$cached_config[ $option_name ] !== null ) {
			return self::$cached_config[ $option_name ];
		}
		
		// Get the configuration from WordPress options
		$config = get_option( $option_name, null );
		$default_config = static::get_default_config();

		// If not found, return default configuration
		if ( $config === null ) {
			// Save default configuration to database
			update_option( $option_name, $default_config, true ); // true for autoload
			$config = $default_config;
		}
		
		// Ensure all fields exist with proper defaults
		$config = wp_parse_args( $config, $default_config );
		
		// Cache the configuration
		self::$cached_config[ $option_name ] = $config;
		
		return $config;
	}

	/**
	 * Get field options dynamically (for fields that need late loading)
	 * Can be overridden by child classes
	 *
	 * @param string $field_name
	 * @return array
	 */
	public static function get_field_options( $field_name ) {
		$specs = static::get_config_fields_specifications();
		return isset( $specs[ $field_name ]['options'] ) ? $specs[ $field_name ]['options'] : array();
	}

	/**
	 * Update a specific configuration value
	 *
	 * @param string $field_name Configuration field name
	 * @param mixed $value New value
	 * @return bool|WP_Error
	 */
	public static function update_config_value( $field_name, $value ) {
		$update_data = array( $field_name => $value );
		$result = static::update_config( $update_data );

		return is_wp_error( $result ) ? $result : true;
	}

	/**
	 * Update configuration in database
	 *
	 * @param array $new_config New configuration values
	 * @return array|WP_Error Updated configuration or error
	 */
	public static function update_config( $new_config ) {

		$option_name = static::OPTION_NAME;
		
		// Validate option name is defined
		if ( empty( $option_name ) ) {
			return new WP_Error( 'invalid_option_name', __( 'Configuration option name not defined', 'echo-knowledge-base' ) );
		}

		// Get current configuration
		$current_config = static::get_config();
		
		// Validate and sanitize new configuration
		$validated_config = static::sanitize_config( $new_config, $current_config );
		if ( is_wp_error( $validated_config ) ) {
			return $validated_config;
		}

		// return if no change in configuration detected
		$old_value = get_option( $option_name );
		if ( $validated_config === $old_value || maybe_serialize( $validated_config ) === maybe_serialize( $old_value ) ) {
			return $validated_config;
		}

		// Save to database with autoload enabled
		$result = update_option( $option_name, $validated_config, true );
		if ( ! $result ) {
			return new WP_Error( 'save_failed', __( 'Failed to save configuration', 'echo-knowledge-base' ) );
		}

		// Clear WordPress object cache
		wp_cache_delete( $option_name, 'options' );

		// Update our static cache with the new validated config
		self::$cached_config[ $option_name ] = $validated_config;
		
		return $validated_config;
	}

	/**
	 * Clear cached configuration
	 */
	public static function clear_cache() {
		$option_name = static::OPTION_NAME;
		if ( ! empty( $option_name ) && isset( self::$cached_config[ $option_name ] ) ) {
			unset( self::$cached_config[ $option_name ] );
		}
	}

	/**
	 * Sanitize configuration data based on field specifications
	 *
	 * @param array $config Configuration data to sanitize
	 * @param array|null $current_config Current configuration for fields not provided in $config
	 * @param array|null $specs Field specifications (will use get_config_fields_specifications() if not provided)
	 * @return array|WP_Error Sanitized configuration or error
	 */
	protected static function sanitize_config( $config, $current_config = null, $specs = null ) {
		
		// Validate input is array
		if ( ! is_array( $config ) ) {
			return new WP_Error( 'invalid_config', __( 'Configuration must be an array', 'echo-knowledge-base' ) );
		}
		
		if ( $specs === null ) {
			$specs = static::get_config_fields_specifications();
		}
		
		if ( $current_config === null ) {
			$current_config = array();
		}
		
		$validated_config = array();
		$input_filter = new EPKB_Input_Filter();
		
		foreach ( $specs as $field_name => $field_spec ) {
			
			// Skip internal fields unless explicitly provided
			if ( isset( $field_spec['internal'] ) && $field_spec['internal'] && ! isset( $config[ $field_name ] ) ) {
				$default_value = static::get_field_default( $field_name );
				$validated_config[ $field_name ] = isset( $current_config[ $field_name ] ) ? $current_config[ $field_name ] : $default_value;
				continue;
			}
			
			// Use new value if provided, otherwise keep current value
			if ( isset( $config[ $field_name ] ) ) {
				$value = $config[ $field_name ];
				
				// For fields with dynamic options, populate them now
				if ( isset( $field_spec['type'] ) && $field_spec['type'] === EPKB_Input_Filter::CHECKBOXES_MULTI_SELECT && empty( $field_spec['options'] ) ) {
					$field_spec['options'] = static::get_field_options( $field_name );
				}
				
				// Validate based on field type
				$validated_value = $input_filter->filter_input_field( $value, $field_spec );
				if ( is_wp_error( $validated_value ) ) {
					$error_message = $validated_value->get_error_message();
					$field_label = isset( $field_spec['label'] ) ? $field_spec['label'] : $field_name;
					return new WP_Error( 'validation_failed', sprintf( __( 'Validation failed for %s: %s', 'echo-knowledge-base' ), $field_label, $error_message ) );
				}
				
				$validated_config[ $field_name ] = $validated_value;
			} else {
				$default_value = static::get_field_default( $field_name );
				$validated_config[ $field_name ] = isset( $current_config[ $field_name ] ) ? $current_config[ $field_name ] : $default_value;
			}
		}
		
		return $validated_config;
	}

	/**
	 * Sanitize a single field value based on its type specification
	 *
	 * @param mixed $value
	 * @param array $field_spec
	 * @return mixed
	 */
	public static function sanitize_field_value( $value, $field_spec ) {
		$type = isset( $field_spec['type'] ) ? $field_spec['type'] : EPKB_Input_Filter::TEXT;

		switch ( $type ) {
			case EPKB_Input_Filter::CHECKBOX:
			case EPKB_Input_Filter::RADIO:
				return in_array( $value, array( 'on', 'off' ) ) ? $value : 'off';
				
			case EPKB_Input_Filter::NUMBER:
				return intval( $value );
				
			case EPKB_Input_Filter::TEXT:
				return sanitize_text_field( $value );
				
			case EPKB_Input_Filter::URL:
				return esc_url_raw( $value );
				
			case EPKB_Input_Filter::EMAIL:
				return sanitize_email( $value );
				
			case EPKB_Input_Filter::ENUMERATION:
				$allowed = array();
				if ( isset( $field_spec['options'] ) && is_array( $field_spec['options'] ) ) {
					$allowed = array_keys( $field_spec['options'] );
				}
				// Note: Can't use static::get_field_default() here since we only have field_spec, not field_name
				$default_value = isset( $field_spec['default'] ) ? $field_spec['default'] : '';
				return in_array( $value, $allowed ) ? $value : $default_value;
				
			case EPKB_Input_Filter::CHECKBOXES_MULTI_SELECT:
			case EPKB_Input_Filter::CHECKBOXES_MULTI_SELECT_NOT:
			case EPKB_Input_Filter::INTERNAL_ARRAY:
				// Ensure value is an array
				if ( ! is_array( $value ) ) {
					return array();
				}
				
				// Sanitize each value in the array
				$sanitized = array();
				foreach ( $value as $item ) {
					$sanitized[] = sanitize_text_field( $item );
				}
				
				return $sanitized;
				
			default:
				return sanitize_text_field( $value );
		}
	}
}