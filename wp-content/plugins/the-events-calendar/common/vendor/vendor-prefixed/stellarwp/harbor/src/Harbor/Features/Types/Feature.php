<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Features\Types;

use TEC\Common\LiquidWeb\Harbor\Utils\Cast;

/**
 * Abstract base class for a Feature.
 *
 * Features are immutable value objects hydrated from the Commerce Portal API.
 * Each subclass implements from_array() to handle type-specific fields.
 *
 * @since 1.0.0
 */
abstract class Feature {

	/**
	 * A feature delivered as a standalone WordPress plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const TYPE_PLUGIN = 'plugin';

	/**
	 * A feature delivered as a WordPress theme.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const TYPE_THEME = 'theme';

	/**
	 * A feature delivered as an externally managed service (e.g. Promoter).
	 *
	 * Service features have no WordPress-side installation or activation.
	 * They are enabled or disabled exclusively through the Commerce Portal.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const TYPE_SERVICE = 'service';

	/**
	 * The feature attributes.
	 *
	 * @since 1.0.0
	 *
	 * @var array<string, mixed>
	 */
	protected array $attributes = [];

	/**
	 * Constructor for a Feature object.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $attributes The feature attributes.
	 *
	 * @return void
	 */
	public function __construct( array $attributes ) {
		$this->attributes = $attributes;
	}

	/**
	 * Creates a Feature instance from an associative array.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $data The feature data from the API response.
	 *
	 * @return static
	 */
	abstract public static function from_array( array $data );

	/**
	 * Converts the feature to an associative array.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, mixed>
	 */
	public function to_array(): array {
		return $this->attributes;
	}

	/**
	 * Gets the feature slug.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_slug(): string {
		return Cast::to_string( $this->attributes['slug'] ?? '' );
	}

	/**
	 * Gets the product the feature belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_product(): string {
		return Cast::to_string( $this->attributes['product'] ?? '' );
	}

	/**
	 * Gets the feature tier.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_tier(): string {
		return Cast::to_string( $this->attributes['tier'] ?? '' );
	}

	/**
	 * Gets the feature display name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_name(): string {
		return Cast::to_string( $this->attributes['name'] ?? '' );
	}

	/**
	 * Gets the feature description.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_description(): string {
		return Cast::to_string( $this->attributes['description'] ?? '' );
	}

	/**
	 * Gets the feature type identifier.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_type(): string {
		return Cast::to_string( $this->attributes['type'] ?? '' );
	}

	/**
	 * Checks whether the feature is available for the current site.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_available(): bool {
		return Cast::to_bool( $this->attributes['is_available'] ?? false );
	}

	/**
	 * Whether the user's licensed tier covers this feature's minimum tier.
	 *
	 * True when the license tier rank is >= the feature's minimum tier rank.
	 * False when the user is unlicensed, or their tier rank is below the minimum.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_in_catalog_tier(): bool {
		return Cast::to_bool( $this->attributes['in_catalog_tier'] ?? false );
	}

	/**
	 * Checks whether the feature is currently enabled/active.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		return Cast::to_bool( $this->attributes['is_enabled'] ?? false );
	}

	/**
	 * Gets the URL to the feature documentation or learn-more page.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_documentation_url(): string {
		return Cast::to_string( $this->attributes['documentation_url'] ?? '' );
	}

	/**
	 * Extracts the common base attributes shared by all feature types.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $data The feature data from the API response.
	 *
	 * @return array<string, mixed>
	 */
	protected static function base_attributes( array $data ): array {
		return [
			'slug'              => Cast::to_string( $data['slug'] ?? '' ),
			'product'           => Cast::to_string( $data['product'] ?? '' ),
			'tier'              => Cast::to_string( $data['tier'] ?? '' ),
			'name'              => Cast::to_string( $data['name'] ?? '' ),
			'description'       => Cast::to_string( $data['description'] ?? '' ),
			'is_available'      => Cast::to_bool( $data['is_available'] ?? false ),
			'access_sources'    => (array) ( $data['access_sources'] ?? [] ),
			'in_catalog_tier'   => Cast::to_bool( $data['in_catalog_tier'] ?? false ),
			'is_enabled'        => Cast::to_bool( $data['is_enabled'] ?? false ),
			'documentation_url' => Cast::to_string( $data['documentation_url'] ?? '' ),
		];
	}

	/**
	 * Gets an attribute by name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key The attribute name.
	 *
	 * @return mixed
	 */
	protected function get_attribute( string $key ) {
		return $this->attributes[ $key ] ?? null;
	}
}
