<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AI Sync Job Manager
 * 
 * Manages sync jobs with unified approach for both direct and cron modes.
 * Both direct and cron sync process one post at a time for consistent behavior.
 * Stores sync state in WordPress option for persistence and single-job enforcement.
 */
class EPKB_AI_Sync_Job_Manager {

	const SYNC_OPTION_NAME = 'epkb_ai_sync_job_status';
	const VERIFY_OPTION_NAME = 'epkb_ai_verify_job_status';
	const CRON_HOOK = 'epkb_do_sync_cron_event';
	const OLD_JOB_THRESHOLD_HOURS = 24; // Jobs older than 1 day are auto-canceled

	/**
	 * Initialize a new sync job
	 *
	 * @param array|string $selected_post_ids Post IDs or 'ALL'
	 * @param string $mode 'direct' or 'cron'
	 * @param int $collection_id Collection ID
	 * @return array|WP_Error
	 */
	public static function initialize_sync_job( $selected_post_ids, $mode, $collection_id ) {

		// Check if there's an active job
		if ( self::is_job_active() ) {
			// If job is older, auto-cancel and proceed
			if ( self::is_job_old() ) {
				self::cancel_all_sync();
				// Continue to start new job below
			} else {
				// Job is recent, ask user to confirm
				return new WP_Error( 'job_active', __( 'A sync job is already running. Do you want to cancel it and start a new sync?', 'echo-knowledge-base' ) );
			}
		}

		// Clear any existing sync job data to ensure we start fresh
		// This prevents old sync records from being processed
		delete_option( self::SYNC_OPTION_NAME );
		
		// Validate collection
		$collection_id = EPKB_AI_Validation::validate_collection_id( $collection_id );
		if ( is_wp_error( $collection_id ) ) {
			return $collection_id;
		}
		
		// Always get all items from collection to have correct types
		$all_items = self::get_all_posts_for_collection( $collection_id );
		
		// Filter items based on selection
		$items = array();
		if ( $selected_post_ids === 'ALL' ) {
			$items = $all_items;
		} elseif ( is_string( $selected_post_ids ) && strpos( $selected_post_ids, 'ALL_' ) === 0 ) {
			// Handle status-filtered "ALL" requests (e.g., "ALL_PENDING", "ALL_ERROR")
			$status_filter = strtolower( substr( $selected_post_ids, 4 ) ); // Extract status after "ALL_"
			
			// Filter items by status
			$training_data_db = new EPKB_AI_Training_Data_DB();
			foreach ( $all_items as $item ) {
				$record = $training_data_db->get_training_data_record_by_item_id( $collection_id, $item['id'] );
				if ( $record && isset( $record->status ) && $record->status === $status_filter ) {
					$items[] = $item;
				}
			}
		} elseif ( is_array( $selected_post_ids ) ) {
			foreach ( $all_items as $item ) {
				if ( in_array( $item['id'], $selected_post_ids ) ) {
					$items[] = $item;
				}
			}
		} else {
			return new WP_Error( 'invalid_post_ids', __( 'Invalid post IDs provided', 'echo-knowledge-base' ) );
		}

		// Check if we have items to sync
		if ( empty( $items ) ) {
			return new WP_Error( 'no_posts', __( 'No syncable items found to sync', 'echo-knowledge-base' ) );
		}

		// Create job data
		$job_data = array_merge( self::get_default_job_data(), array(
			'status' => $mode === 'cron' ? 'scheduled' : 'running',
			'type' => $mode,
			'collection_id' => $collection_id,
			'items' => $items,
			'total' => count( $items )
		) );

		// Save job
		$update_result = self::update_sync_job( $job_data );
		if ( ! $update_result['success'] ) {
			// translators: %s is the failure reason
			$error = new WP_Error( 'save_failed', sprintf( __( 'Failed to save sync job: %s', 'echo-knowledge-base' ), $update_result['reason'] ) );
			EPKB_AI_Log::add_log( $error, array( 'collection_id' => $collection_id ) );
			return $error;
		}

		return $job_data;
	}

