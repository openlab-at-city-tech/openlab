<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Features\Strategy;

use TEC\Common\LiquidWeb\Harbor\Features\Contracts\Strategy;
use TEC\Common\LiquidWeb\Harbor\Features\Types\Feature;
use TEC\Common\LiquidWeb\Harbor\Traits\With_Debugging;

/**
 * Base class for feature-gating strategies.
 *
 * Each strategy instance is bound to a single Feature at construction time.
 *
 * @since 1.0.0
 */
abstract class Abstract_Strategy implements Strategy {

	use With_Debugging;

	/**
	 * The feature this strategy operates on.
	 *
	 * @since 1.0.0
	 *
	 * @var Feature
	 */
	protected Feature $feature;

	/**
	 * Construct the strategy.
	 *
	 * @since 1.0.0
	 *
	 * @param Feature $feature The feature this strategy operates on.
	 */
	public function __construct( Feature $feature ) {
		$this->feature = $feature;
	}
}
