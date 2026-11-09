<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Portal\Results;

use TEC\Common\LiquidWeb\Harbor\Utils\Collection;

/**
 * A collection of Catalog_Tier objects, keyed by slug.
 *
 * @since 1.0.0
 *
 * @extends Collection<Catalog_Tier>
 */
final class Tier_Collection extends Collection {

	/**
	 * Adds a tier to the collection.
	 *
	 * @since 1.0.0
	 *
	 * @param Catalog_Tier $tier Tier instance.
	 *
	 * @return Catalog_Tier
	 */
	public function add( Catalog_Tier $tier ): Catalog_Tier {
		if ( ! $this->offsetExists( $tier->get_tier_slug() ) ) {
			$this->offsetSet( $tier->get_tier_slug(), $tier );
		}

		return $this->offsetGet( $tier->get_tier_slug() ) ?? $tier;
	}

	/**
	 * Alias of offsetGet().
	 *
	 * @since 1.0.0
	 *
	 * @param string $offset The tier slug.
	 *
	 * @return Catalog_Tier|null
	 */
	public function get( $offset ): ?Catalog_Tier { // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found -- Narrows return type for IDE support.
		return parent::get( $offset );
	}
}