	/**
	 * Process next post in the sync queue
	 * 
	 * @return array|WP_Error Result with processed count and status
	 */
	public static function process_next_sync_item() {

		$job = self::get_sync_job();

		// Skip if canceled
		if ( self::is_job_canceled( $job ) ) {
			return array( 'status' => 'idle' );
		}

		// Skip if not running
		if ( $job['status'] !== 'running' ) {
			return array( 'status' => $job['status'] );
		}

		// Get next unprocessed item (always process one at a time)
		$remaining_item = array_slice( $job['items'], $job['processed'], 1 );
		$remaining_item = empty( $remaining_item[0] ) ? null : $remaining_item[0];
		$remaining_post_ids = $remaining_item ? array( $remaining_item['id'] ) : array();

		// check if all done including retries
		if ( empty( $remaining_post_ids ) ) {
			self::update_sync_job( array( 'status' => 'completed', 'percent' => 100 ) );

			$count_check = self::check_sync_count_match( $job['collection_id'] );
			$result = array( 'status' => 'completed' );
			if ( ! empty( $count_check ) ) {
				$result = array_merge( $result, $count_check );
			}

			return $result;
		}
		
		$consecutive_errors = $job['consecutive_errors'];
		$updated_posts = array();
		
		// Process the single post
		$post_id = $remaining_post_ids[0];
		$training_data_id = empty( $remaining_item['training_data_id'] ) ? 0 : absint( $remaining_item['training_data_id'] );

		// Sync the post
		$training_data_db = new EPKB_AI_Training_Data_DB();
		$sync_manager = new EPKB_AI_Sync_Manager();
		$job['processed']++;

		try {
			$sync_data = $sync_manager->sync_post( $post_id, $remaining_item['type'], $job['collection_id'] );
		} catch ( Exception $e ) {
			$error = new WP_Error( 'sync_exception', $e->getMessage() );
			EPKB_AI_Log::add_log( 'Sync exception for item ' . $post_id . ': ' . $e->getMessage() );
			self::handle_sync_error( $training_data_db, $training_data_id, $error );
			return array( 'status' => 'failed', 'processed' => 0, 'errors' => 1, 'message' => $error->get_error_message(), 'updated_posts' => $updated_posts );
		}

		if ( is_wp_error( $sync_data ) ) {
			$error_type = self::handle_sync_error( $training_data_db, $training_data_id, $sync_data );

			// Skipped items (empty content) are not real errors — don't count them
			if ( $error_type === 'skipped' ) {
				$updated_posts[] = array(
					'id' => $post_id,
					'status' => 'skipped',
					'message' => $sync_data->get_error_message()
				);
				$consecutive_errors = 0;
			} else {

			$updated_posts[] = array(
				'id' => $post_id,
				'status' => 'error',
				'message' => $sync_data->get_error_message()
			);

			// Check for fatal errors that should stop sync immediately (e.g., invalid API key, billing issues)
			$error_code = $sync_data->get_error_code();
			if ( in_array( $error_code, array( 'authentication_failed', 'invalid_api_key', 'missing_api_key', 'insufficient_quota' ), true ) ) {
				EPKB_AI_Log::add_log( 'Sync stopped due to fatal error: ' . $sync_data->get_error_message(), array( 'error_code' => $error_code, 'item_id' => $post_id ) );
				self::update_sync_job( array(
					'status' => 'failed',
					'processed' => $job['processed'],
					'errors' => $job['errors'] + 1,
					'percent' => round( ( $job['processed'] / $job['total'] ) * 100 ),
					'error_message' => $sync_data->get_error_message()
				) );

				return array(
					'status' => 'failed',
					'processed' => 0,
					'errors' => 1,
					'message' => $sync_data->get_error_message(),
					'updated_posts' => $updated_posts
				);
			}

			self::update_sync_job();

			// Check if we've hit 5 consecutive errors
			if ( $consecutive_errors >= 5 ) {
				EPKB_AI_Log::add_log( 'Sync stopped after 5 consecutive errors', array( 'item_id' => $post_id, 'last_error' => $sync_data->get_error_message() ) );
				// Update job status and exit sync
				self::update_sync_job( array(
					'status' => 'failed',
					'processed' => $job['processed'],
					'errors' => $job['errors'],
					'percent' => round( ( $job['processed'] / $job['total'] ) * 100 ),
					'consecutive_errors' => $consecutive_errors,
					'error_message' => __( 'Sync stopped after 5 consecutive errors', 'echo-knowledge-base' )
				) );

				return array(
					'status' => 'failed',
					'processed' => 0,
					'errors' => 1,
					'message' => __( 'Sync stopped after 5 consecutive errors', 'echo-knowledge-base' ),
					'updated_posts' => $updated_posts
				);
			}

			} // end real error handling

		} else {

			$new_status = $training_data_db->mark_as_synced( $sync_data['training_data_id'], $sync_data['sync_data'] );
			if ( is_wp_error( $new_status ) ) {
				EPKB_AI_Log::add_log( $new_status, array( 'item_id' => $post_id, 'message' => 'Failed to mark item as synced' ) );
				return $new_status;
			}

			// Reset consecutive errors on success
			$consecutive_errors = 0;

			// Only send minimal data - JavaScript already has title and type from the table
			$post_update_data = array( 
				'id' => $post_id, 
				'status' => 'synced'
			);

			$updated_posts[] = $post_update_data;
		}

		// Update job progress
		$new_processed = $job['processed'];
		$percent = round( ( $new_processed / $job['total'] ) * 100 );
		
		self::update_sync_job( array( 'processed' => $new_processed, 'errors' => $job['errors'], 'percent' => $percent, 'consecutive_errors' => $consecutive_errors ) );
		
		// Check if complete
		if ( $new_processed >= $job['total'] ) {

			self::update_sync_job( array( 'status' => 'completed', 'percent' => 100, 'processed' => $new_processed ) );

			// Verify count match between DB and AI store
			$count_check = self::check_sync_count_match( $job['collection_id'] );

			$result = array( 'status' => 'completed', 'updated_posts' => $updated_posts );
			if ( ! empty( $count_check ) ) {
				$result = array_merge( $result, $count_check );
			}

			return $result;
		}
		
		return array(
			'status' => self::is_job_canceled() ? 'idle' : 'running',
			'processed' => 1,
			'errors' => $job['errors'],
			'updated_posts' => $updated_posts
		);
	}

