<?php
/**
 * Migrations Operations Enum.
 *
 * @since 0.0.1
 *
 * @package TEC\Common\StellarWP\Migrations\Enums
 */

namespace TEC\Common\StellarWP\Migrations\Enums;

use TEC\Common\MyCLabs\Enum\Enum;

/**
 * Migrations Operations enum.
 *
 * @since 0.0.1
 *
 * @extends Enum<Operation::*>
 *
 * @method static self UP()
 * @method static self DOWN()
 */
class Operation extends Enum {
	/**
	 * Data migration operation 'Up'.
	 *
	 * @since 0.0.1
	 */
	private const UP = 'up';

	/**
	 * Data migration operation 'Down'.
	 *
	 * @since 0.0.1
	 */
	private const DOWN = 'down';

	/**
	 * Returns the human-readable label for the log type.
	 *
	 * @since 0.0.1
	 *
	 * @return string The label.
	 */
	public function get_label(): string {
		switch ( $this->getValue() ) {
			case self::UP:
				return _x( 'Up', 'Migration operation', 'stellarwp-migrations' );
			case self::DOWN:
				return _x( 'Down', 'Migration operation', 'stellarwp-migrations' );
			default:
				return _x( 'Unknown', 'Migration operation', 'stellarwp-migrations' );
		}
	}
}
