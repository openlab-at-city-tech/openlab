<?php
/**
 * Migration Log Types Enum.
 *
 * @since 0.0.1
 *
 * @package TEC\Common\StellarWP\Migrations\Enums
 */

namespace TEC\Common\StellarWP\Migrations\Enums;

use TEC\Common\MyCLabs\Enum\Enum;

/**
 * Migration Log Types enum.
 *
 * @since 0.0.1
 *
 * @extends Enum<Log_Type::*>
 *
 * @method static self INFO()
 * @method static self WARNING()
 * @method static self ERROR()
 * @method static self DEBUG()
 */
class Log_Type extends Enum {
	/**
	 * Data migration log type 'Info'.
	 *
	 * @since 0.0.1
	 */
	private const INFO = 'info';

	/**
	 * Data migration log type 'Warning'.
	 *
	 * @since 0.0.1
	 */
	private const WARNING = 'warning';

	/**
	 * Data migration log type 'Error'.
	 *
	 * @since 0.0.1
	 */
	private const ERROR = 'error';

	/**
	 * Data migration log type 'Debug'.
	 *
	 * @since 0.0.1
	 */
	private const DEBUG = 'debug';


	/**
	 * Returns the human-readable label for the log type.
	 *
	 * @since 0.0.1
	 *
	 * @return string The label.
	 */
	public function get_label(): string {
		switch ( $this->getValue() ) {
			case self::INFO:
				return _x( 'Info', 'Migration log type', 'stellarwp-migrations' );
			case self::WARNING:
				return _x( 'Warning', 'Migration log type', 'stellarwp-migrations' );
			case self::ERROR:
				return _x( 'Error', 'Migration log type', 'stellarwp-migrations' );
			case self::DEBUG:
				return _x( 'Debug', 'Migration log type', 'stellarwp-migrations' );
			default:
				return _x( 'Unknown', 'Migration log type', 'stellarwp-migrations' );
		}
	}
}