	/**
	 * Handle sync error
	 *
	 * @param EPKB_AI_Training_Data_DB $training_data_db Training data database object
	 * @param int $training_data_id Training data ID
	 * @param WP_Error $wp_error Error object
	 * @return string 'skipped' for empty content items, 'error' for real errors
	 */
	private static function handle_sync_error( $training_data_db, $training_data_id, $wp_error ) {
		if ( empty( $training_data_id ) ) {
			EPKB_AI_Log::add_log( $wp_error, array( 'message' => 'Sync error for item without training data ID' ) );
			return 'error';
		}

		// For empty content errors, mark as skipped — retrying won't help
		$wp_error_code = $wp_error->get_error_code();
		if ( in_array( $wp_error_code, array( 'empty_markdown', 'empty_content' ), true ) ) {
			$training_data_db->mark_as_skipped( $training_data_id, 500, $wp_error_code );
			EPKB_AI_Log::add_log( $wp_error, array( 'item_id' => $training_data_id, 'message' => 'Sync warning: empty content' ) );
			return 'skipped';
		}

		$mapped = EPKB_AI_Log::map_error_to_internal_code( $wp_error );
		$error_code = isset( $mapped['code'] ) ? $mapped['code'] : 500;
		$error_message = isset( $mapped['message'] ) ? $mapped['message'] : $wp_error->get_error_message();
		$training_data_db->mark_as_error( $training_data_id, $error_code, $error_message );
		EPKB_AI_Log::add_log( $wp_error, array( 'item_id' => $training_data_id, 'message' => 'Sync error for item' ) );
		return 'error';
	}


