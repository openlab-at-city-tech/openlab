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

		// Check if we have posts to sync
		if ( empty( $items ) ) {
			return new WP_Error( 'no_posts', __( 'No posts found to sync', 'echo-knowledge-base' ) );
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
			return new WP_Error( 'save_failed', sprintf( __( 'Failed to save sync job: %s', 'echo-knowledge-base' ), $update_result['reason'] ) );
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
			return array( 'status' => 'completed' );
		}
		
		$consecutive_errors = $job['consecutive_errors'];
		$updated_posts = array();
		
		// Process the single post
		$post_id = $remaining_post_ids[0];

		// Sync the post
		$training_data_db = new EPKB_AI_Training_Data_DB();
		$sync_manager = new EPKB_AI_Sync_Manager();
		$job['processed']++;

		try {
			$sync_data = $sync_manager->sync_post( $post_id, $remaining_item['type'], $job['collection_id'] );
		} catch ( Exception $e ) {	
			$error = new WP_Error( 'sync_exception', $e->getMessage() );
			self::handle_sync_error( $training_data_db, $post_id, $error );
			return array( 'status' => 'failed', 'processed' => 0, 'errors' => 1, 'message' => $error->get_error_message(), 'updated_posts' => $updated_posts );
		}

		if ( is_wp_error( $sync_data ) ) {
			self::handle_sync_error( $training_data_db, $post_id, $sync_data );

			$updated_posts[] = array(
				'id' => $post_id, 
				'status' => 'error', 
				'message' => $sync_data->get_error_message()
			);

			self::update_sync_job();

			// Check if we've hit 5 consecutive errors
			if ( $consecutive_errors >= 5 ) {
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

		} else {

			$sync_data = $training_data_db->mark_as_synced( $sync_data['training_data_id'], $sync_data['sync_data'] );
			if ( is_wp_error( $sync_data ) ) {
				return $sync_data;
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

			// Sync completed successfully - return completed status regardless of DB update result
			return array( 'status' => 'completed', 'updated_posts' => $updated_posts );
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
	 * @return void
	 */
	private static function handle_sync_error( $training_data_db, $training_data_id, $wp_error ) {
		$mapped = EPKB_AI_Log::map_error_to_internal_code( $wp_error );
		$error_code = isset( $mapped['code'] ) ? $mapped['code'] : 500;
		$error_message = isset( $mapped['message'] ) ? $mapped['message'] : $wp_error->get_error_message();
		$training_data_db->mark_as_error( $training_data_id, $error_code, $error_message );
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
	
	/**
	 * Get all posts for a collection with their metadata
	 * 
	 * @param int $collection_id Collection ID
	 * @return array Array of items with id and type
	 */
	private static function get_all_posts_for_collection( $collection_id ) {
		
		// Get all items from the training data database for this collection
		// This includes posts, KB files, and any other item types
		$training_data_db = new EPKB_AI_Training_Data_DB();
		$training_items = $training_data_db->get_training_data_by_collection( $collection_id );

		// Extract item IDs and types from the training data
		$items = array();
		foreach ( $training_items as $item ) {
			if ( ! empty( $item->item_id ) ) {
				$items[] = array(
					'id' => $item->item_id,
					'type' => empty( $item->type ) ? 'post' : $item->type
				);
			}
		}
		
		return $items;
	}
}