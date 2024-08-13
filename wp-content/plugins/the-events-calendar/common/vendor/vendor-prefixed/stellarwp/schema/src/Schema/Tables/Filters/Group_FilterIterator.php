<?php
/**
 * @license GPL-2.0
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace TEC\Common\StellarWP\Schema\Tables\Filters;

class Group_FilterIterator extends \FilterIterator implements \Countable {
	/**
	 * Groups to filter.
	 *
	 * @since 1.0.0
	 *
	 * @var array<string>
	 */
	private $groups = [];

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string> $groups Paths to filter.
	 * @param \Iterator $iterator Iterator to filter.
	 */
	public function __construct( array $groups, \Iterator $iterator ) {
		parent::__construct( $iterator );

		$this->groups = (array) $groups;
	}

	/**
	 * @inheritDoc
	 */
	public function accept(): bool {
		$table = $this->getInnerIterator()->current();

		return in_array( $table::group_name(), $this->groups, true );
	}

	/**
	 * @inheritDoc
	 */
	public function count(): int {
		return iterator_count( $this->getInnerIterator() );
	}
}
