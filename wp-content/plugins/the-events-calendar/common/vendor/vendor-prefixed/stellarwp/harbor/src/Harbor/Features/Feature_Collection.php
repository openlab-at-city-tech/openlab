<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Features;

use TEC\Common\LiquidWeb\Harbor\Features\Types\Feature;
use TEC\Common\LiquidWeb\Harbor\Features\Types\Plugin;
use TEC\Common\LiquidWeb\Harbor\Features\Types\Theme;
use TEC\Common\LiquidWeb\Harbor\Utils\Collection;

/**
 * A collection of Feature objects, keyed by slug.
 *
 * @since 1.0.0
 *
 * @extends Collection<Feature>
 */
class Feature_Collection extends Collection {

	/**
	 * Adds a feature to the collection.
	 *
	 * @since 1.0.0
	 *
	 * @param Feature $feature Feature instance.
	 *
	 * @return Feature
	 */
	public function add( Feature $feature ): Feature {
		if ( ! $this->offsetExists( $feature->get_slug() ) ) {
			$this->offsetSet( $feature->get_slug(), $feature );
		}

		return $this->offsetGet( $feature->get_slug() ) ?? $feature;
	}

	/**
	 * Alias of offsetGet().
	 *
	 * @since 1.0.0
	 *
	 * @param string $offset The feature slug.
	 *
	 * @return Feature|null
	 */
	public function get( $offset ): ?Feature { // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found -- Narrows return type for IDE support.
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

		foreach ( $this as $feature ) {
			$data[] = $feature->to_array();
		}

		return $data;
	}

	/**
	 * Creates a Feature_Collection from an array of Feature objects or raw data arrays.
	 *
	 * When given raw arrays, dispatches to the correct subclass based on the 'type' field.
	 * Unknown types are skipped.
	 *
	 * @since 1.0.0
	 *
	 * @param array<Feature|array<string, mixed>> $data Feature objects or raw arrays.
	 *
	 * @return self
	 */
	public static function from_array( array $data ): self {
		$collection = new self();

		foreach ( $data as $item ) {
			if ( $item instanceof Feature ) {
				$collection->add( $item );
			} elseif ( is_array( $item ) ) {
				$type = $item['type'] ?? '';

				if ( $type === Feature::TYPE_PLUGIN ) {
					$feature = Plugin::from_array( $item );
				} elseif ( $type === Feature::TYPE_THEME ) {
					$feature = Theme::from_array( $item );
				} else {
					continue;
				}

				$collection->add( $feature );
			}
		}

		return $collection;
	}

	/**
	 * Filters the collection by product, tier, availability and/or type.
	 *
	 * All parameters are optional. When null, that criterion is not applied.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null $product   Filter by product (e.g. 'LearnDash', 'TEC').
	 * @param string|null $tier      Filter by tier (e.g. 'Tier 1', 'Tier 2').
	 * @param bool|null   $available Filter by availability (true/false).
	 * @param string|null $type      Filter by feature type (a Feature::TYPE_* constant).
	 *
	 * @return Feature_Collection
	 */
	public function filter( ?string $product = null, ?string $tier = null, ?bool $available = null, ?string $type = null ): Feature_Collection {
		$filtered = new self();

		foreach ( $this as $feature ) {
			if ( $product !== null && $feature->get_product() !== $product ) {
				continue;
			}

			if ( $tier !== null && $feature->get_tier() !== $tier ) {
				continue;
			}

			if ( $available !== null && $feature->is_available() !== $available ) {
				continue;
			}

			if ( $type !== null && $feature->get_type() !== $type ) {
				continue;
			}

			$filtered->add( $feature );
		}

		return $filtered;
	}
}
