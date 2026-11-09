<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Portal\Results;

use TEC\Common\LiquidWeb\Harbor\Utils\Cast;

/**
 * A single tier definition from the product catalog.
 *
 * Immutable value object hydrated from the Commerce Portal catalog API response.
 *
 * @since 1.0.0
 *
 * @phpstan-type TierAttributes array{
 *     tier_slug: string,
 *     name: string,
 *     rank: int,
 *     price: int,
 *     currency: string,
 *     herald_slugs: list<string>,
 *     purchase_url: string,
 *     upgrade_url: string,
 * }
 */
final class Catalog_Tier {

	/**
	 * The tier attributes.
	 *
	 * @since 1.0.0
	 *
	 * @var TierAttributes
	 */
	protected array $attributes = [
		'tier_slug'    => '',
		'name'         => '',
		'rank'         => 0,
		'price'        => 0,
		'currency'     => '',
		'herald_slugs' => [],
		'purchase_url' => '',
		'upgrade_url'  => '',
	];

	/**
	 * Constructor for a Catalog_Tier.
	 *
	 * @since 1.0.0
	 *
	 * @phpstan-param TierAttributes $attributes
	 *
	 * @param array $attributes The tier attributes.
	 *
	 * @return void
	 */
	public function __construct( array $attributes ) {
		$this->attributes = array_merge( $this->attributes, $attributes );
	}

	/**
	 * Creates a Catalog_Tier from a raw data array.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $data The tier data.
	 *
	 * @return self
	 */
	public static function from_array( array $data ): self {
		// Portal uses capabilities; earlier catalog fixtures used herald_slugs.
		// Preserve Harbor's existing serialized field while accepting either input.
		if ( isset( $data['capabilities'] ) && is_array( $data['capabilities'] ) ) {
			$data['herald_slugs'] = $data['capabilities'];
		}

		return new self(
			[
				'tier_slug'    => Cast::to_string( $data['tier_slug'] ?? '' ),
				'name'         => Cast::to_string( $data['name'] ?? '' ),
				'rank'         => Cast::to_int( $data['rank'] ?? 0 ),
				'price'        => Cast::to_int( $data['price'] ?? 0 ),
				'currency'     => Cast::to_string( $data['currency'] ?? '' ),
				'herald_slugs' => isset( $data['herald_slugs'] ) && is_array( $data['herald_slugs'] )
					? array_map( [ Cast::class, 'to_string' ], array_values( $data['herald_slugs'] ) )
					: [],
				'purchase_url' => self::web_url( $data['purchase_url'] ?? '' ),
				'upgrade_url'  => self::web_url( $data['upgrade_url'] ?? '' ),
			]
		);
	}

	/**
	 * Keeps a catalog link only when it is http(s), sanitized for storage.
	 *
	 * The scheme is checked first because esc_url_raw() prepends one to input
	 * without a scheme, which would invent a destination the catalog never gave.
	 *
	 * @param mixed $value The raw catalog value.
	 *
	 * @return string The sanitized URL, or an empty string.
	 */
	private static function web_url( $value ): string {
		$url    = trim( Cast::to_string( $value ) );
		$scheme = strtolower( (string) wp_parse_url( $url, PHP_URL_SCHEME ) );

		if ( ! in_array( $scheme, [ 'http', 'https' ], true ) ) {
			return '';
		}

		return esc_url_raw( $url, [ 'http', 'https' ] );
	}

	/**
	 * Converts the tier to an associative array.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, mixed>
	 */
	public function to_array(): array {
		return $this->attributes;
	}

	/**
	 * Gets the tier slug.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_tier_slug(): string {
		return $this->attributes['tier_slug'];
	}

	/**
	 * Gets the tier display name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->attributes['name'];
	}

	/**
	 * Gets the tier rank for ordering and comparison.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_rank(): int {
		return $this->attributes['rank'];
	}

	/**
	 * Gets the tier price in the smallest currency unit (e.g. cents for USD).
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_price(): int {
		return $this->attributes['price'];
	}

	/**
	 * Gets the currency code (e.g. "USD").
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_currency(): string {
		return $this->attributes['currency'];
	}

	/**
	 * Gets the herald slugs associated with this tier.
	 *
	 * @since 1.0.0
	 *
	 * @return string[]
	 */
	public function get_herald_slugs(): array {
		return $this->attributes['herald_slugs'];
	}

	/**
	 * Gets the purchase URL for this tier.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_purchase_url(): string {
		return $this->attributes['purchase_url'];
	}

	/**
	 * Gets the upgrade URL for this tier.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_upgrade_url(): string {
		return $this->attributes['upgrade_url'];
	}
}
