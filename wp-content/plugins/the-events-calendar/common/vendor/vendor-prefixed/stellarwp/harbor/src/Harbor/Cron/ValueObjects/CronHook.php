<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Cron\ValueObjects;

/**
 * Cron event hook name constants.
 *
 * @since 1.0.0
 */
class CronHook {

	/**
	 * Hook for the 12-hour license and catalog data refresh.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const DATA_REFRESH = 'lw_harbor_data_refresh';
}
