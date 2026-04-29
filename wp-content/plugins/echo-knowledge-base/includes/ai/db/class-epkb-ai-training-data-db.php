<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AI Training Data Repository DB
 * 
 * Manages training data sync state between WordPress content and AI vector stores.
 * Tracks sync status, errors, and metadata for posts, attachments, and other content.
 */
class EPKB_AI_Training_Data_DB extends EPKB_DB {
	
	/**
	 * Version History:
	 * 1.0 - Initial table structure
	 * 1.1 - Fixed index key length issue: Reduced item_id, store_id, file_id from VARCHAR(255) to VARCHAR(100)
	 * 1.3 - Added provider column to track which AI provider owns the training data row
	 */
	const TABLE_VERSION = '1.3';    /** update when table schema changes **/
	const PER_PAGE = 50;
	const PRIMARY_KEY = 'id';

	/**
	 * Get things started
	 */
	public function __construct( $db_check=false ) {
		parent::__construct();
		
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'epkb_ai_training_data';
		$this->primary_key = self::PRIMARY_KEY;

		// Ensure latest table exists
		if ( $db_check ) {
			$this->check_db();
		}
	}
	
	/**
	 * Get columns and formats
	 *
	 * @return array
	 */
	public function get_column_format() {
		return array(
			'collection_id' => '%d',
			'provider'             => '%s',
			'item_id'               => '%s',  // Can be post ID or UUID
			'store_id'              => '%s',
			'file_id'               => '%s',  //  file ID - used for all vector store operations
			'last_synced'           => '%s',
			'title'                 => '%s',
			'type'                  => '%s',
			'status'                => '%s',
			'error_code'            => '%d',
			'error_message'         => '%s',
			'retry_count'           => '%d',
			'path'                  => '%s',
			'url'                   => '%s',
			'content_hash'          => '%s',
			'user_id'               => '%d',
			'created'               => '%s',
			'updated'               => '%s'
		);
	}
	
	/**
	 * Get default column values
	 *
	 * @return array
	 */
	public function get_column_defaults() {
		return array(
			'id'                    => 0,
			'collection_id'=> 0,
			'provider'             => '',
			'item_id'               => '',
			'store_id'              => '',
			'file_id'               => '',
			'last_synced'           => null,
			'title'                 => '',
			'type'                  => 'post',
			'status'                => 'adding',
			'error_code'            => null,
			'error_message'         => null,
			'retry_count'           => 0,
			'path'                  => null,
			'url'                   => null,
			'content_hash'          => '',
			'user_id'               => get_current_user_id(),
			'created'               => gmdate( 'Y-m-d H:i:s' ),
			'updated'               => gmdate( 'Y-m-d H:i:s' )
		);
	}
	
