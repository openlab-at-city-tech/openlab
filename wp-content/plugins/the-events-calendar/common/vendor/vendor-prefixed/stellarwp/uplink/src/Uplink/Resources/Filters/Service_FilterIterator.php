<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Resources\Filters;

use Countable;
use FilterIterator;
use TEC\Common\StellarWP\Uplink\Resources\Service;

/**
 * @method Service current()
 */
class Service_FilterIterator extends FilterIterator implements Countable {

	/**
	 * @inheritDoc
	 */
	public function accept(): bool {
		$resource = $this->getInnerIterator()->current();

		return 'service' === $resource->get_type();
	}

	/**
	 * @inheritDoc
	 */
	public function count() : int {
		return iterator_count( $this );
	}

}
