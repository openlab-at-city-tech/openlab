<?php
/**
 * Link object that contains all link editing (replace/unlink/nofollow) information and methods.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.1.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Broken_Links_Actions
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Broken_Links_Actions;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WP_Error;
use WP_Post;
use WP_User;
use WPMUDEV_BLC\App\Options\Links_Queue\Model as Queue;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\Core\Traits\Execution_Time;

/**
 * Class Scan_Data
 *
 * @package WPMUDEV_BLC\App\Broken_Links_Actions
 */
class Link extends Base {
	/**
	 * Use the Dashboard_API Trait.
	 *
	 * @since 2.1.0
	 */
	use Execution_Time;


	/**
	 * The Link type. Valid values `replace`, `unlink` or `nofollow`.
	 *
	 * @since 2.1.0
	 * @var string $type
	 *
	 */
	protected $type = null;

	/**
	 * The link to be actioned.
	 *
	 * @since 2.1.0
	 * @var string $link
	 */
	protected $link = null;

	/**
	 * The new link that will replace old one. When $this->type is `replace`.
	 *
	 * @since 2.1.0
	 * @var string $new_link_url
	 */
	protected $new_link = null;

	/**
	 * Action should be executed on whole site if true.
	 *
	 * @since 2.1.0
	 * @var boolean $full_site
	 */
	protected $full_site = false;

	/**
	 * The site urs where link needs to be actioned.
	 * Can contain string (for url) or int (for post id).
	 *
	 * @since 2.1.0
	 * @var array $origins
	 */
	protected $origins = array();

	/**
	 * True if link should be unlinked.
	 *
	 * @since 2.1.0
	 * @var array $unlink
	 */
	protected $unlink = true;

	/**
	 * True if link should be set to nofollow.
	 *
	 * @since 2.1.0
	 * @var array $nofollow
	 */
	protected $nofollow = true;

	/**
	 * The offset to start from when link is full site.
	 *
	 * @var int
	 */
	protected $offset = 0;

	/**
	 * The limit that sets how many post to search in for full site links on each batch.
	 *
	 * @var int
	 */
	protected $limit = 10;

	/**
	 * @return void
	 */
	public function __construct( array $props = array() ) {
		// From Execution_Time trait.
		$this->start_timer();

		if ( ! empty( $props ) ) {
			$default_props = array(
				'link'      => '',
				'new_link'  => '',
				'full_site' => false,
				'origins'   => array(),
				'unlink'    => false,
				'nofollow'  => false,
				'offset'    => 0,
			);

			$props = apply_filters(
				'wpmudev_blc_link_actions_link_props',
				wp_parse_args( $props, $default_props ),
				$props
			);

			if ( ! empty( $props ) ) {
				foreach ( $props as $property_name => $property_value ) {
					if ( property_exists( $this, $property_name ) ) {
						$this->__set( $property_name, $property_value );
					}
				}
			}

			$this->link = stripslashes( untrailingslashit( $this->link ) );
		}
	}

	/**
	 * Runs the actions that are requested by link data.
	 *
	 * @return true[]
	 */
	public function execute_action() {
		$report       = array();

		if ( ! empty( $this->is_full_site() ) ) {
			// If all tables have been completed for Link, no need to run `execute_full_site()`.
			// The offset will be reset in caller method.
			if ( Queue::instance()->check_target_tables_complete() ) {
				return array(
					'completed' => true,
					'link' => $this->link,
				);
			}
			$report = $this->execute_full_site();
		} else {
			$report = $this->execute_origins();
		}

		if ( ! is_wp_error( $report ) ) {
			$report['link']      = $this->link;
			$report['link_mode'] = $this->is_full_site() ? 'full_site_links' : 'page_links';
		}

		return $report;
	}

	public function get_type() {
		if ( empty( $this->type ) || ! in_array( $this->type, array( 'replace', 'unlink', 'nofollow' ) ) ) {
			if ( ! empty( $this->new_link ) ) {
				$this->type = 'replace';
			} else if ( ! empty( $this->unlink ) ) {
				$this->type = 'unlink';
			} else if ( ! empty( $this->nofollow ) ) {
				$this->type = 'nofollow';
			}
		}

		return $this->type;
	}

	public function is_full_site() {
		return ! empty( $this->full_site );
	}

	/**
	 * Executes Link action in full site.
	 *
	 * @return void
	 */
	public function execute_full_site() {

		$handler = new Handlers\Full_Site_Handler( $this );
		$response = $handler->execute();

		// Check if current table has been precessed.
		if ( ! empty( $response[ 'table_completed' ] ) ) {
			// Move to process next target table.
			Queue::instance()->move_to_next_target_table();
			$this->offset = 0;


			// check if all target tables have been processed.
			if ( Queue::instance()->check_target_tables_complete() ) {
				return array(
					'completed' => true,
					'link' => $this->link,
				);
			}

			if ( ! $this->runtime_passed_limit() ) {
				$this->execute_full_site();
			}
		}

		// At this point current table process is not complete yet.
		// If response holds the row number we can store that in db.
		if ( ! empty( $response['row_count'] ) ) {
			$current_task         = Queue::instance()->get_current_task();
			$current_task['rows'] = intval( $response['row_count'] );
			Queue::instance()->set( array( 'current_task' => $current_task ) );
			Queue::instance()->save();
		}

		return $response;
	}

	public function get_offset() {
		return intval( $this->offset );
	}

	public function get_limit() {
		return intval( $this->limit );
	}

	public function get_link() {
		return $this->link;
	}

	public function get_new_link() {
		return $this->new_link ?? null;
	}

	/**
	 * Executes action when Link uses origins.
	 *
	 * @return array|WP_Error
	 */
	public function execute_origins() {
		/*
		 * For origins links, we return :
		 *      status : bool, true if action executed successfully in all origins, else false.
		 *      notfound_in : array, list of origins the link was not found in.
		 */
		if ( empty( $this->origins() ) ) {
			return new WP_Error(
				'blc-link-execution-error',
				esc_html__(
					'Missing origins',
					'broken-link-checker'
				)
			);
		}

		$handler = new Handlers\Origins_Handler( $this );
		return $handler->execute();
	}

	/**
	 * Returns the Link origins.
	 *
	 * @return array
	 */
	public function origins() {
		return $this->origins;
	}

	public function types_map() {
		return apply_filters(
			'wpmudev_blc_link_types_map',
			array(
				'replace'  => 'edited',
				'unlink'   => 'unlinked',
				'nofollow' => 'nofollowed',
			),
			$this
		);
	}
}
