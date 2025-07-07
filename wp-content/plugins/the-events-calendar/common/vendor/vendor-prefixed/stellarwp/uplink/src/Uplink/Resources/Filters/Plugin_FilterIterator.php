<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Resources\Filters;

use Countable;
use FilterIterator;
use TEC\Common\StellarWP\Uplink\Resources\Plugin;

/**
 * @method Plugin current()
 */
class Plugin_FilterIterator extends FilterIterator implements Countable {

	/**
	 * @inheritDoc
	 */
	public function accept(): bool {
		$resource = $this->getInnerIterator()->current();

		return 'plugin' === $resource->get_type();
	}

	/**
	 * @inheritDoc
	 */
	public function count() : int {
		return iterator_count( $this );
	}

}