	/**
	 * Get all unique collection IDs from the database
	 *
	 * @param string|null $provider Provider to filter by (optional)
	 * @return array|WP_Error Array of collection IDs or error
	 */
	public function get_all_collection_ids_from_db( $provider = null ) {
		global $wpdb;

		// If provider is provided, filter by it; otherwise return all providers
		if ( ! empty( $provider ) ) {
			$provider = EPKB_AI_Provider::normalize_provider( $provider );
			$sql = $wpdb->prepare( "SELECT DISTINCT collection_id, provider 
					FROM {$this->table_name} 
					WHERE collection_id IS NOT NULL 
					AND collection_id > 0
					AND provider = %s
					ORDER BY collection_id ASC", $provider );
		} else {
			$sql = "SELECT DISTINCT collection_id, provider 
					FROM {$this->table_name} 
					WHERE collection_id IS NOT NULL 
					AND collection_id > 0
					ORDER BY collection_id ASC";
		}

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $sql is a static query with no user input
		$results = $wpdb->get_results( $sql );
		$this->handle_db_error( $results, 'get_all_collection_ids_from_db' );
		if ( is_wp_error( $results ) ) {
			return $results;
		}

		$collection_ids = array();
		if ( is_array( $results ) ) {
			foreach ( $results as $row ) {
				$collection_ids[ intval( $row->collection_id ) ] = $row->provider;
			}
		}

		return $collection_ids;
	}

	/**
	 * Get the store_id for a specific collection and provider from the database
	 *
	 * @param int $collection_id Collection ID
	 * @param string|null $provider Provider to filter by (defaults to active provider)
	 * @return string|null Store ID or null if not found
	 */
	public function get_store_id_by_collection( $collection_id, $provider = null ) {
		global $wpdb;

		$provider = $provider ? EPKB_AI_Provider::normalize_provider( $provider ) : EPKB_AI_Provider::get_active_provider();

		$sql = $wpdb->prepare(
			"SELECT store_id FROM {$this->table_name} WHERE collection_id = %d AND provider = %s AND store_id IS NOT NULL AND store_id != '' LIMIT 1",
			$collection_id,
			$provider
		);

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $sql is prepared above
		return $wpdb->get_var( $sql );
	}

	/**
	 * Insert training data record
	 *
	 * @param array $data Training data
	 * @return int|WP_Error Insert ID or error
	 */
	public function insert_training_data( $data ) {

		if ( isset( $data['error_message'] ) ) {
			$data['error_message'] = EPKB_AI_Log::normalize_error_message( $data['error_message'] );
		}
		
		$data['created'] = gmdate( 'Y-m-d H:i:s' );
		$data['updated'] = gmdate( 'Y-m-d H:i:s' );
		
		// Set default user if not provided
		if ( empty( $data['user_id'] ) ) {
			$data['user_id'] = get_current_user_id();
		}
		
		$result = $this->insert_record( $data );
		$this->handle_db_error( $result, 'insert_training_data' );
		
		return $result;
	}
	
	/**
	 * Update training data record
	 *
	 * @param int $id Record ID
	 * @param array $data Data to update
	 * @return bool|WP_Error
	 */
	public function update_training_data( $id, $data ) {

		if ( isset( $data['error_message'] ) ) {
			$data['error_message'] = EPKB_AI_Log::normalize_error_message( $data['error_message'] );
		}
		
		$data['updated'] = gmdate( 'Y-m-d H:i:s' );
		
		$result = $this->update_record( $id, $data );
		$this->handle_db_error( $result, 'update_training_data' );
		
		return $result;
	}
	
	/**
	 * Get training data by ID
	 *
	 * @param int $id
	 * @return object|null|WP_Error - Training data record or null if not found or WP_Error on failure
	 */
	public function get_training_data_row_by_id( $id ) {

		$row = $this->get_by_primary_key( $id );
		$this->handle_db_error( $row, 'get_training_data' );
		
		return $row;
	}
	
	/**
	 * Get training data by item
	 *
	 * @param int $collection_id
	 * @param string $item_id
	 * @return object|null|WP_Error - Training data record or null if not found or WP_Error on failure
	 */
	public function get_training_data_record_by_item_id( $collection_id, $item_id ) {

		$item_id = (string) $item_id; // Ensure item_id is a string

		$row = $this->get_a_row_by_where_clause( array( 'collection_id' => $collection_id, 'item_id' => $item_id ) );
		$this->handle_db_error( $row, 'get_training_data_by_item' );
		if ( is_wp_error( $row ) ) {
			return $row;
		}
		if ( empty( $row ) ) {
			return null;
		}

		return $row;
	}
	
	/**
	 * Get training data by collection
	 *
	 * @param int $collection_id
	 * @param array $filters Optional filters
	 * @return array
	 */
	public function get_training_data_by_collection( $collection_id, $filters = array() ) {
		global $wpdb;
		
		$where = array();
		$where[] = $this->prepare_column_value( 'collection_id', $collection_id );
		
		if ( ! empty( $filters['status'] ) ) {
			$where[] = $this->get_status_where_clause( $filters['status'] );
		}
		
		if ( ! empty( $filters['type'] ) ) {
			$where[] = $this->prepare_column_value( 'type', $filters['type'] );
		}
		
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $where contains prepared values
		$sql = "SELECT * FROM {$this->table_name} WHERE " . implode( ' AND ', $where );
		$sql .= " ORDER BY created ASC";

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $sql contains prepared values from $where
		$rows = $wpdb->get_results( $sql );
		$this->handle_db_error( $rows, 'get_training_data_by_collection' );
		
		return $rows ?: array();
	}

	/**
	 * Get training data with pagination and filters
	 *
	 * @param array $args Query arguments
	 * @return array
	 */
	public function get_training_data_list( $args = array() ) {
		global $wpdb;
		
		$defaults = array(
			'page'                  => 1,
			'per_page'              => self::PER_PAGE,
			'collection_id'=> 0,
			'type'                  => '',
			'status'                => '',
			'search'                => '',
			'orderby'               => 'created',
			'order'                 => 'DESC'
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		// Build WHERE clauses
		$where = array();
		
		if ( ! empty( $args['collection_id'] ) ) {
			$where[] = $this->prepare_column_value( 'collection_id', $args['collection_id'] );
		}
		
		if ( ! empty( $args['type'] ) ) {
			$where[] = $this->prepare_column_value( 'type', $args['type'] );
		}
		
		if ( ! empty( $args['status'] ) ) {
			$where[] = $this->get_status_where_clause( $args['status'] );
		}
		
		// Add search condition if search term is provided
		if ( ! empty( $args['search'] ) ) {
			$search_term = '%' . $wpdb->esc_like( $args['search'] ) . '%';
			$search_conditions = array();
			$search_conditions[] = $wpdb->prepare( 'title LIKE %s', $search_term );
			$search_conditions[] = $wpdb->prepare( 'type LIKE %s', $search_term );
			$search_conditions[] = $wpdb->prepare( 'url LIKE %s', $search_term );
			$search_conditions[] = $wpdb->prepare( 'item_id LIKE %s', $search_term );
			$where[] = '(' . implode( ' OR ', $search_conditions ) . ')';
		}
		
		// Calculate offset
		$page = max( 1, absint( $args['page'] ) );
		$per_page = max( 1, min( 100, absint( $args['per_page'] ) ) );
		$offset = ( $page - 1 ) * $per_page;
		
		// Get rows with search support
		if ( ! empty( $args['search'] ) ) {
			// Build custom query when search is active
			$where_clause = ! empty( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '';
			$order_by = in_array( $args['orderby'], array( 'created', 'updated', 'title', 'type', 'status' ), true ) ? $args['orderby'] : 'created';
			$order_by = esc_sql( $order_by );
			$order = in_array( strtoupper( $args['order'] ), array( 'ASC', 'DESC' ), true ) ? strtoupper( $args['order'] ) : 'DESC';

			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $order_by validated against allowed list, $where_clause contains prepared values
			$sql = "SELECT * FROM {$this->table_name} {$where_clause} ORDER BY `{$order_by}` {$order} LIMIT %d OFFSET %d";
			$rows = $wpdb->get_results( $wpdb->prepare( $sql, $per_page, $offset ) );
		} else {
			// Use existing method when no search
			$rows = $this->get_rows_with_conditions( 
				$where, 
				$args['orderby'], 
				$args['order'], 
				$per_page, 
				$offset 
			);
		}
		
		$this->handle_db_error( $rows, 'get_training_data_list' );
		
		if ( is_wp_error( $rows ) ) {
			return array();
		}
		
		return $rows ?: array();
	}
	
	/**
	 * Get total count of training data
	 *
	 * @param array $args Query arguments
	 * @return int
	 */
	public function get_training_data_count( $args = array() ) {
		global $wpdb;
		
		$where = array();
		
		if ( ! empty( $args['collection_id'] ) ) {
			$where[] = $this->prepare_column_value( 'collection_id', $args['collection_id'] );
		}
		
		if ( ! empty( $args['type'] ) ) {
			$where[] = $this->prepare_column_value( 'type', $args['type'] );
		}
		
		if ( ! empty( $args['status'] ) ) {
			$where[] = $this->get_status_where_clause( $args['status'] );
		}
		
		// Add search condition if search term is provided
		if ( ! empty( $args['search'] ) ) {
			$search_term = '%' . $wpdb->esc_like( $args['search'] ) . '%';
			$search_conditions = array();
			$search_conditions[] = $wpdb->prepare( 'title LIKE %s', $search_term );
			$search_conditions[] = $wpdb->prepare( 'type LIKE %s', $search_term );
			$search_conditions[] = $wpdb->prepare( 'url LIKE %s', $search_term );
			$search_conditions[] = $wpdb->prepare( 'item_id LIKE %s', $search_term );
			$where[] = '(' . implode( ' OR ', $search_conditions ) . ')';
		}
		
		// Get count with search support
		if ( ! empty( $args['search'] ) ) {
			// Build custom query when search is active
			$where_clause = ! empty( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '';
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $where_clause contains prepared values
			$sql = "SELECT COUNT(*) FROM {$this->table_name} {$where_clause}";
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $sql contains prepared values
			$count = $wpdb->get_var( $sql );
		} else {
			// Use existing method when no search
			$count = $this->get_count_with_conditions( $where );
		}
		
		$this->handle_db_error( $count, 'get_training_data_count' );
		
		return intval( $count );
	}
	
	/**
	 * Mark item as successfully synced
	 *
	 * @param int $id
	 * @param array $sync_data Contains file_id, store_id, content_hash, etc.
	 * @return bool|WP_Error
	 */
	public function mark_as_synced( $id, $sync_data ) {

		$current_data = $this->get_training_data_row_by_id( $id );
		if ( ! $current_data ) {
			return new WP_Error( 'training_data_not_found', __( 'Training data not found', 'echo-knowledge-base' ) . 'ID: ' . $id );
		}

		$new_status = $current_data->status === 'adding' ? 'added' : 'updated';
		$data = array(
			'status'        => $new_status,
			'last_synced'   => gmdate( 'Y-m-d H:i:s' ),
			'error_code'    => null,
			'error_message' => null,
			'retry_count'   => 0
		);

		// Update sync IDs if provided
		if ( isset( $sync_data['file_id'] ) ) {
			$data['file_id'] = $sync_data['file_id'];
		}
		if ( isset( $sync_data['store_id'] ) ) {
			$data['store_id'] = $sync_data['store_id'];
		}
		if ( isset( $sync_data['content_hash'] ) ) {
			$data['content_hash'] = $sync_data['content_hash'];
		}
		if ( isset( $sync_data['title'] ) ) {
			$data['title'] = EPKB_AI_Validation::validate_title( $sync_data['title'] );
		}
		if ( isset( $sync_data['url'] ) ) {
			$data['url'] = $sync_data['url'];
		}

		$result = $this->update_training_data( $id, $data );

		return is_wp_error( $result ) ? $result : $new_status;
	}
	
	/**
	 * Mark item as having sync error
	 *
	 * @param int $id
	 * @param int $error_code HTTP error code
	 * @param string $error_message Error message (will be truncated to 200 chars)
	 * @return bool|WP_Error
	 */
	public function mark_as_error( $id, $error_code, $error_message ) {

		$current_data = $this->get_training_data_row_by_id( $id );
		if ( ! $current_data ) {
			return new WP_Error( 'training_data_not_found', __( 'Training data not found', 'echo-knowledge-base' ) . 'ID: ' . $id );
		}

		$data = array(
			'status'        => 'error',
			'error_code'    => $error_code,
			'error_message' => $error_message,
			'retry_count'   => $current_data->retry_count + 1
		);
		
		return $this->update_training_data( $id, $data );
	}

	/**
	 * Mark item as skipped (empty content, deleted source, etc.)
	 * No retry_count increment — retrying skipped items is pointless.
	 *
	 * @param int $id
	 * @param int $error_code HTTP error code
	 * @param string $error_message Error message
	 * @return bool|WP_Error
	 */
	public function mark_as_skipped( $id, $error_code, $error_message ) {
		$data = array(
			'status'        => 'skipped',
			'error_code'    => $error_code,
			'error_message' => $error_message,
		);
		return $this->update_training_data( $id, $data );
	}

	/**
	 * Migrate existing error records with empty content/deleted source to skipped status
	 *
	 * @return int|false Number of rows updated or false on failure
	 */
	public function migrate_error_to_skipped() {
		global $wpdb;

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name is safe
		return $wpdb->query(
			"UPDATE {$this->table_name} SET status = 'skipped', updated = '" . gmdate( 'Y-m-d H:i:s' ) . "'
			 WHERE status = 'error' AND error_message IN ('empty_content', 'empty_markdown', 'post_not_found')"
		);
	}

	/**
	 * Mark items as outdated by source
	 *
	 * @param string $type Source type (post, page, etc.)
	 * @param string $item_id Item ID
	 * @return bool|WP_Error
	 */
	public function mark_source_as_outdated( $type, $item_id ) {
		global $wpdb;
		
		$result = $wpdb->update(
			$this->table_name,
			array( 
				'status' => 'outdated',
				'updated' => gmdate( 'Y-m-d H:i:s' )
			),
			array(
				'type' => $type,
				'item_id' => $item_id
			),
			array( '%s', '%s' ),
			array( '%s', '%s' )
		);
		
		$this->handle_db_error( $result, 'mark_source_as_outdated' );

		if ( $result === false ) {
			return new WP_Error( 'db_error', $wpdb->last_error );
		}

		return true;
	}
	
	/**
	 * Delete training data by source
	 *
	 * @param string $type Source type
	 * @param string $item_id Item ID
	 * @return bool|WP_Error
	 */
	public function delete_training_data_by_source( $type, $item_id ) {
		global $wpdb;
		
		$result = $wpdb->delete(
			$this->table_name,
			array(
				'type' => $type,
				'item_id' => $item_id
			),
			array( '%s', '%s' )
		);
		
		$this->handle_db_error( $result, 'delete_by_source' );

		if ( $result === false ) {
			return new WP_Error( 'db_error', $wpdb->last_error );
		}

		return true;
	}
	
	/**
	 * Delete training data record by ID
	 *
	 * @param int $id
	 * @return bool|WP_Error
	 */
	public function delete_training_data_record( $id ) {

		$result = $this->delete_record( $id );
		$this->handle_db_error( $result, 'delete_training_data' );
		
		return $result;
	}
	
	/**
	 * Get statistics by status
	 *
	 * @param int $collection_id Optional collection ID filter
	 * @return array
	 */
	public function get_status_statistics( $collection_id = 0 ) {
		global $wpdb;
		
		$where = '';
		if ( $collection_id > 0 ) {
			$where = $wpdb->prepare( ' WHERE collection_id = %d', $collection_id );
		}
		
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $where is prepared above
		$sql = "SELECT status, COUNT(*) as count FROM {$this->table_name} {$where} GROUP BY status";

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $sql contains prepared values
		$results = $wpdb->get_results( $sql );
		$this->handle_db_error( $results, 'get_status_statistics' );
		
		$stats = array(
			'adding'    => 0,
			'added'     => 0,
			'updating'  => 0,
			'updated'   => 0,
			'outdated'  => 0,
			'error'     => 0,
			'skipped'   => 0,
			'pending'   => 0
		);
		
		if ( ! empty( $results ) ) {
			foreach ( $results as $row ) {
				// Handle null/empty status as pending
				if ( empty( $row->status ) || $row->status === 'pending' ) {
					$stats['pending'] += (int) $row->count;
				} else {
					$stats[ $row->status ] = (int) $row->count;
				}
			}
		}
		
		// Calculate totals
		$stats['total'] = array_sum( $stats );
		$stats['synced'] = $stats['added'] + $stats['updated'];
		// Include 'pending' status items in the pending count
		$stats['pending'] = $stats['pending'] + $stats['adding'] + $stats['updating'] + $stats['outdated'];
		
		return $stats;
	}
	
	/**
	 * Get the last sync date for a collection
	 *
	 * @param int $collection_id Collection ID
	 * @return string|null Last sync date or null if no syncs
	 */
	public function get_last_sync_date( $collection_id = 0 ) {
		global $wpdb;
		
		$where = '';
		if ( $collection_id > 0 ) {
			$where = $wpdb->prepare( ' WHERE collection_id = %d AND last_synced IS NOT NULL', $collection_id );
		} else {
			$where = ' WHERE last_synced IS NOT NULL';
		}
		
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $where is prepared above
		$sql = "SELECT MAX(last_synced) as last_sync_date FROM {$this->table_name} {$where}";

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $sql contains prepared values
		$result = $wpdb->get_var( $sql );
		$this->handle_db_error( $result, 'get_last_sync_date' );
		
		return $result;
	}
	
	/**
	 * Get rows with WHERE conditions
	 *
	 * @param array $where WHERE clauses
	 * @param string $orderby
	 * @param string $order
	 * @param int $limit
	 * @param int $offset
	 * @return array|WP_Error
	 */
	private function get_rows_with_conditions( $where, $orderby, $order, $limit, $offset ) {
		global $wpdb;
		
		$sql = "SELECT * FROM $this->table_name";
		
		if ( ! empty( $where ) ) {
			$sql .= " WHERE " . implode( ' AND ', $where );
		}
		
		// Validate orderby against allowed columns
		$allowed_columns = array( 'id', 'created', 'updated', 'type', 'status', 'last_synced', 'title' );
		if ( ! in_array( $orderby, $allowed_columns, true ) ) {
			$orderby = 'created';
		}
		
		// Validate order direction
		$order = strtoupper( $order );
		if ( ! in_array( $order, array( 'ASC', 'DESC' ), true ) ) {
			$order = 'DESC';
		}
		
		$orderby = '`' . $orderby . '`';
		$limit = absint( $limit );
		$offset = absint( $offset );

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $orderby validated against allowed list above
		$sql .= " ORDER BY $orderby $order";
		$sql .= $wpdb->prepare( " LIMIT %d OFFSET %d", $limit, $offset );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $sql contains prepared values
		$results = $wpdb->get_results( $sql );
		$this->handle_db_error( $results, 'mark_source_as_outdated' );

		if ( $results === null && ! empty( $wpdb->last_error ) ) {
			return new WP_Error( 'db_error', $wpdb->last_error );
		}

		return $results ?: array();
	}
	
	/**
	 * Get count with WHERE conditions
	 *
	 * @param array $where WHERE clauses
	 * @return int
	 */
	private function get_count_with_conditions( $where ) {
		global $wpdb;
		
		$sql = "SELECT COUNT(*) FROM $this->table_name";
		
		if ( ! empty( $where ) ) {
			$sql .= " WHERE " . implode( ' AND ', $where );
		}

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $where contains prepared values from calling methods
		$count = $wpdb->get_var( $sql );
		$this->handle_db_error( $count, 'mark_source_as_outdated' );

		if ( $count === null && ! empty( $wpdb->last_error ) ) {
			return 0;
		}
		
		return absint( $count );
	}
	
	/**
	 * Get existing post IDs for a collection
	 *
	 * @param int $collection_id
	 * @return array|WP_Error Array of post IDs or error
	 */
	public function get_existing_post_ids( $collection_id ) {
		global $wpdb;
		
		$collection_id = absint( $collection_id );
		
		$sql = $wpdb->prepare( "SELECT item_id FROM {$this->table_name} WHERE collection_id = %d", $collection_id );
		
		$post_ids = $wpdb->get_col( $sql );
		$this->handle_db_error( $post_ids, 'get_existing_post_ids' );
		if ( $post_ids === null && ! empty( $wpdb->last_error ) ) {
			return new WP_Error( 'db_error', 'Failed to get existing post IDs' );
		}
		
		// Convert to integers for consistency
		return array_map( 'intval', $post_ids );
	}
	
	/**
	 * Check if there is any synced data
	 *
	 * @param array $collection_ids Optional collection IDs to scope the count
	 * @return int
	 */
	public static function count_synced_data( $collection_ids = array() ) {
		global $wpdb;

		if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $wpdb->prefix . 'epkb_ai_training_data' ) ) !== $wpdb->prefix . 'epkb_ai_training_data' ) {
			return 0;
		}

		$collection_ids = array_filter( array_map( 'absint', (array) $collection_ids ) );

		if ( empty( $collection_ids ) ) {
			$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}epkb_ai_training_data WHERE status IN ('added', 'updated')";
			return intval( $wpdb->get_var( $sql ) );
		}

		$placeholders = implode( ', ', array_fill( 0, count( $collection_ids ), '%d' ) );
		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}epkb_ai_training_data WHERE status IN ('added', 'updated') AND collection_id IN ( {$placeholders} )";

