<?php
/**
 * A helper class to provide configuration options for the library.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 *
 * @license GPL-2.0-or-later
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace TEC\Common\StellarWP\Telemetry;

use TEC\Common\StellarWP\ContainerContract\ContainerInterface;

/**
 * A configuration class to help set up the library.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
class Config {

	/**
	 * Container object.
	 *
	 * @since 1.0.0
	 *
	 * @var \TEC\Common\StellarWP\ContainerContract\ContainerInterface
	 */
	protected static $container;

	/**
	 * Prefix for hook names.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected static $hook_prefix = '';

	/**
	 * Unique ID for the stellarwp slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected static $stellar_slug = '';

	/**
	 * Unique IDs and optional plugin slugs for StellarWP slugs.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	protected static $stellar_slugs = [];

	/**
	 * The url of the telemetry server.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected static $server_url = 'https://telemetry.stellarwp.com/api/v1';

	/**
	 * Get the container.
	 *
	 * @since 1.0.0
	 *
	 * @throws \RuntimeException Throws exception if container is not set.
	 *
	 * @return \TEC\Common\StellarWP\ContainerContract\ContainerInterface
	 */
	public static function get_container() {
		if ( null === self::$container ) {
			throw new \RuntimeException( 'You must provide a container via StellarWP\Telemetry\Config::set_container() before attempting to fetch it.' );
		}

		return self::$container;
	}

	/**
	 * Gets the hook prefix.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_hook_prefix() {
		return static::$hook_prefix;
	}

	/**
	 * Gets the telemetry server url.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_server_url() {
		return static::$server_url;
	}

	/**
	 * Gets the stellar slug.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_stellar_slug() {
		return static::$stellar_slug;
	}

	/**
	 * Gets the registered stellar slugs.
	 *
	 * @since 2.0.0
	 *
	 * @return array<string,string>
	 */
	public static function get_all_stellar_slugs() {
		return static::$stellar_slugs;
	}

	/**
	 * Returns whether the container has been set.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function has_container() {
		return null !== self::$container;
	}

	/**
	 * Resets this class back to the defaults.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function reset() {
		static::$hook_prefix  = '';
		static::$server_url   = 'https://telemetry.stellarwp.com/api/v1';
		static::$stellar_slug = '';
	}

	/**
	 * Set the container object.
	 *
	 * @since 1.0.0
	 *
	 * @param \TEC\Common\StellarWP\ContainerContract\ContainerInterface $container Container object.
	 *
	 * @return void
	 */
	public static function set_container( ContainerInterface $container ) {
		self::$container = $container;
	}

	/**
	 * Sets the hook prefix.
	 *
	 * @since 1.0.0
	 *
	 * @param string $prefix The prefix to use for hooks.
	 *
	 * @return void
	 */
	public static function set_hook_prefix( string $prefix ) {
		// Make sure the prefix always ends with a separator.
		if ( substr( $prefix, -1 ) !== '/' ) {
			$prefix = $prefix . '/';
		}

		static::$hook_prefix = $prefix;
	}

	/**
	 * Sets the stellar slug.
	 *
	 * @since 1.0.0
	 *
	 * @param string $stellar_slug The unique slug to identify the plugin with the server.
	 *
	 * @return void
	 */
	public static function set_stellar_slug( string $stellar_slug ) {
		static::$stellar_slug = $stellar_slug;

		// Also add the stellar slug to the array of all registered stellar slugs.
		static::$stellar_slugs[ $stellar_slug ] = '';
	}

	/**
	 * Adds a new stellar slug to the stellar slugs array.
	 *
	 * Utilizing an array of stellar slugs, the library can be tailored for use in a single plugin
	 * or use within a shared library for several plugins. Each stellar slug registered will
	 * generate unique filters and hooks that give further customization for each slug
	 *
	 * @since 2.0.0
	 *
	 * @param string $stellar_slug A unique slug to add to the config.
	 * @param string $wp_slug      The plugin's basename (used for capturing deactivation "Exit Interview" info).
	 *
	 * @return void
	 */
	public static function add_stellar_slug( string $stellar_slug, string $wp_slug = '' ) {
		static::$stellar_slugs[ $stellar_slug ] = $wp_slug;
	}

	/**
	 * Sets the telemetry server url.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url The url of the telemetry server.
	 *
	 * @return void
	 */
	public static function set_server_url( string $url ) {
		static::$server_url = $url;
	}

}
