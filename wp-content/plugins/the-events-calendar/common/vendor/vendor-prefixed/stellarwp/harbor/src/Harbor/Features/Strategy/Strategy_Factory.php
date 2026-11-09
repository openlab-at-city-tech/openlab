<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Features\Strategy;

use InvalidArgumentException;
use TEC\Common\LiquidWeb\Harbor\Features\Contracts\Strategy;
use TEC\Common\LiquidWeb\Harbor\Features\Types\Feature;
use TEC\Common\LiquidWeb\Harbor\Features\Types\Plugin;
use TEC\Common\LiquidWeb\Harbor\Features\Types\Service;
use TEC\Common\LiquidWeb\Harbor\Features\Types\Theme;

/**
 * Factory that creates Strategy instances for features.
 *
 * Maps feature type strings to their corresponding Strategy classes.
 * Each call creates a new Strategy instance bound to the given Feature.
 *
 * @since 1.0.0
 */
class Strategy_Factory {

	/**
	 * Creates the correct strategy for a given feature.
	 *
	 * @since 1.0.0
	 *
	 * @param Feature $feature The feature to create a strategy for.
	 *
	 * @throws InvalidArgumentException If no strategy exists for the feature's type.
	 *
	 * @return Strategy A new Strategy instance bound to the given Feature.
	 */
	public function make( Feature $feature ): Strategy {
		switch ( $feature->get_type() ) {
			case Feature::TYPE_PLUGIN:
				if ( ! $feature instanceof Plugin ) {
					throw new InvalidArgumentException(
						sprintf( 'Feature type "%s" requires a Plugin instance.', Feature::TYPE_PLUGIN )
					);
				}

				return new Plugin_Strategy( $feature );
			case Feature::TYPE_THEME:
				if ( ! $feature instanceof Theme ) {
					throw new InvalidArgumentException(
						sprintf( 'Feature type "%s" requires a Theme instance.', Feature::TYPE_THEME )
					);
				}

				return new Theme_Strategy( $feature );
			case Feature::TYPE_SERVICE:
				if ( ! $feature instanceof Service ) {
					throw new InvalidArgumentException(
						sprintf( 'Feature type "%s" requires a Service instance.', Feature::TYPE_SERVICE )
					);
				}

				return new Service_Strategy( $feature );
			default:
				throw new InvalidArgumentException(
					sprintf( 'No strategy for feature type "%s".', $feature->get_type() )
				);
		}
	}
}
