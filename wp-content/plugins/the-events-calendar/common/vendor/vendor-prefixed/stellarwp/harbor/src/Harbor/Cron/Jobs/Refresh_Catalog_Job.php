<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Cron\Jobs;

use TEC\Common\LiquidWeb\Harbor\Portal\Catalog_Repository;
use TEC\Common\LiquidWeb\Harbor\Traits\With_Debugging;

/**
 * Refreshes the product catalog from the remote API.
 *
 * @since 1.0.0
 */
class Refresh_Catalog_Job {

	use With_Debugging;

	/**
	 * @since 1.0.0
	 *
	 * @var Catalog_Repository
	 */
	private Catalog_Repository $catalog;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Catalog_Repository $catalog The catalog repository.
	 */
	public function __construct( Catalog_Repository $catalog ) {
		$this->catalog = $catalog;
	}

	/**
	 * Fetch fresh catalog data from the remote API.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run(): void {
		static::debug_log( 'Scheduled catalog refresh starting.' );

		$this->catalog->refresh();
	}
}
