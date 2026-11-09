<?php
/**
 * Migrations Statuses Enum.
 *
 * @since 0.0.1
 *
 * @package TEC\Common\StellarWP\Migrations\Enums
 */

namespace TEC\Common\StellarWP\Migrations\Enums;

use TEC\Common\MyCLabs\Enum\Enum;

/**
 * Migrations Statuses enum.
 *
 * @since 0.0.1
 *
 * @extends Enum<Status::*>
 *
 * @method static self CANCELED()
 * @method static self COMPLETED()
 * @method static self FAILED()
 * @method static self NOT_APPLICABLE()
 * @method static self PAUSED()
 * @method static self PENDING()
 * @method static self REVERTED()
 * @method static self RUNNING()
 * @method static self SCHEDULED()
 */
class Status extends Enum {
	/**
	 * Data migration status 'Canceled'.
	 *
	 * @since 0.0.1
	 */
	private const CANCELED = 'canceled';

	/**
	 * Data migration status 'Completed'.
	 *
	 * @since 0.0.1
	 */
	private const COMPLETED = 'completed';

	/**
	 * Data migration status 'Failed'.
	 *
	 * @since 0.0.1
	 */
	private const FAILED = 'failed';

	/**
	 * Data migration status 'Not Applicable'.
	 *
	 * Used for migrations that are not applicable to the current site.
	 *
	 * @since 0.0.1
	 */
	private const NOT_APPLICABLE = 'not-applicable';

	/**
	 * Data migration status 'Paused'.
	 *
	 * @since 0.0.1
	 */
	private const PAUSED = 'paused';

	/**
	 * Data migration status 'Scheduled'.
	 *
	 * @since 0.0.1
	 */
	private const SCHEDULED = 'scheduled';

	/**
	 * Data migration status 'Pending'.
	 *
	 * @since 0.0.1
	 */
	private const PENDING = 'pending';

	/**
	 * Data migration status 'Reverted'.
	 *
	 * Used for migrations that have been manually rolled back successfully.
	 *
	 * @since 0.0.1
	 */
	private const REVERTED = 'reverted';

	/**
	 * Data migration status 'Running'.
	 *
	 * @since 0.0.1
	 */
	private const RUNNING = 'running';

	/**
	 * Returns the human-readable label for the status.
	 *
	 * @since 0.0.1
	 *
	 * @return string The label.
	 */
	public function get_label(): string {
		switch ( $this->getValue() ) {
			case self::PENDING:
				return _x( 'Migration not started', 'Migration status', 'stellarwp-migrations' );
			case self::RUNNING:
				return _x( 'Migration in progress', 'Migration status', 'stellarwp-migrations' );
			case self::COMPLETED:
				return _x( 'Completed successfully!', 'Migration status', 'stellarwp-migrations' );
			case self::FAILED:
				return _x( 'Failed', 'Migration status', 'stellarwp-migrations' );
			case self::NOT_APPLICABLE:
				return _x( 'Not applicable', 'Migration status', 'stellarwp-migrations' );
			case self::PAUSED:
				return _x( 'Paused', 'Migration status', 'stellarwp-migrations' );
			case self::CANCELED:
				return _x( 'Canceled', 'Migration status', 'stellarwp-migrations' );
			case self::SCHEDULED:
				return _x( 'Scheduled', 'Migration status', 'stellarwp-migrations' );
			case self::REVERTED:
				return _x( 'Rolled back', 'Migration status', 'stellarwp-migrations' );
			default:
				return _x( 'Unknown', 'Migration status', 'stellarwp-migrations' );
		}
	}
}
