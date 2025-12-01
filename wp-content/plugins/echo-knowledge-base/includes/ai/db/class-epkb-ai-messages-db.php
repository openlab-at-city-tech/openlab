<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Message Repository DB
 * 
 * Handles all database operations for AI messages.
 */
class EPKB_AI_Messages_DB extends EPKB_DB {
	
	/**
	 * Version History:
	 * 1.0 - Initial table structure
	 */
	const TABLE_VERSION = '1.0';    /** update when table schema changes **/
	const PER_PAGE = 20;
	const PRIMARY_KEY = 'id';
	const TABLE_NAME_SUFFIX = 'epkb_ai_messages';

	/**
	 * Get things started
	 */
	public function __construct() {
		parent::__construct();
		
		global $wpdb;
		$this->table_name = $wpdb->prefix . self::TABLE_NAME_SUFFIX;
		$this->primary_key = self::PRIMARY_KEY;
		
		// Ensure latest table exists
		$this->check_db();
	}
	
	/**
	 * Get columns and formats
	 *
	 * @return array
	 */
	public function get_column_format() {
		return array(
			'user_id'         => '%d',
			'ip'              => '%s',
			'title'           => '%s',
			'messages'        => '%s',
			'mode'            => '%s',
			'model'           => '%s',
			'session_id'      => '%s',
			'chat_id'         => '%s',
			'conversation_id' => '%s',
			'row_version'     => '%d',
			'last_idemp_key'  => '%s',
			'widget_id'       => '%d',
			'language'        => '%s',
			'metadata'        => '%s',
			'created'      => '%s',
			'updated'      => '%s'
		);
	}
	
	/**
	 * Get default column values
	 *
	 * @return array
	 */
	public function get_column_defaults() {
		return array(
			'id'              => 0,
			'user_id'         => 0,
			'ip'              => '',
			'title'           => '',
			'messages'        => '[]',
			'mode'            => 'search',
			'model'           => EPKB_OpenAI_Client::DEFAULT_MODEL,
			'session_id'      => '',
			'chat_id'         => '',
			'conversation_id' => '',
			'row_version'     => 1,
			'last_idemp_key'  => '',
			'widget_id'       => 1,
			'language'        => '',
			'metadata'        => '[]',
			'created'      => gmdate( 'Y-m-d H:i:s' ),
			'updated'      => gmdate( 'Y-m-d H:i:s' )
		);
	}
	
	/**
	 * Save conversation
	 *
	 * @param EPKB_AI_Conversation_Model $conversation
	 * @return int|WP_Error Conversation ID or error
	 */
	public function save_conversation( EPKB_AI_Conversation_Model $conversation ) {
		$data = $conversation->to_db_array();
		if ( is_wp_error( $data ) ) {
			return $data;
		}
		
		if ( $conversation->get_id() > 0 ) {
			$result = $this->update_record( $conversation->get_id(), $data );
			$this->handle_db_error( $result, 'update_conversation' );

			if ( ! $result ) {
				return new WP_Error( 'save_failed', __( 'Failed to update conversation', 'echo-knowledge-base' ) );
			}

			return $conversation->get_id();

		} else {

			// Insert new - set model based on mode
			$data['model'] = $data['mode'] === 'chat' ? EPKB_AI_Config_Specs::get_ai_config_value( 'ai_chat_model' ) : EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_model' );
			
			$record_id = $this->insert_record( $data );
			$this->handle_db_error( $record_id, 'insert_conversation' );

			return $record_id;
		}
	}
	
	/**
	 * Get conversation by ID
	 *
	 * @param int $row_id
	 * @return EPKB_AI_Conversation_Model|null|WP_Error
	 */
	public function get_conversation( $row_id ) {

		$row = $this->get_by_primary_key( $row_id );
		$this->handle_db_error( $row, 'get_conversation' );
		if ( is_wp_error( $row ) ) {
			return $row;
		}
		if ( empty( $row ) ) {
			return null;
		}

		

		
		return EPKB_AI_Conversation_Model::from_db_row( $row );
	}
	