	/**
	 * Compare DB synced count vs AI store file count after sync completes
	 *
	 * @param int $collection_id Collection ID
	 * @return array Empty if counts match, or array with count_mismatch data
	 */
	private static function check_sync_count_match( $collection_id ) {

		if ( empty( $collection_id ) ) {
			return array();
		}

		$training_data_db = new EPKB_AI_Training_Data_DB();
		$stats = $training_data_db->get_status_statistics( $collection_id );
		$db_synced_count = $stats['synced']; // 'added' + 'updated'

		$vector_store = EPKB_AI_Provider::get_vector_store_handler();
		$store_info = $vector_store->get_vector_store_info_by_collection_id( $collection_id );
		if ( is_wp_error( $store_info ) ) {
			return array();
		}

		$completed = isset( $store_info['file_counts']['completed'] ) ? (int) $store_info['file_counts']['completed'] : 0;
		$in_progress = isset( $store_info['file_counts']['in_progress'] ) ? (int) $store_info['file_counts']['in_progress'] : 0;
		$ai_store_count = $completed + $in_progress;

		if ( $db_synced_count !== $ai_store_count ) {
			return array(
				'count_mismatch' => true,
				'db_synced_count' => $db_synced_count,
				'ai_store_count' => $ai_store_count
			);
		}

		return array();
	}

	/**
	 * Get details about mismatched items between DB and AI store
	 *
	 * @param int $collection_id Collection ID
	 * @return array|WP_Error Mismatch details or error
	 */
	public static function get_mismatch_details( $collection_id ) {

		$collection_id = EPKB_AI_Validation::validate_collection_id( $collection_id );
		if ( is_wp_error( $collection_id ) ) {
			return $collection_id;
		}

		// Get all synced DB items (status 'added' or 'updated')
		$training_data_db = new EPKB_AI_Training_Data_DB();
		$added_items = $training_data_db->get_training_data_by_collection( $collection_id, array( 'status' => 'added' ) );
		$updated_items = $training_data_db->get_training_data_by_collection( $collection_id, array( 'status' => 'updated' ) );
		$synced_items = array_merge( $added_items, $updated_items );

		// Get vector store ID for this collection
		$collection_config = EPKB_AI_Training_Data_Config_Specs::get_training_data_collection( $collection_id );
		if ( is_wp_error( $collection_config ) ) {
			return $collection_config;
		}

		$store_id = isset( $collection_config['ai_training_data_store_id'] ) ? $collection_config['ai_training_data_store_id'] : '';
		if ( empty( $store_id ) ) {
			return new WP_Error( 'no_store', __( 'No AI store found for this collection', 'echo-knowledge-base' ) );
		}

		// Get all file IDs from the AI store
		$vector_store = EPKB_AI_Provider::get_vector_store_handler();
		$ai_file_ids = $vector_store->list_vector_store_file_ids( $store_id );
		if ( is_wp_error( $ai_file_ids ) ) {
			return $ai_file_ids;
		}

		$ai_file_ids_set = array_flip( $ai_file_ids );

		// Build map of DB file_id => item data
		$db_file_ids = array();
		$missing_from_ai_store = array();
		foreach ( $synced_items as $item ) {
			$file_id = isset( $item->file_id ) ? $item->file_id : '';
			if ( ! empty( $file_id ) ) {
				$db_file_ids[ $file_id ] = true;
			}

			// Check if this DB item's file_id exists in AI store
			if ( empty( $file_id ) || ! isset( $ai_file_ids_set[ $file_id ] ) ) {
				if ( count( $missing_from_ai_store ) < 20 ) {
					$missing_from_ai_store[] = array(
						'item_id' => isset( $item->item_id ) ? $item->item_id : '',
						'title'   => isset( $item->title ) ? $item->title : '',
						'url'     => isset( $item->url ) ? $item->url : '',
						'type'    => isset( $item->type ) ? $item->type : '',
					);
				}
			}
		}

		// Count AI store files not in DB
		$missing_from_db_count = 0;
		foreach ( $ai_file_ids as $ai_file_id ) {
			if ( ! isset( $db_file_ids[ $ai_file_id ] ) ) {
				$missing_from_db_count++;
			}
		}

		return array(
			'missing_from_ai_store' => $missing_from_ai_store,
			'missing_from_db_count' => $missing_from_db_count,
			'db_synced_count'       => count( $synced_items ),
			'ai_store_count'        => count( $ai_file_ids ),
		);
	}

