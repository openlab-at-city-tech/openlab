<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Cron;

use TEC\Common\LiquidWeb\Harbor\Contracts\Abstract_Provider;
use TEC\Common\LiquidWeb\Harbor\Cron\Actions\Handle_Unschedule_Cron_Data_Refresh;
use TEC\Common\LiquidWeb\Harbor\Cron\Jobs\Refresh_Catalog_Job;
use TEC\Common\LiquidWeb\Harbor\Cron\Jobs\Refresh_License_Job;
use TEC\Common\LiquidWeb\Harbor\Cron\ValueObjects\CronHook;
use TEC\Common\LiquidWeb\Harbor\Utils\Version;

/**
 * Registers the scheduled data refresh cron job.
 *
 * @since 1.0.0
 */
final class Provider extends Abstract_Provider {

	/**
	 * @inheritDoc
	 */
	public function register(): void {
		$this->container->singleton( Refresh_Catalog_Job::class );
		$this->container->singleton( Refresh_License_Job::class );
		$this->container->singleton( Handle_Unschedule_Cron_Data_Refresh::class );

		if ( ! Version::should_handle( 'cron_data_refresh' ) ) {
			return;
		}

		add_action(
			CronHook::DATA_REFRESH,
			function () {
				$this->container->get( Refresh_Catalog_Job::class )->run();
				$this->container->get( Refresh_License_Job::class )->run();
			}
		);

		add_action(
			'init',
			static function () {
				if ( ! wp_next_scheduled( CronHook::DATA_REFRESH ) ) {
					wp_schedule_event( time(), 'twicedaily', CronHook::DATA_REFRESH );
				}
			}
		);

		add_action(
			'deactivated_plugin',
			function () {
				$this->container->get( Handle_Unschedule_Cron_Data_Refresh::class )();
			}
		);

		add_action(
			'switch_theme',
			function () {
				$this->container->get( Handle_Unschedule_Cron_Data_Refresh::class )();
			}
		);
	}
}
