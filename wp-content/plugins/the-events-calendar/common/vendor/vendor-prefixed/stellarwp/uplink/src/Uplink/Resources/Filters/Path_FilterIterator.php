<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Resources\Filters;

use Countable;
use FilterIterator;
use Iterator;
use TEC\Common\StellarWP\Uplink\Resources\Plugin;
use TEC\Common\StellarWP\Uplink\Resources\Resource;
use TEC\Common\StellarWP\Uplink\Resources\Service;

/**
 * @method Resource|Plugin|Service current()
 */
class Path_FilterIterator extends FilterIterator implements Countable {

	/**
	 * Paths to filter.
	 *
	 * @since 1.0.0
	 *
	 * @var array<string>
	 */
	private $paths;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param  Iterator<string, Resource>  $iterator  Iterator to filter.
	 * @param  string[]  $paths  Paths to filter.
	 */
	public function __construct( Iterator $iterator, array $paths ) {
		parent::__construct( $iterator );

		$this->paths = $paths;
	}

	/**
	 * @inheritDoc
	 */
	public function accept(): bool {
		$resource = $this->getInnerIterator()->current();

		return in_array( $resource->get_path(), $this->paths, true );
	}

	/**
	 * @inheritDoc
	 */
	public function count() : int {
		return iterator_count( $this );
	}

}