	/**
	 * Get conversation by chat ID
	 *
	 * @param string $chat_id
	 * @return EPKB_AI_Conversation_Model|null - null if no conversation found
	 */
	public function get_conversation_by_chat_id( $chat_id ) {

		// new conversation if true
		if ( empty( $chat_id ) ) {
			return null; // Invalid chat ID
		}

		$row = $this->get_a_row_by_column_value( 'chat_id', $chat_id );
		$this->handle_db_error( $row, 'get_conversation_by_chat_id' );
		if ( empty( $row ) ) {
			return null;
		}
		
		return EPKB_AI_Conversation_Model::from_db_row( $row );
	}

	/**
	 * Get the latest active conversation for a session
	 * Active = updated within last 24 hours
	 *
	 * @param string $session_id
	 * @return EPKB_AI_Conversation_Model|null - null if no active conversation found
	 */
	public function get_latest_active_chat_for_session( $session_id ) {
		
		// Define active conversation criteria - only time-based
		$max_age_hours = apply_filters( 'epkb_ai_chat_max_age_hours', 24 );
		$cutoff_time = gmdate( 'Y-m-d H:i:s', strtotime( "-{$max_age_hours} hours" ) );
		
		// Get the latest CHAT conversation for this session within the time window
		// Must filter by mode='chat' to avoid returning search conversations		
		$row = $this->get_a_row_by_where_clause( array( 'session_id' => $session_id, 'mode' => 'chat', 'updated' => array( 'value' => $cutoff_time, 'operator' => '>' ) ) );
		$this->handle_db_error( $row, 'get_latest_active_chat_for_session' );
		if ( is_wp_error( $row ) ) {
			return $row;
		}
		if ( empty( $row ) ) {
			return null;
		}
		
		return EPKB_AI_Conversation_Model::from_db_row( $row );
	}
	
	/**
	 * Get conversation by chat ID and session ID for security
	 *
	 * @param string $chat_id
	 * @param string $session_id
	 * @return EPKB_AI_Conversation_Model|null - null if no conversation found
	 */
	public function get_conversation_by_chat_and_session( $chat_id, $session_id ) {
		
		if ( empty( $chat_id ) || empty( $session_id ) ) {
			return null;
		}
		
		$row = $this->get_a_row_by_where_clause( array( 'chat_id' => $chat_id, 'session_id' => $session_id ) );
		$this->handle_db_error( $row, 'get_conversation_by_chat_and_session' );		
		if ( empty( $row ) ) {
			return null;
		}
		if ( is_wp_error( $row ) ) {
			return $row;
		}
		
		return EPKB_AI_Conversation_Model::from_db_row( $row );
	}
	
	/**
	 * Check if idempotency key already exists for a conversation
	 *
	 * @param string $chat_id
	 * @param string $idempotency_key
	 * @return array|null Returns conversation data if idempotent request found
	 */
	public function check_idempotent_request( $chat_id, $idempotency_key ) {
		
		if ( empty( $chat_id ) || empty( $idempotency_key ) ) {
			return null;
		}
		
		$row = $this->get_a_row_by_where_clause( array( 'chat_id' => $chat_id, 'last_idemp_key' => $idempotency_key ) );
		$this->handle_db_error( $row, 'check_idempotent_request' );
		if ( is_wp_error( $row ) ) {
			return $row;
		}
		if ( empty( $row ) ) {
			return null;
		}
		
		return $row;
	}

