<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Portal;

use TEC\Common\LiquidWeb\Harbor\Portal\Results\Product_Catalog;
use TEC\Common\LiquidWeb\Harbor\Utils\Collection;

/**
 * A collection of Product_Catalog objects, keyed by product slug.
 *
 * @since 1.0.0
 *
 * @extends Collection<Product_Catalog>
 */
final class Catalog_Collection extends Collection {

	/**
	 * Adds a product catalog to the collection.
	 *
	 * @since 1.0.0
	 *
	 * @param Product_Catalog $catalog Product catalog instance.
	 *
	 * @return Product_Catalog
	 */
	public function add( Product_Catalog $catalog ): Product_Catalog {
		if ( ! $this->offsetExists( $catalog->get_product_slug() ) ) {
			$this->offsetSet( $catalog->get_product_slug(), $catalog );
		}

		return $this->offsetGet( $catalog->get_product_slug() ) ?? $catalog;
	}

	/**
	 * Alias of offsetGet().
	 *
	 * @since 1.0.0
	 *
	 * @param string $offset The product slug.
	 *
	 * @return Product_Catalog|null
	 */
	public function get( $offset ): ?Product_Catalog { // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found -- Narrows return type for IDE support.
		return parent::get( $offset );
	}

	/**
	 * Converts the collection to an array of raw data arrays.
	 *
	 * @since 1.0.0
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function to_array(): array {
		$data = [];

		foreach ( $this as $catalog ) {
			$data[] = $catalog->to_array();
		}

		return $data;
	}

	/**
	 * Creates a Catalog_Collection from an array of Product_Catalog objects or raw data arrays.
	 *
	 * @since 1.0.0
	 *
	 * @param array<Product_Catalog|array<string, mixed>> $data Product catalogs or raw arrays.
	 *
	 * @return self
	 */
	public static function from_array( array $data ): self {
		$collection = new self();

		foreach ( $data as $item ) {
			if ( $item instanceof Product_Catalog ) {
				$collection->add( $item );
			} elseif ( is_array( $item ) ) {
				$collection->add( Product_Catalog::from_array( $item ) );
			}
		}

		return $collection;
	}
}
