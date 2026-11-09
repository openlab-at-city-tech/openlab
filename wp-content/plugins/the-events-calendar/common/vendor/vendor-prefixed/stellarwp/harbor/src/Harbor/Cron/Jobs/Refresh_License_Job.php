<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Cron\Jobs;

use TEC\Common\LiquidWeb\Harbor\Licensing\License_Manager;
use TEC\Common\LiquidWeb\Harbor\Site\Data;
use TEC\Common\LiquidWeb\Harbor\Traits\With_Debugging;

/**
 * Refreshes license product data from the remote API.
 *
 * Skipped entirely when no license key is stored since there is nothing
 * to validate against the API.
 *
 * @since 1.0.0
 */
class Refresh_License_Job {

	use With_Debugging;

	/**
	 * @since 1.0.0
	 *
	 * @var License_Manager
	 */
	private License_Manager $license_manager;

	/**
	 * @since 1.0.0
	 *
	 * @var Data
	 */
	private Data $site_data;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param License_Manager $license_manager The license manager.
	 * @param Data            $site_data       The site data.
	 */
	public function __construct( License_Manager $license_manager, Data $site_data ) {
		$this->license_manager = $license_manager;
		$this->site_data       = $site_data;
	}

	/**
	 * Fetch fresh license product data from the remote API.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run(): void {
		if ( ! $this->license_manager->key_exists() ) {
			static::debug_log( 'Skipping license refresh: no key stored.' );

			return;
		}

		$domain = $this->site_data->get_domain();

		static::debug_log(
			sprintf( 'Scheduled license refresh starting for domain "%s".', $domain )
		);

		$this->license_manager->refresh_products( $domain );
	}
}