	/**
	 * Update conversation with optimistic concurrency control
	 *
	 * @param EPKB_AI_Conversation_Model $conversation
	 * @return bool|WP_Error
	 */
	public function update_chat_with_version_check( $conversation ) {
		global $wpdb;
		
		$data = $conversation->to_db_array();
		if ( is_wp_error( $data ) ) {
			return $data;
		}
		
		$data['row_version'] = $conversation->get_row_version() + 1;
		$data['last_idemp_key'] = $conversation->get_idempotency_key();
		$data['updated'] = gmdate( 'Y-m-d H:i:s' );
		
		// Get column formats
		$column_formats = $this->get_column_format();
		
		// Filter data to only include columns that have formats
		$data = array_intersect_key( $data, $column_formats );
		
		// Reorder column formats to match the order of data keys
		$data_keys = array_keys( $data );
		$column_formats = array_merge( array_flip( $data_keys ), $column_formats );
		
		// Build update query with version check
		$result = $wpdb->update( $this->table_name, $data, array( 'id' => $conversation->get_id(), 'row_version' => $conversation->get_row_version() ),
								$column_formats, array( '%d', '%d' )	);
		$this->handle_db_error( $result, 'update_chat_with_version_check' );

		if ( $result === false ) {
			return new WP_Error( 'db_error', $wpdb->last_error );
		}

		if ( $result === 0 ) {
			// No rows updated - version conflict
			return new WP_Error( 'version_conflict', __( 'Conversation was modified by another request', 'echo-knowledge-base' ) );
		}
		
		return true;
	}

	/**
	 * Insert new conversation with initial messages
	 *
	 * @param array $data Conversation data including messages
	 * @return int|WP_Error Insert ID or error
	 */
	public function insert_chat_with_messages( $data ) {
		// Add metadata
		$data['row_version'] = 1;
		$data['created'] = gmdate( 'Y-m-d H:i:s' );
		$data['updated'] = gmdate( 'Y-m-d H:i:s' );
		// Set model based on mode
		$data['model'] = $data['mode'] === 'chat' ? EPKB_AI_Config_Specs::get_ai_config_value( 'ai_chat_model' ) : EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_model' );
		
		// Ensure messages is JSON encoded
		if ( isset( $data['messages'] ) && is_array( $data['messages'] ) ) {
			$messages_json = wp_json_encode( $data['messages'] );
			if ( $messages_json === false ) {
				return new WP_Error( 'json_encode_error', 'Failed to encode messages: ' . json_last_error_msg() );
			}
			$data['messages'] = $messages_json;
		}
		
		$result = $this->insert_record( $data );

		$this->handle_db_error( $result, 'insert_conversation' );
		if ( is_wp_error( $result ) ) {
			return new WP_Error( 'insert_failed', $result->get_error_message() );
		}
		
		return $result;
	}
	
