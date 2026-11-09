<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Features;

use TEC\Common\LiquidWeb\Harbor\Traits\With_Debugging;
use WP_Error;

/**
 * Manages in-memory caching and delegates feature resolution to Resolve_Feature_Collection.
 *
 * Resolution is cheap (iterates cached catalog and licensing arrays), so this
 * class only caches the result for the current request. Fresh requests always
 * resolve from the upstream caches, which are the single source of truth.
 *
 * @since 1.0.0
 */
class Feature_Repository {

	use With_Debugging;

	/**
	 * The feature collection resolver.
	 *
	 * @since 1.0.0
	 *
	 * @var Resolve_Feature_Collection
	 */
	private Resolve_Feature_Collection $resolver;

	/**
	 * In-memory cache of the resolved result for the current request.
	 *
	 * @since 1.0.0
	 *
	 * @var Feature_Collection|WP_Error|null
	 */
	private $cached;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Resolve_Feature_Collection $resolver The feature collection resolver.
	 */
	public function __construct( Resolve_Feature_Collection $resolver ) {
		$this->resolver = $resolver;
	}

	/**
	 * Gets the resolved feature collection, using the in-memory cache when available.
	 *
	 * @since 1.0.0
	 *
	 * @return Feature_Collection|WP_Error
	 */
	public function get() {
		if ( $this->cached !== null ) {
			return $this->cached;
		}

		return $this->resolve();
	}

	/**
	 * Clears the in-memory cache and re-resolves.
	 *
	 * @since 1.0.0
	 *
	 * @return Feature_Collection|WP_Error
	 */
	public function refresh() {
		$this->cached = null;

		return $this->resolve();
	}

	/**
	 * Delegates resolution to the resolver and caches the result in memory.
	 *
	 * @since 1.0.0
	 *
	 * @return Feature_Collection|WP_Error
	 */
	protected function resolve() {
		$result       = ( $this->resolver )();
		$this->cached = $result;

		if ( is_wp_error( $result ) ) {
			static::debug_log(
				sprintf(
					'Feature resolution failed: [%s] %s',
					$result->get_error_code(),
					$result->get_error_message()
				)
			);
		}

		return $result;
	}
}
