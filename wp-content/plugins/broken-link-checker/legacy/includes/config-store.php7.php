<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Configuration Store Class for PHP 7.4
 *
 * @package Broken Link Checker
 */

/**
 * Configuration Store Class
 *
 * @package Broken Link Checker
 */

if ( class_exists( 'blcConfigStore' ) ) {
	return;
}
/**
 * Class blcConfigStore
 *
 * Implements ArrayAccess to provide array-like access to configuration data.
 */
class blcConfigStore implements ArrayAccess {

	/**
	 * Configuration managers map.
	 *
	 * @var array<string, blcConfigurationManager>
	 */
	private $managers = array();

	/**
	 * Cached settings map.
	 *
	 * @var array<string, string>
	 */
	private $cached_settings = array();

	/**
	 * Default manager key.
	 *
	 * @var blcConfigurationManager|null
	 */
	private $default_manager = null;

	/**
	 * Check if a configuration offset exists.
	 *
	 * @param mixed $offset The offset to check.
	 * @return bool True if offset exists, false otherwise.
	 */
	public function offsetExists( $offset ): bool {
		$manager = $this->resolve_manager( $offset );
		if ( $manager ) {
			return $manager->has_option( $offset );
		}

		return false;
	}

	/**
	 * Get a configuration value by offset.
	 *
	 * @param mixed $offset The offset to retrieve.
	 * @return mixed The configuration value or null if not set.
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		$manager = $this->resolve_manager( $offset );
		if ( $manager ) {
			return $manager->get( $offset );
		}
		return null;
	}

	/**
	 * Set a configuration value by offset.
	 *
	 * @param mixed $offset The offset to set.
	 * @param mixed $value  The value to set.
	 * @return void
	 */
	public function offsetSet( $offset, $value ): void {
		if ( is_null( $offset ) ) {
			return;
		}

		$manager = $this->resolve_manager( $offset );
		if ( $manager ) {
			$manager->set( $offset, $value );
		}
	}

	/**
	 * Unset a configuration value by offset.
	 *
	 * @param mixed $offset The offset to unset.
	 * @return void
	 */
	public function offsetUnset( $offset ): void {
		$manager = $this->resolve_manager( $offset );
		if ( $manager && $manager->has_option( $offset ) ) {
			$manager->delete_option( $offset );
		}
	}

	/**
	 * Add a configuration manager.
	 *
	 * @param string                  $key     The key for the manager.
	 * @param blcConfigurationManager $manager The manager to add.
	 * @return void
	 */
	public function add_manager( string $key, blcConfigurationManager $manager ): void {
		$this->managers[ $key ] = $manager;
		$this->cache_available_settings( $key, $manager->get_available_settings() );
	}

	/**
	 * Check if a configuration manager exists.
	 *
	 * @param string $key The key of the manager.
	 * @return bool True if manager exists, false otherwise.
	 */
	public function has_manager( string $key ): bool {
		return isset( $this->managers[ $key ] );
	}

	/**
	 * Get the default configuration manager.
	 *
	 * @return blcConfigurationManager|null The default manager or null if not set.
	 */
	public function get_default_manager(): ?blcConfigurationManager {
		return $this->default_manager;
	}

	/**
	 * Cache available settings from a manager.
	 *
	 * @param string $manager_key     The manager key.
	 * @param array  $manager_options The manager options to cache.
	 * @return void
	 */
	public function cache_available_settings( string $manager_key, array $manager_options ): void {
		foreach ( $manager_options as $option ) {
			$this->cached_settings[ $option ] = $manager_key;
		}
	}

	/**
	 * Remove a configuration manager by key.
	 *
	 * @param string $key The key of the manager to remove.
	 * @return bool True if manager was removed, false if not found.
	 */
	public function remove_manager( string $key ): bool {
		if ( isset( $this->managers[ $key ] ) ) {
			unset( $this->managers[ $key ] );
			$this->remove_cached_settings( $key );
			return true;
		}
		return false;
	}

	/**
	 * Remove cached settings for a manager.
	 *
	 * @param string $manager_key The manager key whose settings to remove.
	 * @return void
	 */
	public function remove_cached_settings( string $manager_key ): void {
		foreach ( $this->cached_settings as $setting => $key ) {
			if ( $key === $manager_key ) {
				unset( $this->cached_settings[ $setting ] );
			}
		}
	}

	/**
	 * Get all configuration managers.
	 *
	 * @return array<string, blcConfigurationManager> Map of configuration managers.
	 */
	public function get_managers(): array {
		return $this->managers;
	}

	/**
	 * Get a specific configuration manager by key.
	 *
	 * @param string $key The manager key.
	 * @return blcConfigurationManager|null The manager or null if not found.
	 */
	public function get_manager( string $key ): ?blcConfigurationManager {
		return $this->managers[ $key ] ?? null;
	}

	/**
	 * Set the default configuration manager.
	 *
	 * @param string $key The key of the manager to use as default.
	 * @return void
	 */
	public function set_default_manager( string $key ): void {
		if ( isset( $this->managers[ $key ] ) ) {
			$this->default_manager = $this->managers[ $key ];
		}
	}

	/**
	 * Get the manager responsible for a specific setting.
	 *
	 * @param string $setting The setting key.
	 * @return blcConfigurationManager|null The manager or null if not found.
	 */
	public function resolve_manager( string $setting ): ?blcConfigurationManager {
		if ( isset( $this->cached_settings[ $setting ] ) ) {
			$manager_key = $this->cached_settings[ $setting ];
			return $this->get_manager( $manager_key );
		}

		// Fall back to default manager if set.
		if ( $this->default_manager ) {
			return $this->default_manager;
		}

		return null;
	}
}