	/***********************************************************************************************
	 *      Sync Job Data Management
	 * *********************************************************************************************/

	private static function get_default_job_data() {
		return array(
			'status' => 'idle',	// idle, scheduled (cron), running (direct), completed, failed
			'type' => '',
			'collection_id' => 0,
			'items' => array(),
			'retry_post_ids' => array(),
			'retrying' => false,
			'cancel_requested' => false,
			'processed' => 0,
			'total' => 0,
			'percent' => 0,
			'errors' => 0,
			'consecutive_errors' => 0,
			'start_time' => gmdate( 'Y-m-d H:i:s' ),
			'last_update' => ''
		);
	}

	/**
	 * Get current sync job status
	 *
	 * @return array Sync job data or default values
	 */
	public static function get_sync_job() {

		$default = self::get_default_job_data();
		$job = get_option( self::SYNC_OPTION_NAME, $default );

		return wp_parse_args( $job, $default );
	}

	/**
	 * Update sync job status
	 *
	 * @param array $data Data to update
	 * @return array Array with 'success' (bool) and 'reason' (string: 'job_canceled', 'no_change', 'updated', 'update_failed')
	 */
	public static function update_sync_job( $data=array() ) {

		$job = self::get_sync_job();
		if ( self::is_job_canceled( $job ) ) {
			return array( 'success' => false, 'reason' => 'job_canceled' );
		}

		$updated_job = array_merge( $job, $data );
		$updated_job['last_update'] = gmdate( 'Y-m-d H:i:s' );

		// Get current option value to detect if it's the same
		$current_option = get_option( self::SYNC_OPTION_NAME );

		// If values are identical, treat as success (no change needed)
		if ( $current_option !== false && $current_option === $updated_job ) {
			return array( 'success' => true, 'reason' => 'no_change' );
		}

		$result = update_option( self::SYNC_OPTION_NAME, $updated_job, false );
		if ( $result === false ) {
			return array( 'success' => false, 'reason' => 'update_failed' );
		}

		return array( 'success' => true, 'reason' => 'updated' );
	}

	/**
	 * Check if a job is active
	 *
	 * @return bool
	 */
	public static function is_job_active() {
		$job = self::get_sync_job();
		return in_array( $job['status'], array( 'scheduled', 'running' ) );
	}

	/**
	 * Check if an active job is older than 1 day
	 *
	 * @return bool
	 */
	private static function is_job_old() {
		$job = self::get_sync_job();

		// Only check for active jobs
		if ( ! in_array( $job['status'], array( 'scheduled', 'running' ) ) ) {
			return false;
		}

		// Check if last_update is older than 1 day
		if ( empty( $job['last_update'] ) ) {
			return false;
		}

		// Parse timestamp as UTC since gmdate stores in UTC
		$last_update = strtotime( $job['last_update'] . ' UTC' );
		$threshold_seconds = self::OLD_JOB_THRESHOLD_HOURS * 3600;

		return ( time() - $last_update ) > $threshold_seconds;
	}

