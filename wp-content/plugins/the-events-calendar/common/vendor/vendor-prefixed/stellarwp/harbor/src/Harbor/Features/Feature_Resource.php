<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Features;

use TEC\Common\LiquidWeb\Harbor\Features\Types\Feature;
use TEC\Common\LiquidWeb\Harbor\Features\Types\Plugin;
use TEC\Common\LiquidWeb\Harbor\Features\Types\Service;
use TEC\Common\LiquidWeb\Harbor\Features\Types\Theme;

/**
 * Decorates a resolved Feature with request-context data for REST API responses.
 *
 * After feature resolution is complete (Feature_Repository cache is warm),
 * reading the update transient via get_site_transient() is safe: the transient
 * filter chain hits the cached repository result, so there is no circular
 * dependency.
 *
 * update_version is the version string from the WordPress update transient
 * response object, or null when the feature has no pending update registered
 * by the handler (e.g. dot-org plugins, unlicensed, or not installed).
 *
 * @since 1.0.0
 */
final class Feature_Resource {

	/**
	 * The resolved Feature.
	 *
	 * @since 1.0.0
	 *
	 * @var Feature
	 */
	private Feature $feature;

	/**
	 * The update version sourced from the WordPress update transient, or null.
	 *
	 * @since 1.0.0
	 *
	 * @var string|null
	 */
	private ?string $update_version;

	/**
	 * @since 1.0.0
	 *
	 * @param Feature     $feature        The resolved feature.
	 * @param string|null $update_version Version from the update transient, or null.
	 */
	public function __construct( Feature $feature, ?string $update_version ) {
		$this->feature        = $feature;
		$this->update_version = $update_version;
	}

	/**
	 * Constructs a Feature_Resource from a resolved Feature.
	 *
	 * Should only be called after Feature_Repository has already cached its
	 * results so transient access does not trigger re-resolution.
	 *
	 * @since 1.0.0
	 *
	 * @param Feature $feature The resolved feature.
	 *
	 * @return self
	 */
	public static function from_feature( Feature $feature ) {
		$update_version = null;

		if ( $feature instanceof Plugin ) {
			$update_version = self::get_plugin_update_version( $feature );
		} elseif ( $feature instanceof Theme ) {
			$update_version = self::get_theme_update_version( $feature );
		}

		return new self( $feature, $update_version );
	}

	/**
	 * Returns the data array for use in REST API responses.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, mixed>
	 */
	public function to_array(): array {
		// Services do not support update_version.
		if ( $this->feature instanceof Service ) {
			return $this->feature->to_array();
		}

		return array_merge(
			$this->feature->to_array(),
			[ 'update_version' => $this->update_version ]
		);
	}

	/**
	 * Returns the update version, or null if no update is pending.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	public function get_update_version(): ?string {
		return $this->update_version;
	}

	/**
	 * Returns the decorated Feature.
	 *
	 * @since 1.0.0
	 *
	 * @return Feature
	 */
	public function get_feature(): Feature {
		return $this->feature;
	}

	/**
	 * Reads update_version from the plugin update transient for the given plugin feature.
	 *
	 * @since 1.0.0
	 *
	 * @param Plugin $feature The plugin feature.
	 *
	 * @return string|null
	 */
	private static function get_plugin_update_version( Plugin $feature ) {
		$transient = get_site_transient( 'update_plugins' );

		if ( ! is_object( $transient ) || ! isset( $transient->response ) || ! is_array( $transient->response ) ) {
			return null;
		}

		$plugin_file = $feature->get_plugin_file();

		if ( ! isset( $transient->response[ $plugin_file ] ) ) {
			return null;
		}

		$response = $transient->response[ $plugin_file ];

		$version = is_object( $response ) ? ( $response->new_version ?? null ) : null;

		if ( ! is_string( $version ) || $version === '' ) {
			return null;
		}

		return $version;
	}

	/**
	 * Reads update_version from the theme update transient for the given theme feature.
	 *
	 * @since 1.0.0
	 *
	 * @param Theme $feature The theme feature.
	 *
	 * @return string|null
	 */
	private static function get_theme_update_version( Theme $feature ) {
		$transient = get_site_transient( 'update_themes' );

		if ( ! is_object( $transient ) || ! isset( $transient->response ) || ! is_array( $transient->response ) ) {
			return null;
		}

		$slug = $feature->get_slug();

		if ( ! isset( $transient->response[ $slug ] ) ) {
			return null;
		}

		$response = $transient->response[ $slug ];

		$version = is_array( $response ) ? ( $response['new_version'] ?? null ) : null;

		if ( ! is_string( $version ) || $version === '' ) {
			return null;
		}

		return $version;
	}
}
