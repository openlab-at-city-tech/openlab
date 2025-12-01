<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AI Training Data Repository DB
 * 
 * Manages training data sync state between WordPress content and OpenAI vector stores.
 * Tracks sync status, errors, and metadata for posts, attachments, and other content.
 */
class EPKB_AI_Training_Data_DB extends EPKB_DB {
	
	/**
	 * Version History:
	 * 1.0 - Initial table structure
	 * 1.1 - Fixed index key length issue: Reduced item_id, store_id, file_id from VARCHAR(255) to VARCHAR(100)
	 */
	const TABLE_VERSION = '1.2';    /** update when table schema changes **/
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
			'item_id'               => '%s',  // Can be post ID or UUID
			'store_id'              => '%s',
			'file_id'               => '%s',  // OpenAI file ID (file-xxx) - used for all vector store operations
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
	 * @return array|WP_Error Array of collection IDs or error
	 */
	public function get_all_collection_ids_from_db() {
		global $wpdb;
		
		$sql = "SELECT DISTINCT collection_id 
				FROM {$this->table_name} 
				WHERE collection_id IS NOT NULL 
				AND collection_id > 0 
				ORDER BY collection_id ASC";

		$collection_ids = $wpdb->get_col( $sql );
		$this->handle_db_error( $collection_ids, 'insert_training_data' );
		
		// Convert to integers
		return array_map( 'intval', $collection_ids );
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
		$where[] = $wpdb->prepare( "collection_id = %d", $collection_id );
		
		if ( ! empty( $filters['status'] ) ) {
			$where[] = $wpdb->prepare( "status = %s", $filters['status'] );
		}
		
		if ( ! empty( $filters['type'] ) ) {
			$where[] = $wpdb->prepare( "type = %s", $filters['type'] );
		}
		
		$sql = "SELECT * FROM {$this->table_name} WHERE " . implode( ' AND ', $where );
		$sql .= " ORDER BY created ASC";
		
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
			$where[] = $this->prepare_column_value( 'status', $args['status'] );
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
			$order_by = in_array( $args['orderby'], array( 'created', 'updated', 'title', 'type', 'status' ) ) ? $args['orderby'] : 'created';
			$order = in_array( strtoupper( $args['order'] ), array( 'ASC', 'DESC' ) ) ? strtoupper( $args['order'] ) : 'DESC';
			
			$sql = "SELECT * FROM {$this->table_name} {$where_clause} ORDER BY {$order_by} {$order} LIMIT %d OFFSET %d";
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
			$where[] = $this->prepare_column_value( 'status', $args['status'] );
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
			$sql = "SELECT COUNT(*) FROM {$this->table_name} {$where_clause}";
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

		// Determine new status based on current status
		$new_status = 'added';
		if ( in_array( $current_data->status, array( 'updating', 'updated', 'outdated' ) ) ) {
			$new_status = 'updated';
		}
		
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
			$data['title'] = $sync_data['title'];
		}
		if ( isset( $sync_data['url'] ) ) {
			$data['url'] = $sync_data['url'];
		}
		
		return $this->update_training_data( $id, $data );
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
		
		$sql = "SELECT status, COUNT(*) as count FROM {$this->table_name} {$where} GROUP BY status";

		$results = $wpdb->get_results( $sql );
		$this->handle_db_error( $results, 'get_status_statistics' );
		
		$stats = array(
			'adding'    => 0,
			'added'     => 0,
			'updating'  => 0,
			'updated'   => 0,
			'outdated'  => 0,
			'error'     => 0,
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
		
		$sql = "SELECT MAX(last_synced) as last_sync_date FROM {$this->table_name} {$where}";
		
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
		
		$sql .= " ORDER BY $orderby $order";
		$sql .= $wpdb->prepare( " LIMIT %d OFFSET %d", $limit, $offset );
		
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
	 * @return int
	 */
	public static function count_synced_data() {
		global $wpdb;

		if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $wpdb->prefix . 'epkb_ai_training_data' ) ) !== $wpdb->prefix . 'epkb_ai_training_data' ) {
			return 0;
		}

		return intval( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}epkb_ai_training_data WHERE status = 'updated'" ) );
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
			$sql .= " AND status = %s";
			$params[] = $status;
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
	 * - item_id: Post ID, attachment ID, or generated UUID for uploads/AI-generated content
	 * - store_id: ID of the Vector Store or other storage
	 * - file_id: OpenAI file ID (file-xxx) - used for all OpenAI file and vector store operations
	 * - last_synced: Timestamp of last successful sync
	 * - title: Post/page/attachment/note title; file name
	 * - type: Post, page, CPT, attachment, file, note, PDF, CSV, XML, AI-generated, URL
	 * - status: 'error', 'adding' → 'added', 'updating' → 'updated', 'outdated', 'pending'
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