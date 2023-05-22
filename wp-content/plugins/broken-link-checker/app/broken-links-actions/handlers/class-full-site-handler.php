<?php
/**
 * Handles Full site process of editing/unlinking Link
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.1.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Broken_Links_Actions\Handlers
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Broken_Links_Actions\Handlers;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WP_Error;
use WP_Post;
use WP_User;
use WPMUDEV_BLC\App\Options\Links_Queue\Model as Queue;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\Core\Traits\Execution_Time;
use WPMUDEV_BLC\App\Broken_Links_Actions\Link;
use WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Main as Processor;

/**
 * Class Full_Site_Handler
 *
 * @package WPMUDEV_BLC\App\Broken_Links_Actions\Handlers
 */
class Full_Site_Handler extends Base {
	/**
	 * Use the Dashboard_API Trait.
	 *
	 * @since 2.1
	 */
	use Execution_Time;

	/**
	 * The Link object
	 *
	 * @var object|null
	 */
	protected $link_object = null;

	/**
	 * Queue object.
	 *
	 * @var object|null
	 */
	protected $queue = null;

	/**
	 * Offset is used to check if process for current table is complete.
	 *
	 * @var int
	 */
	protected $offset = 0;

	/**
	 * Select limit.
	 *
	 * @var int
	 */
	protected $limit = 0;

	/**
	 * The current target table [posts|postmeta|authors|comments].
	 * @var string|null
	 */
	protected $current_table = null;

	/**
	 *The $wpdb object.
	 *
	 * @var object|null
	 */
	protected $db = null;

	/**
	 * The query parts that consist of table|select_columns|where|values
	 *
	 * @var array
	 */
	protected $query_parts = array();

	protected $link_instances_count = 0;

	public function __construct( Link $link = null ) {
		global $wpdb;

		$this->db                   = $wpdb;
		$this->link_object          = $link;
		$this->offset               = $this->link_object->get_offset();
		$this->limit                = $this->link_object->get_limit();
		$this->queue                = Queue::instance();
		$this->current_table        = $this->queue->get_current_target_table();
		$this->link_instances_count = $this->get_link_instances_count();
	}

	/**
	 * Gives the number of columns the link was found in current table.
	 *
	 * @return int|null
	 */
	public function get_link_instances_count() {
		$count = $this->queue->get_current_task_rows();

		if ( empty( $count ) ) {
			$count_result = $this->db_select( true );
			$count        = is_array( $count_result ) && ! empty( $count_result[0]->count ) ? $count_result[0]->count : 0;
		}

		return intval( $count );
	}

	public function db_select( bool $count = false ) {
		$query_parts    = $this->get_query_parts();
		$table          = $query_parts['table'] ?? null;
		$select_columns = $count ? 'COUNT(*) as count ' : ( $query_parts['select_columns'] ?? null );
		$where          = $query_parts['where'] ?? null;
		$where_values   = $query_parts['values'] ?? null;
		$orderby        = $count ? '' : "ORDER BY id ASC";
		$limit          = '';

		// If Link is of type `replace` (or `edit`, sorry for using 2 terms for the same thing here and there),
		// we should not use OFFSET as previous instances have been replaced already.
		if ( 'replace' === $this->link_object->get_type() ) {
			$limit = $count ? '' : "LIMIT {$this->limit}";
		} else {
			$limit = $count ? '' : "LIMIT {$this->limit} OFFSET {$this->offset}";
		}

		if ( empty( $table ) || empty( $select_columns ) || empty( $where ) ) {
			return 0;
		}

		return $this->db->get_results(
			$this->db->prepare(
				"SELECT {$select_columns} FROM {$table} WHERE {$where} {$orderby} {$limit};",
				$where_values
			)
		);
	}