		return intval( $wpdb->get_var( $wpdb->prepare( $sql, $collection_ids ) ) );
	}

	/**
	 * Get all unique types for a collection, optionally filtered by status
	 *
	 * @param int $collection_id Collection ID
	 * @param string $status Optional status filter
	 * @return array Array of unique types with labels
	 */
	public function get_collection_types( $collection_id, $status = '' ) {
		global $wpdb;
		
		$collection_id = absint( $collection_id );
		if ( empty( $collection_id ) ) {
			return array();
		}
		
		// Build query
		$sql = "SELECT DISTINCT type FROM {$this->table_name} WHERE collection_id = %d";
		$params = array( $collection_id );
		
		// Add status filter if provided
		if ( ! empty( $status ) && $status !== 'all' ) {
			if ( $status === 'pending' ) {
				$sql .= " AND status IN ('pending', 'adding', 'updating', 'outdated')";
			} else {
				$sql .= " AND status = %s";
				$params[] = $status;
			}
		}
		
		$sql .= " ORDER BY type ASC";
		
		// Get unique types
		$types = $wpdb->get_col( $wpdb->prepare( $sql, $params ) );
		
		if ( empty( $types ) ) {
			return array();
		}
		
		// Format types with labels
		$formatted_types = array();
		foreach ( $types as $type ) {
			$post_type_obj = get_post_type_object( $type );
			if ( $post_type_obj && isset( $post_type_obj->labels->name ) ) {
				if ( strpos( $type, 'epkb_post_type' ) === 0 && isset( $post_type_obj->labels->name ) ) {
					$type_name = $post_type_obj->labels->name;
				} else {
					$type_name = $post_type_obj->labels->singular_name;
				}
			} else {
				$type_name = ucfirst( $type );
			}
			
			// Limit to 20 characters with ellipsis if longer
			if ( strlen( $type_name ) > 20 ) {
				$type_name = substr( $type_name, 0, 18 ) . '..';
			}
			
			$formatted_types[] = array(
				'value' => $type,
				'label' => $type_name
			);
		}
		
		return $formatted_types;
	}

	/**
	 * Get the WHERE clause for a status filter.
	 *
	 * The Pending tab is an aggregate view of all items that still need syncing.
	 *
	 * @param string|array $status Status filter.
	 * @return string
	 */
	private function get_status_where_clause( $status ) {

		if ( is_array( $status ) ) {
			return $this->prepare_column_value( 'status', array_values( array_unique( array_filter( $status ) ) ) );
		}

		if ( $status === 'pending' ) {
			return "status IN ('pending', 'adding', 'updating', 'outdated')";
		}

		return $this->prepare_column_value( 'status', $status );
	}

	/**
	 * Get training data by item_id only (without requiring collection_id)
	 * Used for source reference lookups where collection_id is unknown (e.g., PDFs referenced by UUID)
	 *
	 * @param string $item_id Item ID (e.g., UUID for PDFs)
	 * @return object|null Training data record or null if not found
	 */
	public function get_training_data_by_item_id_only( $item_id ) {
		global $wpdb;

		$item_id = sanitize_text_field( (string) $item_id );

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name is safe
		$sql = $wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE item_id = %s AND status IN ('added', 'updated') LIMIT 1", $item_id );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $sql is prepared above
		$row = $wpdb->get_row( $sql );
		$this->handle_db_error( $row, 'get_training_data_by_item_id_only' );

		return $row ?: null;
	}

	/**
	 * Get training data by file_id only (without requiring collection_id)
	 * Used for provider citation lookups where only the AI file_id is available.
	 *
	 * @param string $file_id AI file ID (e.g., file-xxx)
	 * @param string $provider Optional provider slug to avoid cross-provider collisions
	 * @return object|null Training data record or null if not found
	 */
	public function get_training_data_by_file_id_only( $file_id, $provider = '' ) {
		global $wpdb;

		$file_id = sanitize_text_field( (string) $file_id );
		$provider = sanitize_text_field( (string) $provider );

		if ( empty( $file_id ) ) {
			return null;
		}

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name is safe
		$sql = "SELECT * FROM {$this->table_name} WHERE file_id = %s AND status IN ('added', 'updated')";
		$params = array( $file_id );

		if ( ! empty( $provider ) ) {
			$sql .= ' AND provider = %s';
			$params[] = $provider;
		}

		$sql .= ' LIMIT 1';

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $sql is prepared below
		$prepared_sql = $wpdb->prepare( $sql, $params );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $prepared_sql is prepared above
		$row = $wpdb->get_row( $prepared_sql );
		$this->handle_db_error( $row, 'get_training_data_by_file_id_only' );

		return $row ?: null;
	}

	/**
	 * Get the table version
	 *
	 * @return string
	 */
	protected function get_table_version() {
		return self::TABLE_VERSION;
	}

	/**
	 * Create the table
	 * 
	 * Table stores sync state for AI training data items.
	 * 
	 * Table columns:
	 * - id: Primary key
	 * - collection_id: Collection of posts and other documents stored in a single unique store
	 * - provider: AI provider that owns the vector store/file records
	 * - item_id: Post ID, attachment ID, or generated UUID for uploads/AI-generated content
	 * - store_id: ID of the Vector Store or other storage
	 * - file_id: file ID (file-xxx) - used for all AI file and vector store operations
	 * - last_synced: Timestamp of last successful sync
	 * - title: Post/page/attachment/note title; file name
	 * - type: Post, page, CPT, attachment, file, note, PDF, CSV, XML, AI-generated, URL
	 * - status: 'error', 'skipped', 'adding' → 'added', 'updating' → 'updated', 'outdated', 'pending'
	 * - error_code: HTTP error code (e.g. 429, 503)
	 * - error_message: Error description
	 * - retry_count: Number of sync retry attempts
	 * - path: Server filesystem path (if file/note, etc.), including file name
	 * - url: Publicly accessible URL
	 * - content_hash: MD5 hash of content for change detection
	 * - user_id: WordPress user ID who created/synced this record
	 * - created: Record creation timestamp
	 * - updated: Last update timestamp
	 * 
	 * IMPORTANT: When modifying this table structure, you MUST update TABLE_VERSION constant at the top of this class!
	 */
	protected function create_table() {
		global $wpdb;

		$collate = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '';

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql = "CREATE TABLE {$this->table_name} (
				    id                      BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				    collection_id  			INT UNSIGNED        NOT NULL DEFAULT 1,
				    provider                VARCHAR(20)         NOT NULL DEFAULT '',
				    item_id                 VARCHAR(100)        NOT NULL,
				    store_id                VARCHAR(100)        NULL,
				    file_id                 VARCHAR(100)        NULL,
				    last_synced             DATETIME            NULL,
				    title                   VARCHAR(255)        NOT NULL,
				    type                    VARCHAR(50)         NOT NULL,
				    status                  VARCHAR(20)         NOT NULL DEFAULT 'adding',
				    error_code              INT                 NULL,
				    error_message           VARCHAR(200)        NULL,
				    retry_count             INT                 NOT NULL DEFAULT 0,
				    path                    TEXT                NULL,
				    url                     TEXT                NULL,
				    content_hash            VARCHAR(32)         NULL,
				    user_id                 BIGINT(20) UNSIGNED NULL,
				    created                 DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
				    updated                 DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				    PRIMARY KEY (id),
				    KEY         idx_collection_item         (collection_id, item_id),
				    KEY         idx_provider_collection     (provider, collection_id),
				    KEY         idx_status                  (status),
				    KEY         idx_type                    (type),
				    KEY         idx_file_id                 (file_id),
				    KEY         idx_sync_priority           (status, retry_count, created)
			) $collate;";

		dbDelta( $sql );

		// Only store version if table was actually created successfully
		if ( $this->table_exists( $this->table_name ) ) {
			update_option( $this->get_version_option_name(), self::TABLE_VERSION, true );
		}
	}
}
