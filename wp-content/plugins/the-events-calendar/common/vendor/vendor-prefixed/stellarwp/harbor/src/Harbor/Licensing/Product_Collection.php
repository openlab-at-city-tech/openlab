<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Licensing;

use TEC\Common\LiquidWeb\Harbor\Licensing\Results\Product_Entry;
use TEC\Common\LiquidWeb\Harbor\Utils\Collection;

/**
 * A collection of Product_Entry objects, keyed by "product_slug:tier".
 *
 * The licensing API returns one entry per tier for each product, so multiple
 * entries with the same slug but different tiers can coexist. Use
 * get_all_by_slug() to retrieve all tiers.
 *
 * @since 1.0.0
 *
 * @extends Collection<Product_Entry>
 */
final class Product_Collection extends Collection {

	/**
	 * Adds a product entry to the collection, keyed by "slug:tier".
	 *
	 * All entries are stored — no deduplication across tiers.
	 *
	 * @since 1.0.0
	 *
	 * @param Product_Entry $entry Product entry instance.
	 *
	 * @return Product_Entry
	 */
	public function add( Product_Entry $entry ): Product_Entry {
		$this->offsetSet( $entry->get_product_slug() . ':' . $entry->get_tier(), $entry );
		return $entry;
	}

	/**
	 * Returns all stored entries for a given product slug.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The product slug.
	 *
	 * @return Product_Entry[]
	 */
	public function get_all_by_slug( string $slug ): array {
		$entries = [];

		foreach ( $this as $entry ) {
			if ( $entry->get_product_slug() === $slug ) {
				$entries[] = $entry;
			}
		}

		return $entries;
	}

	/**
	 * Returns the entry that is activated on the current domain for a given slug.
	 *
	 * Returns null when no entry for the slug has activated_here set to true.
	 *
	 * @since 1.0.0
	 * @deprecated 2.0.1 Use get_all_by_slug() and check get_activated_here() on each entry.
	 *
	 * @param string $slug The product slug.
	 *
	 * @return Product_Entry|null
	 */
	public function get_activated_entry( string $slug ): ?Product_Entry {
		_deprecated_function( __METHOD__, '2.0.1', __CLASS__ . '::get_all_by_slug()' );

		foreach ( $this as $entry ) {
			if ( $entry->get_product_slug() === $slug && $entry->get_activated_here() ) {
				return $entry;
			}
		}

		return null;
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

		foreach ( $this as $entry ) {
			$data[] = $entry->to_array();
		}

		return $data;
	}

	/**
	 * Creates a Product_Collection from an array of Product_Entry objects or raw data arrays.
	 *
	 * @since 1.0.0
	 *
	 * @param array<Product_Entry|array<string, mixed>> $entries Product entries or raw arrays.
	 *
	 * @return self
	 */
	public static function from_array( array $entries ): self {
		$collection = new self();

		foreach ( $entries as $entry ) {
			if ( $entry instanceof Product_Entry ) {
				$collection->add( $entry );
			} elseif ( is_array( $entry ) ) {
				$collection->add( Product_Entry::from_array( $entry ) );
			}
		}

		return $collection;
	}
}
