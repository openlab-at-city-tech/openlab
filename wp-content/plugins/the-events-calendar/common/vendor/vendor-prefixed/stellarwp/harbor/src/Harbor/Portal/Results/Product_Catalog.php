<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Portal\Results;

use TEC\Common\LiquidWeb\Harbor\Utils\Cast;

/**
 * A single product's catalog of tiers and features.
 *
 * Immutable value object hydrated from the Commerce Portal catalog API response.
 *
 * @since 1.0.0
 */
final class Product_Catalog {

	/**
	 * The product ID from the Commerce Portal.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected string $product_id;

	/**
	 * The product slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected string $product_slug;

	/**
	 * The product display name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected string $product_name;

	/**
	 * The tier collection, sorted by rank.
	 *
	 * @since 1.0.0
	 *
	 * @var Tier_Collection
	 */
	protected Tier_Collection $tiers;

	/**
	 * The feature objects.
	 *
	 * @since 1.0.0
	 *
	 * @var Catalog_Feature[]
	 */
	protected array $features;

	/**
	 * Constructor for a Product_Catalog.
	 *
	 * @since 1.0.0
	 *
	 * @param string            $product_id   The product ID.
	 * @param string            $product_slug The product slug.
	 * @param string            $product_name The product display name.
	 * @param Tier_Collection   $tiers        The tier collection.
	 * @param Catalog_Feature[] $features     The feature objects.
	 *
	 * @return void
	 */
	public function __construct(
		string $product_id,
		string $product_slug,
		string $product_name,
		Tier_Collection $tiers,
		array $features
	) {
		$this->product_id   = $product_id;
		$this->product_slug = $product_slug;
		$this->product_name = $product_name;
		$this->tiers        = $tiers;
		$this->features     = $features;
	}

	/**
	 * Creates a Product_Catalog from a raw data array.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $data The product catalog data.
	 *
	 * @return self
	 */
	public static function from_array( array $data ): self {
		$tiers = new Tier_Collection();

		if ( isset( $data['tiers'] ) && is_array( $data['tiers'] ) ) {
			$tier_objects = [];

			foreach ( $data['tiers'] as $tier_data ) {
				if ( is_array( $tier_data ) ) {
					/** @var array<string, mixed> $tier_data */
					$tier_objects[] = Catalog_Tier::from_array( $tier_data );
				}
			}

			usort(
				$tier_objects,
				static function ( Catalog_Tier $a, Catalog_Tier $b ): int {
					return $a->get_rank() <=> $b->get_rank();
				}
			);

			foreach ( $tier_objects as $tier ) {
				$tiers->add( $tier );
			}
		}

		$features = [];

		if ( isset( $data['features'] ) && is_array( $data['features'] ) ) {
			foreach ( $data['features'] as $feature_data ) {
				if ( is_array( $feature_data ) ) {
					/** @var array<string, mixed> $feature_data */
					$features[] = Catalog_Feature::from_array( $feature_data );
				}
			}
		}

		return new self(
			Cast::to_string( $data['product_id'] ?? '' ),
			Cast::to_string( $data['product_slug'] ?? '' ),
			Cast::to_string( $data['product_name'] ?? '' ),
			$tiers,
			$features,
		);
	}

	/**
	 * Converts the product catalog to an associative array.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, mixed>
	 */
	public function to_array(): array {
		return [
			'product_id'   => $this->product_id,
			'product_slug' => $this->product_slug,
			'product_name' => $this->product_name,
			'tiers'        => array_values(
				array_map(
					static function ( Catalog_Tier $tier ): array {
						return $tier->to_array();
					},
					iterator_to_array( $this->tiers )
				)
			),
			'features'     => array_map(
				static function ( Catalog_Feature $feature ): array {
					return $feature->to_array();
				},
				$this->features
			),
		];
	}

	/**
	 * Gets the product ID.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_product_id(): string {
		return $this->product_id;
	}

	/**
	 * Gets the product slug.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_product_slug(): string {
		return $this->product_slug;
	}

	/**
	 * Gets the product display name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_product_name(): string {
		return $this->product_name;
	}

	/**
	 * Gets the tier collection, ordered by rank.
	 *
	 * @since 1.0.0
	 *
	 * @return Tier_Collection
	 */
	public function get_tiers(): Tier_Collection {
		return $this->tiers;
	}

	/**
	 * Gets a tier by its slug, or null if not found.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The tier slug.
	 *
	 * @return Catalog_Tier|null
	 */
	public function get_tier_by_slug( string $slug ): ?Catalog_Tier {
		return $this->tiers->get( $slug );
	}

	/**
	 * Gets the hydrated feature objects.
	 *
	 * @since 1.0.0
	 *
	 * @return Catalog_Feature[]
	 */
	public function get_features(): array {
		return $this->features;
	}
}
