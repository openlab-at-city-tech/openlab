<?php
/**
 * An endpoint where Hub can send requests and receive current status of Links Queue.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.1.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Hub_Endpoints\Queue_Status
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Hub_Endpoints\Queue_Status;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Controllers\Hub_Endpoint;
use WPMUDEV_BLC\App\Options\Links_Queue\Model as Queue;
use WPMUDEV_BLC\App\Scheduled_Events\Edit_Links\Controller as Schedule;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Hub_Endpoint\Queue_Status
 */
class Controller extends Hub_Endpoint {
	public function process() {
		// If WP Cron is disabled, we can use HUB's pings to trigger cron callback. So it can be called pseudo pseudo cron.
		// Actually no need to relly only on wp cron. We can use Hub pings to speed up things a bit.
		if ( ! Queue::instance()->queue_is_empty() ) {
			// A quick way ti clear Queue, when there is a chance that queue gets stuck for some reason.
			// Not to be used without confirming with WPMUDEV Team that queue needs to be cleared.
			if ( ! empty( $_GET['empty_queue'] ) && 'force' === $_GET['empty_queue'] ) {
				Queue::instance()->clear();
			}

			$last_timestamp = Queue::instance()->batch_end_timestamp();

			if ( ! $last_timestamp || $last_timestamp <= 0 ) {
				$last_timestamp = current_time( 'U' );
				Queue::instance()->set( array( 'batch_end_timestamp' => $last_timestamp ) );
				Queue::instance()->save();
			}

			if ( $last_timestamp > 0 && ( current_time( 'U' ) - $last_timestamp ) >= 15 ) {
				// Deactivate Schedule, so it doesn't get triggered while current action is still in progress (probably impossible since DISABLE_WP_CRON is true).
				Schedule::instance()->deactivate_cron();
				Schedule::instance()->process_scheduled_event();
			}
		}

		$this->output_formatted_response( Queue::instance()->get_queue_data() );
	}

	/**
	 * Sets the endpoint's action vars to be used by Dash plugin.
	 */
	protected function setup_action_vars() {
		$this->endpoint_action_name     = 'blc_get_links_process_status';
		$this->endpoint_action_callback = 'process';
	}

}
