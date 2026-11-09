<?php
/**
 * Global (non-namespaced) Harbor helper functions.
 *
 * These functions are registered by whichever vendor-prefixed Harbor instance
 * is the version leader. They delegate to version-keyed closures stored in a
 * static registry so that the highest version's logic always runs, regardless
 * of which instance's copy of this file was included first.
 *
 * Plugin consumers should use these functions instead of the namespaced
 * equivalents to ensure they always execute the most up-to-date implementation.
 */

if ( ! function_exists( '_lw_harbor_instance_registry' ) ) {
	/**
	 * Reads from or writes to the active Harbor instance registry.
	 *
	 * The static variable lives inside this single function so all
	 * vendor-prefixed copies share the same registry. Only currently-active
	 * instances can register themselves, so there is no stale-version problem.
	 *
	 * - Register: _lw_harbor_instance_registry( '3.0.1', 'givewp/give.php' )  // appends to version's list
	 * - Read all:  _lw_harbor_instance_registry()
	 *
	 * @internal Not intended for direct use by plugins.
	 *
	 * @param string $version     Version to register (omit when reading).
	 * @param string $plugin_basename Plugin basename relative to WP_PLUGIN_DIR (omit when reading).
	 *
	 * @return array<string, string[]> Map of version string to list of plugin basenames.
	 */
	function _lw_harbor_instance_registry( string $version = '', string $plugin_basename = '' ): array {
		/** @var array<string, string[]> $instances */
		static $instances = [];

		if ( '' === $version ) {
			return $instances;
		}

		if ( did_action( 'wp_loaded' ) ) {
			_doing_it_wrong(
				__FUNCTION__,
				'Registrations are only accepted during the bootstrap window (before wp_loaded).',
				'1.2.0'
			);
			return $instances;
		}

		// Only accept registrations during the bootstrap window (before wp_loaded).
		// All real Harbor instances initialize during plugins_loaded, so anything
		// arriving after wp_loaded is outside the expected lifecycle and is ignored
		// to prevent external code from injecting fake versions into the registry.
		$instances[ $version ][] = $plugin_basename;

		return $instances;
	}
}

if ( ! function_exists( '_lw_harbor_global_function_registry' ) ) {
	/**
	 * Reads from or writes to the global function registry.
	 *
	 * The static variable lives inside this single function so all callers
	 * share the same registry without relying on $GLOBALS.
	 *
	 * - Register: _lw_harbor_global_function_registry( 'key', '1.0.0', $callable )
	 * - Retrieve leader callable: _lw_harbor_global_function_registry( 'key' )
	 *
	 * @internal Not intended for direct use by plugins.
	 *
	 * @param string        $key      Function identifier.
	 * @param string        $version  Version registering the callable (omit when reading).
	 * @param callable|null $callback Callable to register (omit when reading).
	 *
	 * @return callable|null Null when writing, or the leader's callable when reading.
	 */
	function _lw_harbor_global_function_registry( string $key, string $version = '', ?callable $callback = null ): ?callable {
		/** @var array<string, array<string, callable>> $registry */
		static $registry = [];

		if ( $callback !== null ) {
			// Mirror the instance registry's registration window: only accept
			// writes before wp_loaded so callbacks can't be injected after bootstrap.
			if ( ! did_action( 'wp_loaded' ) ) {
				$registry[ $key ][ $version ] = $callback;
			}
			return null;
		}

		$versions = array_keys( _lw_harbor_instance_registry() );
		$highest  = array_reduce(
			$versions,
			static function ( string $carry, string $v ): string {
				return version_compare( $v, $carry, '>' ) ? $v : $carry;
			},
			'0.0.0'
		);

		return $registry[ $key ][ $highest ] ?? null;
	}
}

if ( ! function_exists( 'lw_harbor_has_unified_license_key' ) ) {
	/**
	 * Whether the site has a unified license key stored or discoverable.
	 *
	 * Does not make any remote API calls — only checks local storage and
	 * registered products for an embedded key.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	function lw_harbor_has_unified_license_key(): bool {
		$callback = _lw_harbor_global_function_registry( 'lw_harbor_has_unified_license_key' );

		return $callback ? (bool) $callback() : false;
	}
}

if ( ! function_exists( 'lw_harbor_get_unified_license_key' ) ) {
	/**
	 * Get the unified license key.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null The unified license key, or null if not found.
	 */
	function lw_harbor_get_unified_license_key(): ?string {
		$callback = _lw_harbor_global_function_registry( 'lw_harbor_get_unified_license_key' );

		// @phpstan-ignore return.type
		return $callback ? $callback() : null;
	}
}