	/**
	 * Calculates and returns the query parts.
	 *
	 * @return array
	 */
	public function get_query_parts() {
		if ( empty( $this->query_parts ) ) {
			$link                 = $this->link_object->link;
			$escaped_link         = str_replace( [ '"', "'" ], "", json_encode( $link ) );
			$where_query          = '';
			$table                = '';
			$where_struct         = array();
			$where_q_format_parts = array();
			$where_q_values       = array();

			$where_struct[] = array(
				'column'     => 'post_content',
				'value'      => '%' . $this->db->esc_like( $link ) . '%',
				'value_type' => 'string',
				'compare'    => 'LIKE',
			);

			// We expect this to be always true since crawler will sniff un-escaped urls from front end.
			// However, rules obsession dictate to have a condition.
			if ( $escaped_link !== $link ) {
				$where_struct[] = array(
					'column'     => 'post_content',
					//'value'      => '%' . $wpdb->esc_like( $escaped_link ) . '%',
					'value'      => '%' . $escaped_link . '%',
					'value_type' => 'string',
					'compare'    => 'LIKE'
				);
			}

			switch ( $this->current_table ) {
				case 'posts' :
					$table          = $this->db->posts;
					$select_columns = 'ID as id, post_content as content';
					break;
				case 'postmeta' :
					$table          = $this->db->postmeta;
					$select_columns = 'meta_id as id, meta_value as content';

					foreach ( $where_struct as $key => $where_struct_item ) {
						$where_struct[ $key ]['column'] = 'meta_value';
					}
					break;
				case 'comments' :
					$table          = $this->db->comments;
					$select_columns = 'comment_ID as id, comment_content as content';

					foreach ( $where_struct as $key => $where_struct_item ) {
						$where_struct[ $key ]['column'] = 'comment_content';
					}
					break;
				case 'authors' :
					$table          = $this->db->usermeta;
					$select_columns = 'umeta_id as id, meta_value as content';

					foreach ( $where_struct as $key => $where_struct_item ) {
						$where_struct[ $key ]['column'] = 'meta_value';
					}
					break;
			}

			if ( ! empty( $where_struct ) ) {
				foreach ( $where_struct as $key => $values_sctruct ) {
					$column  = $values_sctruct['column'] ?? '';
					$value   = $values_sctruct['value'] ?? '';
					$compare = $values_sctruct['compare'] ?? '';

					// Let's not get over-dramatic and set $type_specifier to string since we're looking for urls.
					$type_specifier = '%s';

					$where_q_format_parts[] = "{$column} {$compare} \"{$type_specifier}\"";
					$where_q_values[]       = $value;
				}

				$where_query = implode( ' OR ', $where_q_format_parts );
			}

			if ( 'posts' === $this->current_table ) {
				$posts_where = "post_type <> 'revision' AND post_status='publish' ";

				if ( ! empty( $where_query ) ) {
					$where_query = "{$posts_where} AND ({$where_query})";
				} else {
					$where_query = $posts_where;
				}
			}

			$this->query_parts = apply_filters(
				'wpmudev_blc_full_site_handler_query_parts',
				array(
					'table'          => $table,
					'select_columns' => $select_columns,
					'where'          => $where_query,
					'values'         => $where_q_values,
				)
			);
		}

		return $this->query_parts;
	}

	/**
	 * General callable method that starts the process.
	 *
	 * @return array|void
	 */
	public function execute() {
		if ( empty( $this->current_table ) ) {
			return table_status_flag(
				array(
					'has_error'     => true,
					'error_message' => esc_html__(
						'Missing Full Site Target Table',
						'broken-link-checker'
					)
				)
			);
		}

		// Check if table completed. `$this->link_instances_count` is the initial instance count.
		if ( $this->link_instances_count <= 0 || $this->offset >= $this->link_instances_count ) {
			return $this->table_completed_flag();
		}

		$result = $this->proceed_execution();

		return $result;
	}

	/**
	 * Returns current table status with `table_completed` flag being set to true.
	 *
	 * @return array
	 */
	private function table_completed_flag() {
		return $this->table_status_flag(
			array(
				'table_completed' => true,
				'offset'          => 0,
			)
		);
	}

	/**
	 * Provides the default response flags.
	 *
	 * @param array $args
	 *
	 * @return mixed
	 */
	private function table_status_flag( array $args = array() ) {
		$default = array(
			'link'            => $this->link_object->get_link(),
			'link_mode'       => $this->link_object->is_full_site() ? 'full_site_links' : 'page_links',
			'table_name'      => $this->current_table,
			'table_completed' => false,
			'has_error'       => false,
			'error_message'   => '',
			'offset'          => $this->offset,
			'row_count'       => $this->link_instances_count,
		);

		return wp_parse_args( $args, $default );
	}

	/**
	 * Starts the actual process of Link's tables.
	 * 1. Gets the list of instances (table row ids where the link url is found)
	 * 2. Processes the link url in instances found by editing or unlinking them (ot nofollowing)
	 *
	 * @return array|mixed
	 */
	public function proceed_execution() {
		$instances = $this->db_select();
		$count     = 0;

		if ( empty( $instances ) ) {
			return $this->table_completed_flag();
		}

		foreach ( $instances as $instance ) {
			// At this point let's check the  max execution time.
			if ( $this->runtime_passed_limit() ) {
				break;
			}

			$this->process_instance( $instance );
			$count ++;
		}

		$this->offset += $count;

		if ( $this->offset >= $this->link_instances_count ) {
			return $this->table_completed_flag();
		}

		return $this->table_status_flag();
	}

	/**
	 * Process the Link.
	 *
	 * @param object|null $instance
	 *
	 * @return bool
	 */
	public function process_instance( object $instance = null ) {
		if ( empty( $instance ) || ! property_exists( $instance, 'id' ) || ! property_exists( $instance, 'content' ) ) {
			return false;
		}

		$processor = new Processor( $this->link_object );

		switch ( $this->current_table ) {
			case 'posts' :
				$processor->execute_in_post_content( $instance->id, $instance->content );
				break;
			case 'postmeta' :
				$processor->execute_in_postmeta( $instance->id, $instance->content );
				break;
			case 'comments' :
				$processor->execute_in_comment( $instance->id, $instance->content );
				break;
			case 'authors' :
				$processor->execute_in_usermeta( $instance->id, $instance->content );
				break;
		}

		return true;
	}

}