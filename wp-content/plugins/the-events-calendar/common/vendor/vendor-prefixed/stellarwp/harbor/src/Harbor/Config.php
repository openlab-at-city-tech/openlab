<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor;

use TEC\Common\LiquidWeb\Harbor\Utils\Cast;
use RuntimeException;
use TEC\Common\StellarWP\ContainerContract\ContainerInterface;

class Config {

	/**
	 * The default base URL for the StellarWP licensing service.
	 *
	 * @since 1.0.0
	 */
	public const DEFAULT_LICENSING_BASE_URL = 'https://licensing.nexcess.com';

	/**
	 * The default base URL for the Commerce Portal (catalog API).
	 *
	 * @since 1.0.0
	 */
	public const DEFAULT_PORTAL_BASE_URL = 'https://software.liquidweb.com';

	/**
	 * The default base URL for the Herald download service.
	 *
	 * @since 1.0.0
	 */
	public const DEFAULT_HERALD_BASE_URL = 'https://herald.nexcess.com';

	/**
	 * Container object.
	 *
	 * @since 1.0.0
	 *
	 * @var ContainerInterface
	 */
	protected static $container;

	/**
	 * The base URL for the StellarWP licensing service.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected static $licensing_base_url = self::DEFAULT_LICENSING_BASE_URL;

	/**
	 * The base URL for the Commerce Portal (catalog API).
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected static $portal_base_url = self::DEFAULT_PORTAL_BASE_URL;

	/**
	 * The base URL for the Herald download service.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected static $herald_base_url = self::DEFAULT_HERALD_BASE_URL;

	/**
	 * The plugin basename (relative to WP_PLUGIN_DIR) of the plugin hosting this Harbor instance.
	 *
	 * @since 1.0.0
	 *
	 * @var string|null
	 */
	protected static $plugin_basename = null;

	/**
	 * Get the container.
	 *
	 * @since 1.0.0
	 *
	 * @throws RuntimeException If the container has not been set.
	 *
	 * @return ContainerInterface
	 */
	public static function get_container() {
		if ( self::$container === null ) {
			throw new RuntimeException(
				__( 'You must provide a container via LiquidWeb\Harbor\Config::set_container() before attempting to fetch it.', 'tribe-common' )
			);
		}

		return self::$container;
	}

	/**
	 * Returns whether the container has been set.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function has_container(): bool {
		return self::$container !== null;
	}

	/**
	 * Resets this class back to the defaults.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function reset(): void {
		static::$licensing_base_url = self::DEFAULT_LICENSING_BASE_URL;
		static::$portal_base_url    = self::DEFAULT_PORTAL_BASE_URL;
		static::$herald_base_url    = self::DEFAULT_HERALD_BASE_URL;
		static::$plugin_basename    = null;
	}

	/**
	 * Returns the plugin basename (relative to WP_PLUGIN_DIR) of the plugin
	 * hosting this Harbor instance, or null if not set.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	public static function get_plugin_basename(): ?string {
		return static::$plugin_basename;
	}

	/**
	 * Set the plugin basename of the plugin hosting this Harbor instance.
	 *
	 * Pass the result of plugin_basename( __FILE__ ) from the host plugin's
	 * main file, e.g. 'myplugin/myplugin.php'.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_basename The plugin basename (e.g. 'myplugin/myplugin.php').
	 *
	 * @return void
	 */
	public static function set_plugin_basename( string $plugin_basename ): void {
		static::$plugin_basename = $plugin_basename;
	}

	/**
	 * Set the container object.
	 *
	 * @since 1.0.0
	 *
	 * @param ContainerInterface $container Container object.
	 *
	 * @return void
	 */
	public static function set_container( ContainerInterface $container ): void {
		self::$container = $container;
	}

	/**
	 * Set the base URL for the StellarWP licensing service.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url The API base URL (no trailing slash).
	 *
	 * @return void
	 */
	public static function set_licensing_base_url( string $url ): void {
		static::$licensing_base_url = rtrim( $url, '/' );
	}

	/**
	 * Get the base URL for the StellarWP licensing service.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_licensing_base_url(): string {
		if ( defined( 'LW_HARBOR_LICENSING_BASE_URL' ) ) {
			$url = Cast::to_string( LW_HARBOR_LICENSING_BASE_URL );

			return rtrim( $url, '/' );
		}

		return static::$licensing_base_url;
	}

	/**
	 * Set the base URL for the Commerce Portal (catalog API).
	 *
	 * @since 1.0.0
	 *
	 * @param string $url The portal base URL (no trailing slash).
	 *
	 * @return void
	 */
	public static function set_portal_base_url( string $url ): void {
		static::$portal_base_url = rtrim( $url, '/' );
	}

	/**
	 * Get the base URL for the Commerce Portal (catalog API).
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_portal_base_url(): string {
		if ( defined( 'LW_HARBOR_PORTAL_BASE_URL' ) ) {
			$url = Cast::to_string( LW_HARBOR_PORTAL_BASE_URL );

			return rtrim( $url, '/' );
		}

		return static::$portal_base_url;
	}

	/**
	 * Set the base URL for the Herald download service.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url The Herald base URL (no trailing slash).
	 *
	 * @return void
	 */
	public static function set_herald_base_url( string $url ): void {
		static::$herald_base_url = rtrim( $url, '/' );
	}

	/**
	 * Get the base URL for the Herald download service.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_herald_base_url(): string {
		if ( defined( 'LW_HARBOR_HERALD_BASE_URL' ) ) {
			$url = Cast::to_string( LW_HARBOR_HERALD_BASE_URL );

			return rtrim( $url, '/' );
		}

		return static::$herald_base_url;
	}
}