if ( ! function_exists( 'lw_harbor_is_product_license_active' ) ) {
	/**
	 * Whether a specific product has an active, valid license.  *
	 *
	 * @since 1.0.0
	 *
	 * @param string $product The product slug (e.g. 'give', 'learndash', 'kadence', 'the-events-calendar').
	 *
	 * @return bool
	 */
	function lw_harbor_is_product_license_active( string $product ): bool {
		$callback = _lw_harbor_global_function_registry( 'lw_harbor_is_product_license_active' );

		return $callback ? (bool) $callback( $product ) : false;
	}
}

if ( ! function_exists( 'lw_harbor_is_feature_enabled' ) ) {
	/**
	 * Whether a feature is currently active/enabled locally on this site.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The feature slug.
	 *
	 * @return bool
	 */
	function lw_harbor_is_feature_enabled( string $slug ): bool {
		$callback = _lw_harbor_global_function_registry( 'lw_harbor_is_feature_enabled' );

		return $callback ? (bool) $callback( $slug ) : false;
	}
}

if ( ! function_exists( 'lw_harbor_is_feature_available' ) ) {
	/**
	 * Whether the customer's license/tier includes this feature.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The feature slug.
	 *
	 * @return bool
	 */
	function lw_harbor_is_feature_available( string $slug ): bool {
		$callback = _lw_harbor_global_function_registry( 'lw_harbor_is_feature_available' );

		return $callback ? (bool) $callback( $slug ) : false;
	}
}

if ( ! function_exists( 'lw_harbor_register_submenu' ) ) {
	/**
	 * Registers a submenu item under a plugin's existing menu that links to the Harbor feature manager.
	 *
	 * Call this during or after plugins_loaded, before the admin_menu hook fires.
	 *
	 * @since 1.0.0
	 *
	 * @param string $parent_slug The slug of the parent top-level menu item.
	 *
	 * @return void
	 */
	function lw_harbor_register_submenu( string $parent_slug ): void {
		$callback = _lw_harbor_global_function_registry( 'lw_harbor_register_submenu' );

		if ( $callback ) {
			$callback( $parent_slug );
		}
	}
}

if ( ! function_exists( 'lw_harbor_get_licensed_domain' ) ) {
	/**
	 * Returns the domain used for licensing on the current site.
	 *
	 * @since 1.0.0
	 *
	 * @return string The site domain, or an empty string if no instance is active.
	 */
	function lw_harbor_get_licensed_domain(): string {
		$callback = _lw_harbor_global_function_registry( 'lw_harbor_get_licensed_domain' );

		$result = $callback ? $callback() : '';

		return is_string( $result ) ? $result : '';
	}
}

if ( ! function_exists( 'lw_harbor_get_license_page_url' ) ) {
	/**
	 * Returns the admin URL for the unified Feature Manager page.
	 *
	 * @since 1.0.0
	 *
	 * @return string The admin URL, or an empty string if no instance is active.
	 */
	function lw_harbor_get_license_page_url(): string {
		$callback = _lw_harbor_global_function_registry( 'lw_harbor_get_license_page_url' );

		$result = $callback ? $callback() : '';

		return is_string( $result ) ? $result : '';
	}
}

if ( ! function_exists( 'lw_harbor_refresh_catalog' ) ) {
	/**
	 * Forces a synchronous fresh catalog fetch from the Commerce Portal API.
	 *
	 * Bypasses the cached catalog state and re-populates it from the upstream
	 * service. Use after any action where the caller has reason to believe the
	 * remote catalog has changed (e.g. a user-initiated "refresh" button).
	 *
	 * Synchronous: this performs an outbound HTTP request and blocks until the
	 * Portal API responds. Returns true on a successful refresh, false on any
	 * failure (HTTP error, parsing error, or Harbor not being active).
	 *
	 * @since 1.4.0
	 *
	 * @return bool Whether the catalog was refreshed successfully.
	 */
	function lw_harbor_refresh_catalog(): bool {
		$callback = _lw_harbor_global_function_registry( 'lw_harbor_refresh_catalog' );

		return $callback ? (bool) $callback() : false;
	}
}

if ( ! function_exists( 'lw_harbor_display_legacy_license_page_notice' ) ) {
	/**
	 * Displays an informational admin notice on legacy plugin license pages.
	 *
	 * Intended to be called by consuming plugins on their own license settings
	 * pages to inform users that licensing is now managed centrally through
	 * Nexcess's unified system.
	 *
	 * @since 1.0.0
	 *
	 * @param string $product_name Optional human-readable product name (e.g. "GiveWP", "Kadence").
	 *                            When omitted, a generic message is displayed.
	 *
	 * @return void
	 */
	function lw_harbor_display_legacy_license_page_notice( string $product_name = '' ): void {
		$callback = _lw_harbor_global_function_registry( 'lw_harbor_display_legacy_license_page_notice' );

		if ( $callback ) {
			$callback( $product_name );
		}
	}
}
