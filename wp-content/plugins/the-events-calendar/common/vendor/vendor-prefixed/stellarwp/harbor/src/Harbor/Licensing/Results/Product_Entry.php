<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Licensing\Results;

use DateTimeImmutable;
use TEC\Common\LiquidWeb\Harbor\Licensing\Enums\Validation_Status;
use TEC\Common\LiquidWeb\Harbor\Utils\Cast;
use TEC\Common\LiquidWeb\LicensingApiClient\Responses\Product\ValueObjects\CatalogEntry;

/**
 * A single product entry from the Liquid Web v1 licensing catalog.
 *
 * Immutable value object hydrated from the GET /stellarwp/v4/products response.
 * Mirrors the licensing service's Catalog_Entry_Result API response shape.
 *
 * @since 1.0.0
 *
 * @phpstan-type ProductAttributes array{
 *     product_slug: string,
 *     tier: string,
 *     status: string,
 *     expires: string,
 *     site_limit: int,
 *     active_count: int,
 *     activation_domains: string[],
 *     activated_here: ?bool,
 *     validation_status: ?string,
 *     capabilities: string[],
 * }
 */
final class Product_Entry {

	/**
	 * The product entry attributes.
	 *
	 * @since 1.0.0
	 *
	 * @var ProductAttributes
	 */
	protected array $attributes = [
		'product_slug'       => '',
		'tier'               => '',
		'status'             => '',
		'expires'            => '1970-01-01 00:00:00',
		'site_limit'         => 0,
		'active_count'       => 0,
		'activation_domains' => [],
		'activated_here'     => null,
		'validation_status'  => null,
		'capabilities'       => [],
	];

	/**
	 * Constructor for a Product_Entry.
	 *
	 * @since 1.0.0
	 *
	 * @phpstan-param ProductAttributes $attributes
	 *
	 * @param array $attributes The product entry attributes.
	 *
	 * @return void
	 */
	public function __construct( array $attributes ) {
		$this->attributes = $attributes;
	}

	/**
	 * Creates a Product_Entry from a CatalogEntry returned by the licensing API client.
	 *
	 * @since 1.0.0
	 *
	 * @param CatalogEntry $entry The catalog entry from the API client.
	 *
	 * @return self
	 */
	public static function from_catalog_entry( CatalogEntry $entry ): self {
		return new self(
			[
				'product_slug'       => $entry->productSlug,
				'tier'               => $entry->tier,
				'status'             => $entry->status,
				'expires'            => $entry->expires->format( 'Y-m-d H:i:s' ),
				'site_limit'         => $entry->activations->siteLimit,
				'active_count'       => $entry->activations->activeCount,
				'activation_domains' => array_keys( $entry->activations->domains ),
				'activated_here'     => $entry->activatedHere,
				'validation_status'  => $entry->validationStatus,
				'capabilities'       => $entry->capabilities->toArray(),
			]
		);
	}

	/**
	 * Creates a Product_Entry from a raw attribute array.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $data The product data.
	 *
	 * @return self
	 */
	public static function from_array( array $data ): self {
		$activations  = isset( $data['activations'] ) && is_array( $data['activations'] ) ? $data['activations'] : [];
		$raw_caps     = isset( $data['capabilities'] ) && is_array( $data['capabilities'] ) ? $data['capabilities'] : [];
		$capabilities = array_values( array_filter( array_map( [ Cast::class, 'to_string' ], $raw_caps ) ) );
		$raw_domains  = isset( $activations['domains'] ) && is_array( $activations['domains'] ) ? $activations['domains'] : [];
		if ( ! empty( $raw_domains ) && is_array( reset( $raw_domains ) ) ) {
			$activation_domains = array_keys( $raw_domains );
		} else {
			$activation_domains = array_values( array_filter( array_map( [ Cast::class, 'to_string' ], $raw_domains ) ) );
		}

		return new self(
			[
				'product_slug'       => Cast::to_string( $data['product_slug'] ?? '' ),
				'tier'               => Cast::to_string( $data['tier'] ?? '' ),
				'status'             => Cast::to_string( $data['status'] ?? '' ),
				'expires'            => Cast::to_string( $data['expires'] ?? '' ),
				'site_limit'         => Cast::to_int( $activations['site_limit'] ?? 0 ),
				'active_count'       => Cast::to_int( $activations['active_count'] ?? 0 ),
				'activation_domains' => $activation_domains,
				'activated_here'     => isset( $data['activated_here'] ) ? Cast::to_bool( $data['activated_here'] ) : null,
				'validation_status'  => isset( $data['validation_status'] ) ? Cast::to_string( $data['validation_status'] ) : null,
				'capabilities'       => $capabilities,
			]
		);
	}

