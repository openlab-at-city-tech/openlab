<?php
/**
 * The Links Queue Option model
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.1.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Options\Links_Queue
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Options\Links_Queue;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Models\Option;

/**
 * Class Settings
 *
 * @package WPMUDEV_BLC\App\Options\Links_Queue
 */
class Model extends Option {
	/**
	 * Default options. Optional
	 *
	 * @since 2.1.0
	 * @var string|array|null $option_keys
	 */
	public $default = array(
		'total_links'         => 0,
		// int Total number of links (page links and full site links).
		'remaining_links'     => 0,
		// int Number of links that have not been processed yet.
		'links_processed'     => 0,
		// int Number of links processed.
		'full_site_links'     => array(),
		// array List of links to be edited in all site's posts.
		'page_links'          => array(),
		// array List of links to be edited on specific posts.
		'last_run'            => null,
		// int Timestamp that last edit took place.
		'link_list_success'   => array(),
		// array List of links that are successfully edited.
		'link_list_failed'    => array(),
		// array List of links that failed to be edited.
		'link_list_notfound'  => array(),
		// array List of links that were not found.
		'report'              => array(),
		// array
		'forced_limit'        => null,
		// null|int When set this limit will be used. Useful when bash gets completed and more than half of the limit has been spent.
		'current_task'        => array(
			'link'          => '',
			// The link last action reached.
			'offset'        => '',
			// The offset of posts/urls it scanned.
			'rows'          => 0,
			// The number of rows that meet WHERE clause. This is calculated before Links are edited/unlinked.
			'target_tables' => array(
				'posts'    => false, // True if completed checking posts.
				'postmeta' => false, // True if completed checking postmeta.
				'comments' => false, // True if completed checking comments.
				'authors'  => false, // True if completed checking authors.
			),
			'link_mode'     => '',
			// One of full_site_link or page_link.
		),
		'batch_end_timestamp' => 0,
		// Timestamp from when a Link/Batch action was previously completed. Link may not have been completed going through it's origins, but if batch (Limit) is completed, then we keep that timestamp.
	);

	/**
	 * The option_name.
	 *
	 * @since 2.1.0
	 * @var string $name
	 */
	protected $name = 'blc_links_queue';

	/**
	 * Returns the remaining links stored in option.
	 *
	 * @return void
	 */
	public function remaining_links() {
		return $this->get( 'remaining_links' );
	}

	/**
	 * Pushes/adds values to option.
	 *
	 * @param array $batch
	 *
	 * @return false|void
	 */
	public function push( array $batch = array() ) {
		if ( empty( $batch['links'] ) ) {
			return false;
		}

		$page_links      = array();
		$full_site_links = array();

		foreach ( $batch['links'] as $batch_link ) {
			if ( ! empty( $batch_link['full_site'] ) ) {
				$full_site_links[] = $batch_link;
			} else {
				$page_links[] = $batch_link;
			}
		}

		$this->set( array( 'full_site_links' => wp_parse_args( $full_site_links, $this->get( 'full_site_links' ) ) ) );
		$this->set( array( 'page_links' => wp_parse_args( $page_links, $this->get( 'page_links' ) ) ) );
		$this->set( array( 'total_links' => $this->total_links() + count( $batch['links'] ) ) );
		$this->set( array( 'remaining_links' => count( $this->get( 'full_site_links' ) ) + count( $this->get( 'page_links' ) ) ) );

		$this->save();
	}

	/**
	 * Returns the total links that option includes (remaining and completed).
	 *
	 * @return int
	 */
	public function total_links() {
		$queue = $this->get_queue_data();

		return ! empty( $queue['total_links'] ) ? intval( $queue['total_links'] ) : 0;
	}

	/**
	 * Returns the option's value in array format.
	 *
	 * @return array
	 */
	public function get_queue_data() {
		$queue = $this->get();

		if ( ! is_array( $queue ) || empty( $queue ) ) {
			$this->set( $this->get_defaults() );

			$queue = $this->get();
		}

		return $queue;
	}

