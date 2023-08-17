<?php
/**
 * The Option model
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\Core\Models
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\Core\Models;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\Core\Traits\Sanitize;

/**
 * Class Options
 *
 * @package WPMUDEV_BLC\Core\Models
 */
class Option extends Base {
	/**
	 * Use the Sanitize Trait.
	 *
	 * @since 2.0.0
	 */
	use Sanitize;

	/**
	 * Option keys. In case the option contains an array of sub options, this prop can be used for storing the array
	 * keys for consistency along the application.
	 *
	 * @var array|null $option_keys
	 *
	 * @since 2.0.0
	 */
	public $option_keys = null;
	/**
	 * The option_name.
	 *
	 * @var string $name
	 *
	 * @since 2.0.0
	 */
	protected $name;
	/**
	 * The option value. It has to be an array, so it is more flexible.
	 *
	 * @var array $value
	 *
	 * @since 2.0.0
	 */
	protected $value;
	/**
	 * Sets if option should autoload. Default is false.
	 *
	 * @var bool $autoload
	 *
	 * @since 2.0.0
	 */
	protected $autoload = false;
	/**
	 * Sets if the entire option should be stored partially or not when option value is an array or json object. By default, it is false.
	 *
	 * @var bool $override
	 *
	 * @since 2.0.0
	 */
	protected $override = false;

	/**
	 * The option default value.
	 *
	 * @var bool|null|string|numeric $default
	 *
	 * @since 2.0.0
	 */
	public $default = false;
	/**
	 * When on multisite sets if option is network wide or not. Default true.
	 *
	 * @var bool $network_wide
	 *
	 * @since 2.0.0
	 */
	protected $network_wide = false;

	/**
	 * Options constructor.
	 *
	 * Configure option data.
	 *
	 * @return void|object
	 * @since 2.0.0
	 *
	 */

	public function __construct( $props = array() ) {
		if ( ! empty( $props ) ) {
			$default_props = array(
				'name'         => '',
				'value'        => '',
				'autoload'     => false,
				'override'     => false,
				'network_wide' => false,
				'option_keys'  => null,
				'default'      => false,
			);

			$props = wp_parse_args( $props, $default_props );

			if ( ! empty( $props ) ) {
				foreach ( $props as $property_name => $property_value ) {
					if ( property_exists( $this, $property_name ) ) {
						$this->__set( $property_name, $property_value );
					}
				}
			}
		}
	}

	/**
	 * Initiates the Option.
	 *
	 * @return void
	 * @since 2
	 */
	public function init() {
		$this->value = $this->get( null, null, null, true );
	}

	/**
	 * Set object $this->value param. Option does not get stored in db yet. The $this->value param can be latter be stored/saved by calling $this->save().
	 *
	 * @param array|object $options An array or json object with the options to be saved. Should be in the form key
	 * => value.
	 * @param bool         $override A boolean to set if function will override entire option by clearing all previous options.
	 *
	 * @return bool Returns true if options get set for Options object. Those values are not saved yet.
	 * @since 2.0.0
	 */
	public function set( array $options = array(), bool $override = false ) {
		$_options = $options;

		if ( $override ) {
			$this->value = array();
		}

		// If not an array check if it is json and convert to array. Not a required checked, needs to be removed.
		if ( ! is_array( $options ) ) {
			$_options = json_decode( $options, true );

			if ( json_last_error() !== JSON_ERROR_NONE ) {
				$_options = array();
			}
		}

		if ( ! empty( $_options ) ) {
			if ( ! is_array( $this->value ) ) {
				$this->value = array();
			}

			foreach ( $_options as $key => $value ) {
				$this->value[ $key ] = $value;
			}
		} else {
			return false;
		}

		return true;
	}

	/**
	 * Save Options in DB.
	 *
	 * @param array|null $options The options to save.
	 *
	 * @param bool $override Optional. Useful when option is an array or json object. If true it will replace the
	 * entire option value. By default, it will replace only the value of the specified $options array.
	 *
	 * @param string|null $option_name Optional nullable option_key.
	 *
	 * @param bool $autoload To autoload option or not. Default is false
	 *
	 * @param bool $network_wide Store option network wide or not in case site is multisite. Default is false.
	 *
	 * @return bool True if saved successfully.
	 * @since 2.0.0
	 *
	 */
	public function save(
		$options = null, $override = false, $option_name = null, $autoload = false, $network_wide = false
	) {
		$option_name = ! is_null( $option_name ) ? $option_name : $this->name;
		$options     = ! is_null( $options ) ? $options : $this->value;

		if ( ! is_array( $options ) ) {
			return false;
		}

		if ( ! $override ) {
			$options = wp_parse_args( $options, $this->get( null, $option_name ) );
		}

		$options = wp_json_encode( $this->sanitize_array( $options ) );

		if ( is_multisite() ) {
			$network_wide = is_bool( $network_wide ) ? $network_wide : $this->network_wide;

			if ( $network_wide ) {
				return update_site_option( $option_name, $options );
			}
		}

		return update_option(
			$option_name,
			$options,
			$autoload
		);
	}