	/**
	 * Get conversations with pagination
	 *
	 * @param array $args Query arguments
	 * @return array Array of EPKB_Conversation_Model objects
	 */
	public function get_conversations( $args = array() ) {
		$defaults = array(
			'page'      => 1,
			'per_page'  => self::PER_PAGE,
			'mode'      => '',
			'user_id'   => 0,
			'widget_id' => '',
			'language'  => '',
			'orderby'   => 'created',
			'order'     => 'DESC',
			'search'    => '',
			'date_from' => '', // Format: 'Y-m-d H:i:s'
			'date_to'   => ''  // Format: 'Y-m-d H:i:s'
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		// Build WHERE clauses
		$where = array();
		
		if ( ! empty( $args['mode'] ) ) {
			// Special handling for chat mode - include both 'chat' and 'support'
			if ( $args['mode'] === 'chat' ) {
				$where[] = "mode IN ('chat', 'support')";
			} elseif ( $args['mode'] === 'search' ) {
				$where[] = "mode IN ('search', 'advanced_search')";
			} else {
				$where[] = $this->prepare_column_value( 'mode', $args['mode'] );
			}
		}
		
		
		if ( ! empty( $args['user_id'] ) ) {
			$where[] = $this->prepare_column_value( 'user_id', $args['user_id'] );
		}
		
		if ( ! empty( $args['language'] ) ) {
			$where[] = $this->prepare_column_value( 'language', $args['language'] );
		}
		
		if ( ! empty( $args['widget_id'] ) ) {
			$where[] = $this->prepare_column_value( 'widget_id', $args['widget_id'] );
		}
		
		// Add date range filtering
		if ( ! empty( $args['date_from'] ) ) {
			global $wpdb;
			$where[] = $wpdb->prepare( 'created >= %s', $args['date_from'] );
		}
		
		if ( ! empty( $args['date_to'] ) ) {
			global $wpdb;
			$where[] = $wpdb->prepare( 'created <= %s', $args['date_to'] );
		}
		
		// Add search functionality
		if ( ! empty( $args['search'] ) ) {
			global $wpdb;
			$search_term = '%' . $wpdb->esc_like( $args['search'] ) . '%';
			// Search in messages JSON column and user display name
			$where[] = $wpdb->prepare( 
				"(messages LIKE %s OR user_id IN (SELECT ID FROM {$wpdb->users} WHERE display_name LIKE %s))", 
				$search_term, 
				$search_term 
			);
		}
		
		// Calculate offset with overflow protection
		$page = max( 1, absint( $args['page'] ) );
		$per_page = max( 1, min( 100, absint( $args['per_page'] ) ) ); // Limit to 100 per page
		$offset = ( $page - 1 ) * $per_page;
		
		// Get rows
		$rows = $this->get_rows_with_conditions( 
			$where, 
			$args['orderby'], 
			$args['order'], 
			$per_page, 
			$offset 
		);
		
		// Check if we need to create table and retry
		$this->handle_db_error( $rows, 'get_conversations' );
		if ( is_wp_error( $rows ) ) {
			return array();
		}
		
		// Convert to models
		$conversations = array();
		foreach ( $rows as $row ) {
			$conversations[] = EPKB_AI_Conversation_Model::from_db_row( $row );
		}
		
		return $conversations;
	}
	
	/**
	 * Get total count of conversations
	 *
	 * @param array $args Query arguments
	 * @return int
	 */
	public function get_conversations_count( $args = array() ) {
		$where = array();

		if ( ! empty( $args['mode'] ) ) {
			// Special handling for chat mode - include both 'chat' and 'support'
			if ( $args['mode'] === 'chat' ) {
				$where[] = "mode IN ('chat', 'support')";
			} elseif ( $args['mode'] === 'search' ) {
				$where[] = "mode IN ('search', 'advanced_search')";
			} else {
				$where[] = $this->prepare_column_value( 'mode', $args['mode'] );
			}
		}

		if ( ! empty( $args['user_id'] ) ) {
			$where[] = $this->prepare_column_value( 'user_id', $args['user_id'] );
		}
		
		if ( ! empty( $args['widget_id'] ) ) {
			$where[] = $this->prepare_column_value( 'widget_id', $args['widget_id'] );
		}
		
		// Add date range filtering
		if ( ! empty( $args['date_from'] ) ) {
			global $wpdb;
			$where[] = $wpdb->prepare( 'created >= %s', $args['date_from'] );
		}
		
		if ( ! empty( $args['date_to'] ) ) {
			global $wpdb;
			$where[] = $wpdb->prepare( 'created <= %s', $args['date_to'] );
		}
		
		// Add search functionality
		if ( ! empty( $args['search'] ) ) {
			global $wpdb;
			$search_term = '%' . $wpdb->esc_like( $args['search'] ) . '%';
			// Search in messages JSON column and user display name
			$where[] = $wpdb->prepare( 
				"(messages LIKE %s OR user_id IN (SELECT ID FROM {$wpdb->users} WHERE display_name LIKE %s))", 
				$search_term, 
				$search_term 
			);
		}
		
		$count = $this->get_count_with_conditions( $where );

		return $count;
	}
	
	/**
	 * Get conversations from the last N hours
	 *
	 * @param int $hours Number of hours to look back (default: 24)
	 * @param string $mode Conversation mode ('search', 'chat', or empty for all)
	 * @param array $additional_args Additional query arguments
	 * @return array Array of EPKB_Conversation_Model objects
	 */
	public function get_recent_conversations( $hours = 24, $mode = '', $additional_args = array() ) {
		// Calculate date range
		$date_from = gmdate( 'Y-m-d H:i:s', strtotime( "-{$hours} hours" ) );
		$date_to = gmdate( 'Y-m-d H:i:s' );
		
		// Build query arguments
		$args = array(
			'date_from' => $date_from,
			'date_to'   => $date_to,
			'per_page'  => 1000  // Get all conversations in the time period
		);
		
		// Add mode if specified
		if ( ! empty( $mode ) ) {
			$args['mode'] = $mode;
		}
		
		// Merge with any additional arguments
		$args = wp_parse_args( $additional_args, $args );
		
		return $this->get_conversations( $args );
	}
	
	/**
	 * Get count of conversations from the last N hours
	 *
	 * @param int $hours Number of hours to look back (default: 24)
	 * @param string $mode Conversation mode ('search', 'chat', or empty for all)
	 * @param array $additional_args Additional query arguments
	 * @return int
	 */
	public function get_recent_conversations_count( $hours = 24, $mode = '', $additional_args = array() ) {
		// Calculate date range
		$date_from = gmdate( 'Y-m-d H:i:s', strtotime( "-{$hours} hours" ) );
		$date_to = gmdate( 'Y-m-d H:i:s' );
		
		// Build query arguments
		$args = array(
			'date_from' => $date_from,
			'date_to'   => $date_to
		);
		
		// Add mode if specified
		if ( ! empty( $mode ) ) {
			$args['mode'] = $mode;
		}
		
		// Merge with any additional arguments
		$args = wp_parse_args( $additional_args, $args );
		
		return $this->get_conversations_count( $args );
	}
	
	/**
	 * Delete conversation and any potential duplicates
	 * Deletes all records with the same chat_id to ensure consistency
	 *
	 * @param int $id Primary key of the conversation to delete
	 * @return bool True on success, false on failure
	 */
	public function delete_conversation( $id ) {
		global $wpdb;

		// First get the chat_id for this conversation
		$conversation = $this->get_by_primary_key( $id );
		$this->handle_db_error( $conversation, 'delete_conversation_get' );

		if ( empty( $conversation ) || empty( $conversation->chat_id ) ) {
			// If conversation not found or has no chat_id, just delete by ID
			$result = $this->delete_record( $id );
			if ( $this->handle_db_error( $result, 'delete_conversation' ) === 'retry_operation' ) {
				$result = $this->delete_record( $id );
			}
			return $result;
		}

		// Delete all records with this chat_id (handles any potential duplicates)
		$chat_id = $conversation->chat_id;
		$result = $wpdb->query( $wpdb->prepare(
			"DELETE FROM {$this->table_name} WHERE chat_id = %s",
			$chat_id
		) );

		$this->handle_db_error( $result, 'delete_conversation_by_chat_id' );

		if ( $result === false ) {
			return false;
		}

		return true;
	}
	
	/**
	 * Get unique conversation count by mode
	 * Counts distinct chat_ids to ensure each conversation is counted once
	 *
	 * @param string $mode The mode to filter by ('search' or 'chat')
	 * @return int Number of unique conversations with the specified mode
	 */
	public function get_row_count( $mode ) {
		global $wpdb;

		// Special handling for chat mode - include both 'chat' and 'support'
		if ( $mode === 'chat' ) {
			$where = "mode IN ('chat', 'support')";
		} elseif ( $mode === 'search' ) {
			$where = "mode IN ('search', 'advanced_search')";
		} else {
			$where = $wpdb->prepare( 'mode = %s', $mode );
		}

		// Count distinct chat_ids to ensure each conversation is counted once
		$count = $wpdb->get_var( "SELECT COUNT(DISTINCT chat_id) FROM {$this->table_name} WHERE {$where}" );

		$this->handle_db_error( $count, 'get_row_count' );

		return absint( $count );
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

		// Group by chat_id to ensure each conversation appears only once
		// This prevents duplicates in edge cases where multiple records might exist
		$sql .= " GROUP BY chat_id";

		// Validate orderby against allowed columns to prevent SQL injection
		$allowed_columns = array( 'id', 'created', 'updated', 'user_id', 'ip' );
		if ( ! in_array( $orderby, $allowed_columns, true ) ) {
			$orderby = 'created';
		}

		// Validate order direction
		$order = strtoupper( $order );
		if ( ! in_array( $order, array( 'ASC', 'DESC' ), true ) ) {
			$order = 'DESC';
		}

		// Use backticks for column name and validate limit/offset
		$orderby = '`' . $orderby . '`';
		$limit = absint( $limit );
		$offset = absint( $offset );

		$sql .= " ORDER BY $orderby $order";
		$sql .= $wpdb->prepare( " LIMIT %d OFFSET %d", $limit, $offset );

		$results = $wpdb->get_results( $sql );

		$this->handle_db_error( $results, 'get_rows_with_conditions' );

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

		// Use COUNT(DISTINCT chat_id) to ensure we count unique conversations only
		// This matches the GROUP BY logic in get_rows_with_conditions
		$sql = "SELECT COUNT(DISTINCT chat_id) FROM $this->table_name";

		if ( ! empty( $where ) ) {
			$sql .= " WHERE " . implode( ' AND ', $where );
		}

		$count = $wpdb->get_var( $sql );

		$this->handle_db_error( $count, 'get_count_with_conditions' );

		// If there's an error, return 0 to let the caller handle it
		if ( $count === null && ! empty( $wpdb->last_error ) ) {
			return 0;
		}

		return absint( $count );
	}

	/**
	 * Delete old conversations based on retention period
	 *
	 * @param int $retention_days Number of days to keep conversations
	 * @return int|WP_Error Number of conversations deleted or error
	 */
	public function delete_old_conversations( $retention_days = 30 ) {
		global $wpdb;

		// Validate retention days
		$retention_days = absint( $retention_days );
		if ( $retention_days < 1 || $retention_days > 90 ) { // Max 90 days
			$retention_days = 10; // Default to 10 days
		}

		// Calculate cutoff date
		$cutoff_date = gmdate( 'Y-m-d H:i:s', strtotime( "-{$retention_days} days" ) );

		// Delete conversations older than retention period based on last update time
		$deleted = $wpdb->query( $wpdb->prepare( "DELETE FROM {$this->table_name} WHERE updated < %s", $cutoff_date ) );

		$this->handle_db_error( $deleted, 'delete_old_conversations' );
		if ( $deleted === false ) {
			return new WP_Error( 'db_error', __( 'Failed to delete old conversations', 'echo-knowledge-base' ) );
		}

		if ( $deleted > 0 ) {
			EPKB_AI_Log::add_log( "Deleted {$deleted} old conversations older than {$retention_days} days" );
		}
		
		return $deleted;
	}
	
	/**
	 * Get metadata for a conversation
	 *
	 * @param int $conversation_id
	 * @return array Decoded metadata array or empty array
	 */
	public function get_metadata( $conversation_id ) {
		global $wpdb;
		
		$metadata = $wpdb->get_var( $wpdb->prepare( "SELECT metadata FROM {$this->table_name} WHERE id = %d", $conversation_id ) );

		$this->handle_db_error( $metadata, 'get_metadata' );
		if ( empty( $metadata ) ) {
			return array();
		}
		
		$decoded = json_decode( $metadata, true );
		return is_array( $decoded ) ? $decoded : array();
	}
	
	/**
	 * Update metadata for a conversation (merges with existing metadata)
	 *
	 * @param int $conversation_id
	 * @param array $metadata_array New metadata to merge with existing
	 * @return bool|WP_Error
	 */
	public function update_metadata( $conversation_id, $metadata_array ) {
		global $wpdb;

		// Get existing conversation to retrieve current metadata
		$conversation = $this->get_conversation( $conversation_id );
		if ( empty( $conversation ) ) {
			return new WP_Error( 'conversation_not_found', __( 'Conversation not found', 'echo-knowledge-base' ) );
		}

		// Get existing metadata and merge with new metadata (append instead of overwrite)
		$existing_metadata = $conversation->get_metadata();
		if ( ! is_array( $existing_metadata ) ) {
			$existing_metadata = array();
		}
		$merged_metadata = array_merge( $existing_metadata, $metadata_array );

		// Encode merged metadata
		if ( empty( $merged_metadata ) ) {
			$json_metadata = '[]';
		} else {
			$json_metadata = wp_json_encode( $merged_metadata );
			if ( $json_metadata === false ) {
				return new WP_Error( 'invalid_metadata', __( 'Invalid metadata format', 'echo-knowledge-base' ) );
			}
		}

		$result = $wpdb->update( $this->table_name,
			array( 'metadata' => $json_metadata ),
			array( 'id' => $conversation_id ),
			array( '%s' ),
			array( '%d' )
		);

		$this->handle_db_error( $result, 'update_metadata' );
		if ( $result === false ) {
			return new WP_Error( 'update_failed', $wpdb->last_error );
		}

		return true;
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
	 * Check if user has used AI features i.e. the table exists and user has messages
	 *
	 * @return bool True if messages exist, false otherwise
	 */
	public static function has_user_used_ai() {
		global $wpdb;
		
		// Get table name without instantiating (avoids check_db call)
		$table_name = $wpdb->prefix . self::TABLE_NAME_SUFFIX;
		
		// Check if table exists
		if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) !== $table_name ) {
			return false;
		}
		
		// Check if any messages exist
		$message_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name}" );
		