	/**
	 * Returns an array with the default value for option.
	 *
	 * @param string $key Optional key of default index.
	 *
	 * @return array
	 */
	protected function get_defaults( string $key = null ) {
		if ( ! empty( $key ) ) {
			return $this->default[ $key ] ?? array();
		}

		return $this->default;
	}

	public function pull( array $link = array() ) {
		//if ( empty( $link['link'] ) || ! isset( $link['processed'] ) ) {
		if ( empty( $link['link'] ) ) {
			return false;
		}

		$url             = $link['link'];
		$processed       = ! empty( $link['processed'] );
		$full_site       = ! empty( $link['full_site'] );
		$full_site_links = $this->get( 'full_site_links' );
		$page_links      = $this->get( 'page_links' );

		// Remove link from full site links, if link is full site link, else remove from page links.
		if ( $full_site ) {
			$link_index = array_search( untrailingslashit( $url ), array_map( 'untrailingslashit', wp_list_pluck( $full_site_links, 'link' ) ) );

			unset( $full_site_links[ $link_index ] );
			// We use array_values to re-index array after unset.
			$this->set( array( 'full_site_links' => array_values( $full_site_links ) ) );
		} else {
			$link_index = array_search( untrailingslashit( $url ), array_map( 'untrailingslashit', wp_list_pluck( $page_links, 'link' ) ) );

			unset( $page_links[ $link_index ] );
			$this->set( array( 'page_links' => array_values( $page_links ) ) );
		}

		// After removing link, update processed and not found links lists.
		if ( ! $link_index ) {
			$notfound = $this->get( 'link_list_notfound' );

			if ( ! is_array( $notfound ) ) {
				$notfound = array();
			}

			$notfound[] = $url;

			$this->set( array( 'link_list_notfound' => array_unique( $notfound ) ) );
		} elseif ( $processed ) {
			$processed_links_num = intval( $this->get( 'links_processed' ) ) + 1;
			$this->set( array( 'links_processed' => $processed_links_num ) );
		}

		//$this->reset_totals();

		return $this->save();
	}

	/**
	 * Resets the total links count.
	 *
	 * @return void
	 */
	public function reset_totals( bool $force_save = false ) {
		$this->set(
			array(
				'remaining_links' => intval( $this->get( 'remaining_links' ) ) - 1,
				'links_processed' => intval( $this->get( 'links_processed' ) ) + 1,
				//'current_task'    => array(),
				'current_task'    => $this->get_defaults( 'current_task' ),
				'last_run'        => time(),
				'forced_limit'    => null,
			)
		);

		return $force_save ? $this->save() : true;
	}

	/**
	 * Updates queue report.
	 *
	 * @param array $new_link_report
	 *
	 * @return void
	 */
	public function push_queue_report( array $new_link_report = array() ) {
		$queue_report   = $this->get_report();
		$queue_report[] = $new_link_report;

		if ( ! empty( $new_link_report['link'] ) ) {
			if ( ! empty( $new_link_report['status'] ) ) {
				$queue_report['link_list_success'] = $new_link_report['link'];
			} else {
				$queue_report['link_list_failed'] = $new_link_report['link'];
			}

			// For fullsite links `notfound` is used in Links report, else `notfound_in`.
			if ( ! empty( $new_link_report['notfound'] ) || ! empty( $new_link_report['notfound_in'] ) ) {
				$queue_report['link_list_notfound'] = $new_link_report['link'];
			}
		}

		$queue_report['link_list_failed'] = ! empty( $new_link_report['status'] );

		$this->set( array( 'report' => $queue_report ) );
		$this->save();
	}

	public function get_report() {
		return $this->get( 'report' );
	}