	private static function is_job_canceled( $job = null ) {

		if ( empty( $job ) ) {
			$job = self::get_sync_job();
		}

		return ! empty( $job['cancel_requested'] );
	}

	/**
	 * Cancel all sync operations
	 *
	 * @return bool Success
	 */
	public static function cancel_all_sync() {

		// Mark cancel requested and set to idle (align with sync semantics)
		self::update_sync_job( array(
			'status' => 'idle',
			'cancel_requested' => true,
		) );
		
		// Clear scheduled cron event if exists
		wp_clear_scheduled_hook( self::CRON_HOOK );
		
		// Don't delete the cancel flag here - let it persist until a new sync starts
		// This prevents race conditions where a running process might not see the cancel
		
		return true;
	}
	
	/***********************************************************************************************
	 *      Verify & Fix Job
	 * *********************************************************************************************/

	/**
	 * Initialize a verify job for synced items in a collection
	 *
	 * @param int $collection_id Collection ID
	 * @return array|WP_Error
	 */
	public static function initialize_verify_job( $collection_id ) {

		// Check if there's an active verify job
		$existing = self::get_verify_job();
		if ( $existing['status'] === 'running' ) {
			return new WP_Error( 'verify_active', __( 'A verify job is already running.', 'echo-knowledge-base' ) );
		}

		// Clear any existing verify job
		delete_option( self::VERIFY_OPTION_NAME );

		$collection_id = EPKB_AI_Validation::validate_collection_id( $collection_id );
		if ( is_wp_error( $collection_id ) ) {
			return $collection_id;
		}

		// Get all synced items (status 'added' or 'updated')
		$training_data_db = new EPKB_AI_Training_Data_DB();
		$added_items = $training_data_db->get_training_data_by_collection( $collection_id, array( 'status' => 'added' ) );
		$updated_items = $training_data_db->get_training_data_by_collection( $collection_id, array( 'status' => 'updated' ) );
		$synced_items = array_merge( $added_items, $updated_items );

		$items = array();
		foreach ( $synced_items as $item ) {
			$items[] = array(
				'id' => $item->item_id,
				'type' => empty( $item->type ) ? 'post' : $item->type
			);
		}

		$collection_config = EPKB_AI_Training_Data_Config_Specs::get_training_data_collection( $collection_id );
		if ( is_wp_error( $collection_config ) ) {
			return $collection_config;
		}

		$vector_store_id = isset( $collection_config['ai_training_data_store_id'] ) ? (string) $collection_config['ai_training_data_store_id'] : '';
		if ( empty( $items ) && $vector_store_id === '' ) {
			return new WP_Error( 'no_items', __( 'No synced items found to verify', 'echo-knowledge-base' ) );
		}

		$job_data = array(
			'status' => 'running',
			'collection_id' => $collection_id,
			'items' => $items,
			'total' => count( $items ),
			'processed' => 0,
			'percent' => 0,
			'verified_ok' => 0,
			'marked_outdated' => 0,
			'removed_orphan_files' => 0,
			'removed_orphan_posts' => 0,
			'removed_orphan_pdfs' => 0,
			'removed_orphan_notes' => 0,
			'errors' => 0,
			'start_time' => gmdate( 'Y-m-d H:i:s' ),
			'last_update' => gmdate( 'Y-m-d H:i:s' ),
			'cancel_requested' => false
		);

		update_option( self::VERIFY_OPTION_NAME, $job_data, false );

		return $job_data;
	}