		return intval( $message_count ) > 0;
	}

	/**
	 * Create the table
	 * 
	 * Table columns:
	 * - id: Primary key, auto-incrementing
	 * - user_id: WordPress user ID (null for guests)
	 * - session_id: Browser session (cookie); ties with one or more chat_ids
	 * - chat_id: Hex-UUID for the conversation (unique internal identifier)
	 * - ip: Store SHA-256 hash, not plaintext
	 * - title: Conversation title
	 * - conversation_id: Last OpenAI response.id (may not be unique)
	 * - messages: Ordered history in JSON format. Each message contains attributes: 'role', 'content'
	 * - row_version: Optimistic Concurrency Control (OCC) version counter
	 * - last_idemp_key: Last Idempotency-Key; prevent duplicates on retries
	 * - mode: Operation mode (default: 'search')
	 * - model: Determines which model to use and which provider
	 * - widget_id: Used to differentiate between different chat widgets
	 * - language: Language code
	 * - metadata: JSON field for storing additional data
	 * - created: Timestamp when conversation started
	 * - updated: Timestamp of last update
	 * 
	 * Indexes:
	 * - uniq_chat: 1-row-per-chat guard on chat_id
	 * - Various performance indexes on session_id, chat_id, conversation_id, mode, user_id, updated, widget_id
	 */
	protected function create_table() {
		global $wpdb;

		$collate = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '';

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		/** IMPORTANT: When modifying this table structure, you MUST update TABLE_VERSION constant at the top of this class! **/
		$sql = "CREATE TABLE {$this->table_name} (
				    id               BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				    user_id          BIGINT(20) UNSIGNED NULL,
				    session_id       VARCHAR(64)        NOT NULL,
				    chat_id          CHAR(64)           NOT NULL,
				    ip               VARBINARY(64)      NULL,
				    title            VARCHAR(255)       NULL,
				    conversation_id  VARCHAR(64)        NULL,
				    messages         TEXT               NOT NULL,
				    row_version      INT UNSIGNED       NOT NULL DEFAULT 1,
				    last_idemp_key   CHAR(64)           NULL,
				    mode             VARCHAR(20)        NOT NULL DEFAULT 'search',
				    model            VARCHAR(64)        NULL,
				    widget_id        TINYINT UNSIGNED   NOT NULL DEFAULT 1,
				    language         VARCHAR(20)        NULL,
				    metadata         LONGTEXT           NULL,
				    created          DATETIME           NOT NULL DEFAULT CURRENT_TIMESTAMP,
				    updated          DATETIME           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				    PRIMARY KEY (id),
				    UNIQUE KEY  uniq_chat                    (chat_id),
				    KEY         idx_session_created          (session_id, created),
				    KEY         idx_chat_id                  (chat_id),
				    KEY         idx_conversation_id          (conversation_id),
				    KEY         idx_mode                     (mode),
				    KEY         idx_user_id_created          (user_id, created),
				    KEY         idx_updated                  (updated),
				    KEY         idx_widget_id                (widget_id)
			) $collate;";

		dbDelta( $sql );

		// Only store version if table was actually created successfully
		if ( $this->table_exists( $this->table_name ) ) {
			update_option( $this->get_version_option_name(), self::TABLE_VERSION, true );
		}
	}
}