	/**
	 * Get Options from DB.
	 *
	 * @param string|null      $settings_key Specific settings key. If null it returns all options.
	 *
	 * @param string|null      $option_name Optional nullable option name.
	 * @param string|null|bool $default Optional default option value.
	 * @param bool             $force Optional. Force fetch option from db. Default is false.
	 *
	 * @return array|string|null Returns an array with options.
	 * @since 2.0.0
	 */
	public function get( string $settings_key = null, string $option_name = null, $default = null, bool $force = false ) {
		if ( ! empty( $this->value ) && ! $force ) {
			$options = $this->value;
		} else {
			$option_name = ! is_null( $option_name ) ? $option_name : $this->name;
			$default     = ! is_null( $default ) ? $default : $this->default;

			if ( is_array( $default ) ) {
				$default = json_encode( $default );
			}

			// Options are expected to be stored as json.
			$options = json_decode( get_option( $option_name, $default ), true );

			if ( json_last_error() !== JSON_ERROR_NONE ) {
				$options = array();
			}

			$this->value = $options;
		}

		if ( ! is_null( $settings_key ) ) {
			$settings_key = sanitize_key( $settings_key );
			$options      = $options[ $settings_key ] ?? null;
		}

		return $options;
	}

	/**
	 * Delete Option from DB.
	 * Does NOT require calling $this->save() in order to finalise deletion.
	 *
	 * @param string|array $options Holds the option key or keys that need to be deleted from option json. It should
	 * be a string or single array.
	 *
	 * @param string|null $option_name Optional nullable option name.
	 *
	 * @param bool|null $network_wide Delete option network wide or not in case site is multisite. Default is false.
	 *
	 * @return bool
	 * @since 2.0.0
	 *
	 */
	public function delete(
		$options = null, $option_name = null, $network_wide = null
	) {
		if ( ! is_null( $options ) ) {
			return $this->delete_option_elements( $options, $option_name, $network_wide );
		}

		$option_name = ! is_null( $option_name ) ? $option_name : $this->name;

		if ( ! is_string( $option_name ) ) {
			return false;
		}

		if ( is_multisite() ) {
			$network_wide = is_bool( $network_wide ) ? $network_wide : $this->network_wide;
			if ( $network_wide ) {
				return delete_site_option( $option_name );
			}
		}

		return delete_option( $option_name );
	}

	/**
	 * Prepares Option elements to be deleted. Removes elements passed through the $options input from the Option.
	 * Does NOT require calling $this->save() in order to finalise deletion.
	 * Used by $this->delete().
	 *
	 * @param string|array $options Holds the option key or keys that need to be deleted from option json. It should
	 * be a string or single array.
	 *
	 * @param string|null $option_name Optional nullable option name.
	 *
	 * @param bool $network_wide Delete option network wide or not in case site is multisite. Default is false.
	 *
	 * @return bool
	 * @since 2.0.0
	 */
	public function delete_option_elements( $options = null, $option_name = null, $network_wide = false ) {
		$option_name = ! is_null( $option_name ) ? $option_name : $this->name;

		if ( ! is_string( $option_name ) ) {
			return false;
		}

		if ( is_object( $options ) ) {
			$options = ( array ) $options;
		}

		$current_options = $this->get( null, $option_name );

		if ( is_array( $options ) ) {
			// Using array_values in case the input $options is multidimensional array.
			$current_options = array_filter(
				$current_options,
				function ( $key ) use ( $options ) {
					return ! in_array( $key, array_values( $options ) );
				},
				ARRAY_FILTER_USE_KEY
			);

		} else {
			unset( $current_options[ $options ] );
		}

		$options = wp_json_encode( $this->sanitize_array( $current_options ) );

		if ( is_multisite() ) {
			$network_wide = is_bool( $network_wide ) ? $network_wide : $this->network_wide;
			if ( $network_wide ) {
				return update_site_option( $option_name, $options );
			}
		}

		return update_option( $option_name, $options );
	}

	/**
	 * Resets Option without deleting it.
	 *
	 * @param string|null $option_name Optional nullable option name.
	 *
	 * @param bool $network_wide Reset option network wide or not in case site is multisite. Default is false.
	 *
	 * @return bool
	 * @since 2.0.0
	 *
	 */
	public function reset_option( string $option_name = null, bool $network_wide = false) {
		$option_name = ! is_null( $option_name ) ? $option_name : $this->name;

		if ( ! is_string( $option_name ) ) {
			return false;
		}

		if ( is_multisite() ) {
			$network_wide = is_bool( $network_wide ) ? $network_wide : $this->network_wide;
			if ( $network_wide ) {
				return update_site_option( $option_name, '' );
			}
		}

		return update_option( $option_name, '' );
	}
}