	/**
	 * Process next item in the verify queue
	 *
	 * @return array Result with status and counts
	 */
	public static function process_next_verify_item() {

		$job = self::get_verify_job();

		if ( ! empty( $job['cancel_requested'] ) ) {
			return array( 'status' => 'idle' );
		}

		if ( $job['status'] !== 'running' ) {
			return array( 'status' => $job['status'] );
		}

		// Get next unprocessed item
		$item = isset( $job['items'][ $job['processed'] ] ) ? $job['items'][ $job['processed'] ] : null;

		// Verify the item
		$sync_manager = new EPKB_AI_Sync_Manager();
		if ( empty( $item ) ) {
			return self::complete_verify_job( $job, $sync_manager );
		}

		try {
			$result = $sync_manager->verify_item( $item['id'], $job['collection_id'] );
		} catch ( Exception $e ) {
			EPKB_AI_Log::add_log( 'Verify exception for item ' . $item['id'] . ': ' . $e->getMessage() );
			$result = array( 'status' => 'error', 'message' => $e->getMessage() );
		}

		$updated_post = array( 'id' => $item['id'] );

		// Update counters
		switch ( $result['status'] ) {
			case 'ok':
				$job['verified_ok']++;
				$updated_post['status'] = 'verified_ok';
				break;
			case 'outdated':
				$job['marked_outdated']++;
				$updated_post['status'] = 'outdated';
				$updated_post['message'] = isset( $result['message'] ) ? $result['message'] : '';
				break;
			case 'error':
				$job['errors']++;
				$updated_post['status'] = 'verify_error';
				$updated_post['message'] = isset( $result['message'] ) ? $result['message'] : '';
				EPKB_AI_Log::add_log( 'Verify error for item ' . $item['id'] . ': ' . $updated_post['message'] );
				break;
			default: // skipped
				$updated_post['status'] = 'skipped';
				break;
		}

		$job['processed']++;
		$job['percent'] = round( ( $job['processed'] / $job['total'] ) * 100 );
		$job['last_update'] = gmdate( 'Y-m-d H:i:s' );

		// Check if complete
		if ( $job['processed'] >= $job['total'] ) {
			return self::complete_verify_job( $job, $sync_manager, $updated_post );
		}

		update_option( self::VERIFY_OPTION_NAME, $job, false );

		$response = array(
			'status' => $job['status'],
			'processed' => $job['processed'],
			'total' => $job['total'],
			'percent' => $job['percent'],
			'verified_ok' => $job['verified_ok'],
			'marked_outdated' => $job['marked_outdated'],
			'removed_orphan_files' => $job['removed_orphan_files'],
			'removed_orphan_posts' => $job['removed_orphan_posts'],
			'removed_orphan_pdfs' => $job['removed_orphan_pdfs'],
			'removed_orphan_notes' => $job['removed_orphan_notes'],
			'errors' => $job['errors'],
			'updated_post' => $updated_post
		);

		return $response;
	}

	/**
	 * Get current verify job status
	 *
	 * @return array
	 */
	public static function get_verify_job() {

		$default = array(
			'status' => 'idle',
			'collection_id' => 0,
			'items' => array(),
			'total' => 0,
			'processed' => 0,
			'percent' => 0,
			'verified_ok' => 0,
			'marked_outdated' => 0,
			'removed_orphan_files' => 0,
			'removed_orphan_posts' => 0,
			'removed_orphan_pdfs' => 0,
			'removed_orphan_notes' => 0,
			'errors' => 0,
			'start_time' => '',
			'last_update' => '',
			'cancel_requested' => false
		);

		$job = get_option( self::VERIFY_OPTION_NAME, $default );

		return wp_parse_args( $job, $default );
	}

	/**
	 * Cancel verify job
	 *
	 * @return bool
	 */
	public static function cancel_verify_job() {

		$job = self::get_verify_job();
		$job['status'] = 'idle';
		$job['cancel_requested'] = true;
		$job['last_update'] = gmdate( 'Y-m-d H:i:s' );
		update_option( self::VERIFY_OPTION_NAME, $job, false );

		return true;
	}