	/**
	 * Gives the number of processed links.
	 *
	 * @return int
	 */
	public function links_processed() {
		$queue = $this->get_queue_data();

		return ! empty( $queue['links_processed'] ) ? intval( $queue['links_processed'] ) : 0;
	}

	public function clear() {
		return $this->save( $this->get_defaults(), true );
	}

	public function queue_is_empty() {
		return ( empty( $this->get_current_task() ) || empty( $this->get_current_task()['links'] ) ) && empty( $this->page_links() ) && empty( $this->full_site_links() );
	}

	public function get_current_task() {
		return $this->get( 'current_task' );
	}

	/**
	 * Gives page links.
	 *
	 * @return array
	 */
	public function page_links() {
		$queue = $this->get_queue_data();

		return ! empty( $queue['page_links'] ) ? $queue['page_links'] : array();
	}

	/**
	 * Gives full site links.
	 *
	 * @return array
	 */
	public function full_site_links() {
		$queue = $this->get_queue_data();

		return ! empty( $queue['full_site_links'] ) ? $queue['full_site_links'] : array();
	}

	/**
	 * Returns the current table that we will search for editing/unlinking a link for full site action.
	 *
	 * @return string|null
	 */
	public function get_current_target_table() {
		$current_task        = $this->get_current_task();
		$current_task_tables = $current_task['target_tables'] ?? null;

		if ( ! empty( $current_task_tables ) ) {
			foreach ( $current_task_tables as $table_name => $actioned ) {
				// Return the first table that is not actioned.
				if ( empty( $actioned ) ) {
					return $table_name;
				}
			}
		}

		// When all tables have been actioned, return null.
		return null;
	}

	/**
	 * Sets current target table to true, so that the next target table becomes available to process.
	 *
	 * @return bool
	 */
	public function move_to_next_target_table() {
		$current_task        = $this->get_current_task();
		$current_task_tables = $current_task['target_tables'] ?? null;
		$current_table       = $this->get_current_target_table();
		//Set current target table to true.
		$current_task_tables[ $current_table ] = true;
		$current_task['target_tables']         = $current_task_tables;
		$current_task['rows']                  = 0;
		$current_task['offset']                = 0;

		$this->set( array( 'current_task' => $current_task ) );

		return $this->save();
	}

	/**
	 * Checks if all current Link's target tables have been processed and returns true, else false.
	 *
	 * @return bool
	 */
	public function check_target_tables_complete() {
		$current_task        = $this->get_current_task();
		$current_task_tables = $current_task['target_tables'] ?? null;
		$tables_completed    = true;

		if ( empty( $current_task_tables ) ) {
			return true;
		}

		foreach ( $current_task_tables as $table_name => $status ) {
			if ( empty( $status ) ) {
				$tables_completed = false;
				break;
			}
		}

		return $tables_completed;
	}

	/**
	 * Checks if table has been processed.
	 *
	 * @param string|null $table
	 *
	 * @return bool
	 */
	public function has_table_been_prosseced( string $table = null ) {
		$current_task        = $this->get_current_task();
		$current_task_tables = $current_task['target_tables'] ?? null;

		if ( ! in_array( $table, $current_task_tables ) ) {
			return false;
		}

		return boolval( $current_task_tables[ $table ] );
	}

	/**
	 * Returns the stored number of rows that WHERE clause initially counted, before Links had been edited.
	 *
	 * @return int|null
	 */
	public function get_current_task_rows() {
		$current_task = $this->get_current_task();

		return  isset( $current_task['rows'] ) && ! empty( $current_task['rows'] ) ? intval( $current_task['rows'] ) : null;
	}

	/**
	 * Returns the timestamp that previous batch was completed.
	 *
	 * @return int
	 */
	public function batch_end_timestamp() {
		$queue = $this->get_queue_data();

		return ! empty( $queue['batch_end_timestamp'] ) ? intval( $queue['batch_end_timestamp'] ) : 0;
	}
}
