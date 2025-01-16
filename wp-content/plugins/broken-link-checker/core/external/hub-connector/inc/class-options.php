<?php
/**
 * The options class.
 *
 * This class will handle setting options and transients.
 *
 * @link    http://wpmudev.com
 * @since   1.0.0
 * @author  Joel James <joel@incsub.com>
 * @package WPMUDEV\Hub\Connector
 */

namespace WPMUDEV\Hub\Connector;

/**
 * Class Options
 */
class Options {

	/**
	 * Settings options key.
	 *
	 * @since 1.0.0
	 */
	const NAME = 'wpmudev_hc_options';

	/**
	 * Returns the value of a module option.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name    The option name.
	 * @param mixed  $default Optional. Set value to return if option not found.
	 *
	 * @return mixed The option value.
	 */
	public static function get( $name, $default = false ) {
		if ( ! empty( $name ) ) {
			// Get options.
			$options = self::get_options();

			// Return value.
			return isset( $options[ $name ] ) ? $options[ $name ] : $default;
		}

		return $default;
	}

	/**
	 * Updates the value of a single module option.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name  The option name.
	 * @param mixed  $value The new option value.
	 *
	 * @return bool
	 */
	public static function set( $name, $value = false ) {
		$defaults = self::defaults();

		// Only if name is set.
		if ( isset( $defaults[ $name ] ) ) {
			$options = self::get_options();

			// Set the value.
			$options[ $name ] = $value;

			// Update the values.
			self::set_options( $options );
		}

		return false;
	}

	/**
	 * Returns the entire module options.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_options() {
		return (array) get_site_option( self::NAME, array() );
	}

	/**
	 * Updates the entire module options.
	 *
	 * @since 1.0.0
	 *
	 * @param array $values The values to update.
	 *
	 * @return bool
	 */
	public static function set_options( $values ) {
		return update_site_option( self::NAME, $values );
	}

	/**
	 * Returns the value of a module transient.
	 *
	 * The prefix is automatically added to the transient name.
	 * Use this function instead of direct access via get_site_transient().
	 *
	 * @since 1.0.0
	 *
	 * @param string $name   The transient name.
	 * @param bool   $prefix Optional. Set to false to not prefix the name.
	 *
	 * @return mixed The transient value.
	 */
	public static function get_transient( $name, $prefix = true ) {
		$key = $prefix ? 'wpmudev_hc_' . $name : $name;

		// Transient name cannot be longer than 45 characters.
		$key = substr( $key, 0, 45 );

		return get_site_transient( $key );
	}

	/**
	 * Updates the value of a module transient.
	 *
	 * The prefix is automatically added to the transient name.
	 * Use this function instead of direct access via set_site_transient().
	 *
	 * @since 1.0.0
	 *
	 * @param string $name       The transient name.
	 * @param mixed  $value      The new transient value.
	 * @param int    $expiration Time until expiration. Default: No expiration.
	 * @param bool   $prefix     Optional. Set to false to not prefix the name.
	 *
	 * @return bool
	 */
	public static function set_transient( $name, $value, $expiration = 0, $prefix = true ) {
		$key = $prefix ? 'wpmudev_hc_' . $name : $name;

		// Transient name cannot be longer than 45 characters.
		$key = substr( $key, 0, 45 );

		// Fix to prevent WP from hashing PHP objects.
		delete_site_transient( $key );

		if ( null !== $value ) {
			return set_site_transient( $key, $value, $expiration );
		}

		return false;
	}

	/**
	 * Reset the options to defaults.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function reset() {
		return self::set_options( self::defaults() );
	}

	/**
	 * Get the default options values.
	 *
	 * Only the options in this list will be accepted for update.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function defaults() {
		$options = array(
			// Bigger options.
			'membership_data' => array(),
			'profile_data'    => array(),
			// Other small options.
			'timestamp_sync'  => array(),
			'version'         => \WPMUDEV_HUB_CONNECTOR_VERSION,
			'hub_nonce'       => '',
		);

		/**
		 * Filter to modify default options.
		 *
		 * @since 1.0.0
		 *
		 * @param array $options Default options.
		 */
		return apply_filters( 'wpmudev_hub_connector_options_defaults', $options );
	}
}