	/**
	 * Converts the product entry to an associative array matching the API response shape.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, mixed>
	 */
	public function to_array(): array {
		$data = [
			'product_slug' => $this->get_product_slug(),
			'tier'         => $this->get_tier(),
			'status'       => $this->get_status(),
			'expires'      => $this->get_expires()->format( 'Y-m-d H:i:s' ),
			'activations'  => [
				'site_limit'   => $this->get_site_limit(),
				'active_count' => $this->get_active_count(),
				'over_limit'   => $this->is_over_limit(),
				'domains'      => $this->get_activation_domains(),
			],
			'capabilities' => $this->get_capabilities(),
		];

		if ( $this->get_activated_here() !== null ) {
			$data['activated_here'] = $this->get_activated_here();
		}

		if ( $this->get_validation_status() !== null ) {
			$data['validation_status'] = $this->get_validation_status();
			$data['is_valid']          = $this->is_valid();
		}

		return $data;
	}

	/**
	 * Gets the product slug.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_product_slug(): string {
		return $this->attributes['product_slug'];
	}

	/**
	 * Gets the subscription tier.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_tier(): string {
		return $this->attributes['tier'];
	}

	/**
	 * Gets the entitlement status.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_status(): string {
		return $this->attributes['status'];
	}

	/**
	 * Gets the expiration date.
	 *
	 * @since 1.0.0
	 *
	 * @return DateTimeImmutable
	 */
	public function get_expires(): DateTimeImmutable {
		return new DateTimeImmutable( $this->attributes['expires'] );
	}

	/**
	 * Gets the maximum number of site activations (0 = unlimited).
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_site_limit(): int {
		return $this->attributes['site_limit'];
	}

	/**
	 * Gets the current number of active site activations.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_active_count(): int {
		return $this->attributes['active_count'];
	}

	/**
	 * Gets the domains currently activated under this entitlement.
	 *
	 * @since 1.0.0
	 *
	 * @return string[]
	 */
	public function get_activation_domains(): array {
		return $this->attributes['activation_domains'];
	}

	/**
	 * Gets whether this product is activated on the requesting domain.
	 *
	 * Returns null when no domain was provided in the request.
	 *
	 * @since 1.0.0
	 *
	 * @return bool|null
	 */
	public function get_activated_here(): ?bool {
		return $this->attributes['activated_here'];
	}

	/**
	 * Gets the list of feature slugs this license grants access to.
	 *
	 * @since 1.0.0
	 *
	 * @return string[]
	 */
	public function get_capabilities(): array {
		return $this->attributes['capabilities'];
	}

	/**
	 * Gets the validation status string, or null when not provided.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null A Validation_Status constant value, or null.
	 */
	public function get_validation_status(): ?string {
		return $this->attributes['validation_status'];
	}

	/**
	 * Whether the product's validation status is valid.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_valid(): bool {
		return $this->get_validation_status() === Validation_Status::VALID;
	}

	/**
	 * Whether the product has exceeded its activation seat limit.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_over_limit(): bool {
		return $this->get_site_limit() > 0 && $this->get_active_count() > $this->get_site_limit();
	}
}
