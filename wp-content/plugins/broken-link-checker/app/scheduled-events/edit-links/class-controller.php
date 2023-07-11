<?php
/**
 * Scheduled event for Editing links.
 * A single scheduled event that is used for editing/unlinking links"
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Schedule_Events\Scan
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Scheduled_Events\Edit_Links;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\App\Broken_Links_Actions\Link;
use WPMUDEV_BLC\App\Options\Links_Queue\Model as Queue;
use WPMUDEV_BLC\App\Options\Settings\Model as Settings;
use WPMUDEV_BLC\App\Scheduled_Events\Edit_Links\Controller as Schedule;
use WPMUDEV_BLC\App\Http_Requests\Edit_Complete\Controller as HTTP_Request;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\Core\Traits\Cron;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Schedule_Events\Edit_Links
 */
class Controller extends Base {
	use Cron;

	/**
	 * WP Cron hook to execute when event is run.
	 *
	 * @var string
	 */
	public $cron_hook_name = 'blc_schedule_edit_links';

	/**
	 * BLC settings from options table.
	 *
	 * @var array
	 */
	private $settings = null;

	/**
	 * Init Schedule
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function init() {
		if ( wp_doing_ajax() ) {
			return;
		}

		add_action( 'wpmudev_blc_plugin_deactivated', array( $this, 'deactivate_cron' ) );
	}

	/**
	 * Returns the scheduled event's hook name.
	 * Overriding Trait's method.
	 */
	public function get_hook_name() {
		return $this->cron_hook_name;
	}

	/**
	 * Starts the scheduled scan.
	 */
	public function process_scheduled_event() {
		if ( boolval( Settings::instance()->get( 'use_legacy_blc_version' ) ) ) {
			return false;
		}

		$queue           = Queue::instance()->get_queue_data();
		$current_task    = Queue::instance()->get_current_task();
		$limit           = Queue::instance()->get( 'forced_limit' );
		$page_links      = $queue['page_links'] ?? array();
		$full_site_links = $queue['full_site_links'] ?? array();
		$result          = array();
		$props           = array();

		// 1. Starting with previously unfinished links.
		// 2.Then continue with page links which are faster.
		// 3.Finally, run full site links.
		if ( ! empty( $current_task['link'] ) ) {
			$current_link_url  = $current_task['link'];
			$current_link_mode = ! empty( $current_task['link_mode'] ) ? $current_task['link_mode'] : null;

			if ( empty( $current_link_url ) || empty( $current_link_mode ) ) {
				// Remove current task from db since it is corrupt.
				Queue::instance()->set(
					array(
						'current_task' => array(),
					)
				);
			}

			$link_index = array_search( untrailingslashit( $current_link_url ), array_map( 'untrailingslashit', wp_list_pluck( $queue[ $current_link_mode ], 'link' ) ) );
			$props      = $queue[ $current_link_mode ][ $link_index ];

			// We need to use the $authors_completed flag too.

			$props['offset'] = $current_task['offset'];
		} elseif ( ! empty( $page_links ) ) {
			$props = $page_links[0];
		} elseif ( ! empty( $full_site_links ) ) {
			$props = $full_site_links[0];
		}

		if ( ! empty( $limit ) ) {
			$props['limit'] = intval( $limit );
		}

		$link = new Link( $props );

		if ( $link instanceof Link ) {
			$result = $link->execute_action();
		}

		Queue::instance()->set( array( 'batch_end_timestamp' => current_time( 'U' ) ) );

		// Set queue offsets and current link.
		if ( ! empty( $result ) && ! is_wp_error( $result ) ) {
			// What to do when link action has been completed.
			if ( ! empty( $result['completed'] ) ) {
				// Removing link.
				Queue::instance()->pull(
					array(
						'link'      => $link->__get( 'link' ),
						'new_link'  => $link->__get( 'new_link' ),
						'full_site' => $link->__get( 'full_site' ),
					)
				);

				Queue::instance()->reset_totals( true );
				// Send an update to HUB API after each link task is completed.
				HTTP_Request::instance()->send_report( $link );
			} else {
				$current_task = Queue::instance()->get_current_task();

				Queue::instance()->set(
					array(
						'current_task' => array(
							'link'          => $link->get_link(),
							'offset'        => $result['offset'] ?? $link->get_offset(),
							'rows'          => ! empty( $result['row_count'] ) ? intval( $result['row_count'] ) : intval( $current_task['rows'] ),
							'link_mode'     => $result['link_mode'],
							'target_tables' => ! empty( $result['target_tables'] ) ? $result['target_tables'] : array(
								'posts'    => ! empty( $current_task['target_tables']['posts'] ) ? boolval( $current_task['target_tables']['posts'] ) : false,
								'postmeta' => ! empty( $current_task['target_tables']['postmeta'] ) ? boolval( $current_task['target_tables']['postmeta'] ) : false,
								'comments' => ! empty( $current_task['target_tables']['comments'] ) ? boolval( $current_task['target_tables']['comments'] ) : false,
								'authors'  => ! empty( $current_task['target_tables']['authors'] ) ? boolval( $current_task['target_tables']['authors'] ) : false,
							),
						)
					)
				);
			}

			Queue::instance()->save();

			// If there is a forced limit, it means that previous batch did not spend whole LIMIT.
			// Using remaining LIMIT for next batch.
			if ( ! empty( $result['force_limit'] ) && is_numeric( $result['force_limit'] ) ) {
				Queue::instance()->set( array( 'force_limit' => intval( $result['force_limit'] ) ) );
				Queue::instance()->save();
			}
		}

		if ( Queue::instance()->queue_is_empty() ) {
			Queue::instance()->clear();
		} else {
			Schedule::instance()->setup( intval( apply_filters( 'wpmudev_blc_link_action_schedule_cooldown_seconds', 15 ) ) );
		}

		return $result;
	}

	/**
	 * Sets up the schedule.
	 *
	 * @param int $seconds
	 *
	 * @return bool
	 */
	public function setup( int $seconds = null ) {
		// Deactivate cron if is already created, so we will replace it later on.
		$this->deactivate_cron();

		// As a single event it will be possible to set custom timestamps to run.
		$this->is_single_event = true;
		// Set the timestamp for single event's next run.
		$this->timestamp = intval( $this->get_timestamp( $seconds ) );

		return $this->activate_cron();
	}

	/**
	 * Returns the timestamp of next scheduled scan.
	 *
	 * @param array $schedule
	 *
	 * @return false|int|null
	 */
	public function get_timestamp( int $seconds = null ) {
		if ( empty( $seconds ) ) {
			$seconds = intval( apply_filters( 'wpmudev_blc_edit_links_cron_seconds', 30 ) );
		}

		return strtotime( "+ $seconds seconds" );
	}

	/**
	 * Sets the scan flag to true. Useful when API sends the SET request, an email about the current schedule should
	 * be sent to schedule receivers.
	 */
	public function set_scan_schedule_flag( bool $flag = true ) {

	}
}