	/**
	 * Complete verify job and perform orphaned vector store cleanup.
	 *
	 * @param array $job Verify job data.
	 * @param EPKB_AI_Sync_Manager $sync_manager Sync manager instance.
	 * @param array|null $updated_post Last updated post payload.
	 * @return array
	 */
	private static function complete_verify_job( $job, $sync_manager, $updated_post = null ) {

		$cleanup_result = $sync_manager->cleanup_orphaned_vector_store_files( $job['collection_id'] );
		if ( isset( $cleanup_result['status'] ) && $cleanup_result['status'] === 'error' ) {
			$job['errors']++;
			EPKB_AI_Log::add_log( 'Validate & Fix orphan cleanup failed: ' . $cleanup_result['message'], array( 'collection_id' => $job['collection_id'] ) );
		} else {
			$job['removed_orphan_files'] = isset( $cleanup_result['removed_orphan_files'] ) ? (int) $cleanup_result['removed_orphan_files'] : 0;
			$job['removed_orphan_posts'] = isset( $cleanup_result['removed_orphan_posts'] ) ? (int) $cleanup_result['removed_orphan_posts'] : 0;
			$job['removed_orphan_pdfs'] = isset( $cleanup_result['removed_orphan_pdfs'] ) ? (int) $cleanup_result['removed_orphan_pdfs'] : 0;
			$job['removed_orphan_notes'] = isset( $cleanup_result['removed_orphan_notes'] ) ? (int) $cleanup_result['removed_orphan_notes'] : 0;
			if ( ! empty( $cleanup_result['errors'] ) ) {
				$job['errors'] += (int) $cleanup_result['errors'];
			}
		}

		$job['status'] = 'completed';
		$job['percent'] = 100;
		$job['last_update'] = gmdate( 'Y-m-d H:i:s' );

		update_option( self::VERIFY_OPTION_NAME, $job, false );

		EPKB_AI_Log::add_log(
			'Validate & Fix completed: ' .
			$job['verified_ok'] . ' OK, ' .
			$job['marked_outdated'] . ' marked outdated, ' .
			$job['removed_orphan_files'] . ' orphan files removed from vector store (' .
			$job['removed_orphan_posts'] . ' WordPress items, ' .
			$job['removed_orphan_pdfs'] . ' PDFs, ' .
			$job['removed_orphan_notes'] . ' AI Notes), ' .
			$job['errors'] . ' errors (total: ' . $job['total'] . ')'
		);

		return array(
			'status' => 'completed',
			'processed' => $job['processed'],
			'total' => $job['total'],
			'percent' => $job['percent'],
			'verified_ok' => $job['verified_ok'],
			'marked_outdated' => $job['marked_outdated'],
			'removed_orphan_files' => $job['removed_orphan_files'],
			'removed_orphan_posts' => $job['removed_orphan_posts'],
			'removed_orphan_pdfs' => $job['removed_orphan_pdfs'],
			'removed_orphan_notes' => $job['removed_orphan_notes'],
			'errors' => $job['errors'],
			'updated_post' => $updated_post
		);
	}

	/**
	 * Get all posts for a collection with their metadata
	 *
	 * @param int $collection_id Collection ID
	 * @return array Array of items with item ID, training data ID, and type
	 */
	private static function get_all_posts_for_collection( $collection_id ) {
		
		// Get all items from the training data database for this collection.
		// Uploaded PDF/HTML files are excluded because they are synced at upload time, not through this sync flow.
		$training_data_db = new EPKB_AI_Training_Data_DB();
		$training_items = $training_data_db->get_training_data_by_collection( $collection_id );

		// Extract item IDs and types from the training data
		$items = array();
		foreach ( $training_items as $item ) {
			if ( empty( $item->item_id ) || in_array( $item->type, array( 'PDF', 'HTML' ), true ) ) {
				continue;
			}

			$items[] = array(
				'id' => $item->item_id,
				'training_data_id' => (int) $item->id,
				'type' => empty( $item->type ) ? 'post' : $item->type
			);
		}
		
		return $items;
	}
}
